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
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => json_decode($rec->getGeoJSON()),
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
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak dapat diproses.'
                ], 400);
        }
    }

    private function saveGeoJsonFeatureToDatabase(array $feature): string
    {
        if ($this->geoFeatureService->exists($feature)) {
            $geometry = json_encode($feature['geometry']);
            $propertiesArray = $feature['properties']['properties'] ?? [];
            $signatureData = $geometry . json_encode($propertiesArray, JSON_UNESCAPED_UNICODE);
            return hash('sha256', $signatureData);
        }

        $newFeature = $this->geoFeatureService->createFromGeoJson($feature);
        return $newFeature->signature;
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

        try {
            DB::transaction(function () use ($features, $mapped, $request) {
                foreach ($features as $feature) {
                    $data = [];

                    $coordinates = $feature['geometry']['coordinates'] ?? [null, null];
                    $data['longitude'] = $coordinates[0];
                    $data['latitude'] = $coordinates[1];
                    $data['jenis_pos'] = $request->input('jenis_pos');
                    $data['kewenangan'] = $request->input('kewenangan');

                    $desa= $this->geoFeatureService->findFeatureContainingPoint( $coordinates[0], $coordinates[1]);
                    if(!empty($desa)){
                        $properties = json_decode($desa->properties, true);
                        $data['desa'] = $properties['WADMKD'];
                        $data['kecamatan'] = $properties['WADMKC'];
                        $data['kabupaten'] = $properties['WADMKK'];
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
                    $data['geo_feature_signature'] = $signature;

                    \App\Models\PosPantau::create($data);
                }
            });

            return response()->json([
                'success' => true,
                'message' => count($features) . ' data berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
