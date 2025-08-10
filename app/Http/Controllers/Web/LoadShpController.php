<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\GeoFeatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Shapefile\ShapefileReader;
use Shapefile\ShapefileException;
use ZipArchive;
use Illuminate\Support\Facades\DB;
use App\Models\WilayahSungai;

class LoadShpController extends Controller
{
    protected $geoFeatureService;

    public function __construct(GeoFeatureService $geoFeatureService)
    {
        $this->geoFeatureService = $geoFeatureService;
    }

    public function index(Request $request)
    {
        $data = [];
        if (isset($request->type)) {
            $data = ['type' => $request->type];
        }
        return view('admin.pages.loadshp.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shp_file' => 'required|file|mimes:zip',
        ]);

        try {
            $file = $request->file('shp_file');

            if (!$file->isValid()) {
                return response()->json(['success' => false, 'message' => 'File upload tidak valid.'], 400);
            }

            $timestamp = time();
            $originalName = $file->getClientOriginalName();
            $tmpPath = "/tmp/{$timestamp}_{$originalName}";
            $file->move('/tmp', "{$timestamp}_{$originalName}");

            Log::info("Uploaded file saved to: {$tmpPath}");

            $extractPath = "/tmp/extracted_{$timestamp}";
            File::makeDirectory($extractPath, 0755, true);
            $zip = new \ZipArchive;
            $res = $zip->open($tmpPath);
            if ($res !== true) {
                return response()->json(['success' => false, 'message' => "Gagal membuka ZIP (kode $res)"], 400);
            }
            $zip->extractTo($extractPath);
            $zip->close();

            Log::info("ZIP berhasil diekstrak ke: {$extractPath}");

            $shpFilePath = $this->findShpInDir($extractPath);
            if (!$shpFilePath || !file_exists($shpFilePath)) {
                return response()->json(['success' => false, 'message' => 'File .shp tidak ditemukan di dalam ZIP.'], 400);
            }

            $geoJson = $this->convertShpToGeoJson($shpFilePath);

            File::delete($tmpPath);
            if (File::isDirectory($extractPath)) {
                File::deleteDirectory($extractPath);
            }

            return response()->json(['success' => true, 'geojson' => $geoJson]);

        } catch (\Exception $e) {
            Log::error("Error proses shapefile: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function findShpInDir(string $dir): ?string
    {
        $allFiles = File::allFiles($dir);
        foreach ($allFiles as $f) {
            if (strtolower($f->getExtension()) === 'shp') {
                return $f->getPathname();
            }
        }
        return null;
    }

    private function convertShpToGeoJson(string $shpPath): array
    {
        try {
            $reader = new ShapefileReader($shpPath);
            $features = [];

            while ($rec = $reader->fetchRecord()) {
                if ($rec->isDeleted()) continue;

                $geometry = json_decode($rec->getGeoJSON(), true);

                // Deteksi dan konversi jika koordinat dalam EPSG:3857
                if ($this->isLikelyMercator($geometry)) {
                    $geometry['coordinates'] = $this->convertCoordinatesToWGS84($geometry['coordinates']);
                }

                $features[] = [
                    'type' => 'Feature',
                    'geometry' => $geometry,
                    'properties' => $rec->getDataArray(),
                ];
            }

            return ['type' => 'FeatureCollection', 'features' => $features];
        } catch (ShapefileException $e) {
            throw new \Exception('Error reading SHP: ' . $e->getMessage());
        }
    }


    public function saveGeo(Request $request)
    {
        $tabel = $request->pos_type;

        switch ($tabel) {
            case 'Pos Pantau':
                return $this->savePosPantau($request);
            case 'Wilayah Sungai' :
                return $this->saveWilayahSungai($request);
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak dapat diproses.'
                ], 400);
        }
    }

    private function saveGeoJsonFeatureToDatabase(array $feature): string
    {
        $signature = null;
        $existFeature = $this->geoFeatureService->exists($feature);
        if ($existFeature) {
            $signature = $existFeature->signature;
        } else {
            $newFeature = $this->geoFeatureService->createFromGeoJson($feature);
            if ($newFeature) {
                $signature = $newFeature->signature;
            }
        }

        return $signature;
    }

    private function savePosPantau(Request $request)
    {
        $features = json_decode($request->input('visibleFeatures'), true);
        $mapped = $request->input('mapped', []);

        if (!$features || !is_array($features)) {
            return response()->json([
                'success' => false,
                'message' => 'Data fitur tidak valid.'
            ], 400);
        }
        $datas=[];
        try {
            DB::transaction(function () use ($features, $mapped, $request) {
                foreach ($features as $feature) {
                    $data = [];

                    $coordinates = $feature['geometry']['coordinates'] ?? [null, null];
                    $data['longitude'] = $coordinates[0];
                    $data['latitude'] = $coordinates[1];
                    $data['jenis_pos'] = $request->input('jenis_pos');
                    $data['instansi_id'] = $request->input('instansi_id');
                    $data['kewenangan'] = $request->input('kewenangan');
                    
                    $kabupaten= $this->geoFeatureService->findFeatureContainingPoint( $coordinates[0], $coordinates[1], 'kecamatan');
                    if(!empty($kabupaten)){
                        $properties = json_decode($kabupaten->properties, true);
                        // $data['desa'] = $properties['WADMKD'];
                        $data['kabupaten_id'] = $properties['KDWKB'];
                        $data['kecamatan_id'] = $properties['KDWKC'];
                    }
                    $ws= $this->geoFeatureService->findFeatureContainingPoint( $coordinates[0], $coordinates[1], 'Wilayah Sungai');
                    if (!empty($ws)) {

                        $ws_data = WilayahSungai::where('signature', $ws->signature)->first();

                        $data['ws_id'] = $ws_data->id;
                    }

                    foreach ($mapped as $field => $mappingOptions) {
                        $mappedKey = $mappingOptions[0] ?? null;

                        if ($mappedKey && str_starts_with($mappedKey, 'properties.') && isset($feature['properties'])) {
                            $propName = str_replace('properties.', '', $mappedKey);
                            $data[$field] = $feature['properties'][$propName] ?? null;
                        }
                    }

                    // Mempersiapkan data untuk disimpan sebagai GeoFeature
                    // Strukturnya harus cocok dengan yang diharapkan oleh GeoFeatureService
                    $featureToSave = [
                        'type' => 'Feature',
                        'geometry' => $feature['geometry'],
                        'properties' => [
                            // 'name' dan 'tag' berada di level atas 'properties'
                            'name' => $data['nama_pos'] ?? null,
                            'tag' => $request->input('jenis_pos'),
                            // 'properties' asli dari shapefile di-nest di dalam 'properties'
                            'properties' => $feature['properties']
                        ]
                    ];
                    $signature = $this->saveGeoJsonFeatureToDatabase($featureToSave);
                    if ($signature) {
                        $data['geo_feature_signature'] = $signature;
                        $datas[] = $data;
                        Log::info(json_encode($data));
                        \App\Models\PosPantau::create($data);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => count($features) . ' data berhasil disimpan.',
                'datas'    => $datas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function saveWilayahSungai(Request $request) {
        
        $features = json_decode($request->input('visibleFeatures'), true);
        $mapped = $request->input('mapped', []);

        if (!$features || !is_array($features)) {
            return response()->json([
                'success' => false,
                'message' => 'Data fitur tidak valid.'
            ], 400);
        }
        $datas=[];
        try {
            DB::transaction(function () use ($features, $mapped, $request) {
                foreach ($features as $feature) {
                    $data = [];

                    $coordinates = $feature['geometry']['coordinates'] ?? [null, null];

                    foreach ($mapped as $field => $mappingOptions) {
                        $mappedKey = $mappingOptions[0] ?? null;

                        if ($mappedKey && str_starts_with($mappedKey, 'properties.') && isset($feature['properties'])) {
                            $propName = str_replace('properties.', '', $mappedKey);
                            $data[$field] = $feature['properties'][$propName] ?? null;
                        }
                    }

                    // Mempersiapkan data untuk disimpan sebagai GeoFeature
                    // Strukturnya harus cocok dengan yang diharapkan oleh GeoFeatureService
                    $featureToSave = [
                        'type' => 'Feature',
                        'geometry' => $feature['geometry'],
                        'properties' => [
                            // 'name' dan 'tag' berada di level atas 'properties'
                            'name' => $data['name'] ?? null,
                            'tag' => 'Wilayah Sungai',
                            // 'properties' asli dari shapefile di-nest di dalam 'properties'
                            'properties' => $feature['properties']
                        ]
                    ];
                    $signature = $this->saveGeoJsonFeatureToDatabase($featureToSave);
                    if ($signature) {
                        $data['signature'] = $signature;
                        $data['instansi_id'] = $request->input('instansi_id');
                        Log::info(json_encode($data));
                        \App\Models\WilayahSungai::create($data);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => count($features) . ' data berhasil disimpan.',
                'datas'    => $datas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function isLikelyMercator(array $geometry): bool
    {
        $coords = $this->extractFirstCoordinate($geometry['coordinates'] ?? []);
        return isset($coords[0], $coords[1]) && (abs($coords[0]) > 180 || abs($coords[1]) > 90);
    }

    private function extractFirstCoordinate($coords)
    {
        while (is_array($coords) && isset($coords[0]) && is_array($coords[0])) {
            $coords = $coords[0];
        }
        return $coords;
    }

    private function convertCoordinatesToWGS84($coords)
    {
        if (!is_array($coords[0])) {
            return $this->mercatorToLatLng($coords[0], $coords[1]);
        }

        return array_map(function ($c) {
            return $this->convertCoordinatesToWGS84($c);
        }, $coords);
    }

    private function mercatorToLatLng($x, $y): array
    {
        $R = 6378137.0;
        $lng = ($x / $R) * (180 / pi());
        $lat = rad2deg(atan(sinh($y / $R)));
        return [$lng, $lat];
    }

}
