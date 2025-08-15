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
use App\Models\Sungai;

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

            $shpFiles = $this->findShpInDir($extractPath);

            if (empty($shpFiles)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada file .shp ditemukan di dalam ZIP.'], 400);
            }

            $allFeatures = [];

            foreach ($shpFiles as $shpFilePath) {
                try {
                    $geoJson = $this->convertShpToGeoJson($shpFilePath);

                    // Gabungkan semua fitur dari setiap file
                    if (isset($geoJson['features'])) {
                        $allFeatures = array_merge($allFeatures, $geoJson['features']);
                    }
                } catch (\Exception $e) {
                    Log::error("Gagal mengonversi file {$shpFilePath}: {$e->getMessage()}");
                    return response()->json(['success' => false, 'message' => "Gagal mengonversi file {$shpFilePath}."], 500);
                }
            }

            return response()->json([
                'success' => true,
                'geojson' => [
                    'type' => 'FeatureCollection',
                    'features' => $allFeatures
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error proses shapefile: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function findShpInDir(string $dir): array
    {
        $allFiles = File::allFiles($dir);
        $shpFiles = [];

        foreach ($allFiles as $f) {
            if (strtolower($f->getExtension()) === 'shp') {
                $shpFiles[] = $f->getPathname(); // Menambahkan file .shp ke array
            }
        }

        return $shpFiles;
    }

    private function convertShpToGeoJson(string $shpPath): array
    {
        try {
            $reader = new ShapefileReader($shpPath);
            $features = [];

            while ($rec = $reader->fetchRecord()) {
                if ($rec->isDeleted()) continue;

                $geoJsonStr = $rec->getGeoJSON();
                if (!$geoJsonStr || strtolower($geoJsonStr) === 'null') continue;

                $geometry = json_decode($geoJsonStr, true);

                // Skip jika geometry tidak valid
                if (
                    !$geometry ||
                    !isset($geometry['type']) ||
                    !isset($geometry['coordinates']) ||
                    !is_array($geometry['coordinates']) ||
                    empty($geometry['coordinates'])
                ) {
                    continue;
                }

                // Konversi koordinat jika perlu
                if ($this->isLikelyMercator($geometry)) {
                    $geometry['coordinates'] = $this->convertCoordinatesToWGS84($geometry['coordinates']);
                }

                // Bersihkan tipe geometry yang pakai "M" di akhir
                if (isset($geometry['type']) && preg_match('/M$/i', $geometry['type'])) {
                    $geometry['type'] = preg_replace('/M$/i', '', $geometry['type']);
                }

                // Hapus bbox invalid
                if (isset($geometry['bbox']) && (!is_array($geometry['bbox']) || array_filter($geometry['bbox'], fn($v) => !is_numeric($v)))) {
                    unset($geometry['bbox']);
                }

                // Ambil properties asli dari shapefile
                $properties = $rec->getDataArray();

                // === Perhitungan Luas, Keliling, Panjang ===
                if ($geometry['type'] === 'Polygon') {
                    $area_m2 = $this->calculatePolygonArea($geometry['coordinates']);
                    $perimeter_m = $this->calculatePolygonPerimeter($geometry['coordinates']);

                    $properties['area_m2'] = $area_m2;
                    $properties['area_ha'] = $area_m2 / 10000; // hektar
                    $properties['area_km2'] = $area_m2 / 1_000_000; // km²
                    $properties['area_acre'] = $area_m2 / 4046.8564224; // acre

                    $properties['perimeter_m'] = $perimeter_m;
                    $properties['perimeter_km'] = $perimeter_m / 1000; // km
                    $properties['perimeter_miles'] = $perimeter_m / 1609.344; // mil
                } elseif ($geometry['type'] === 'MultiPolygon') {
                    $totalArea = 0;
                    $totalPerimeter = 0;
                    foreach ($geometry['coordinates'] as $polygon) {
                        $totalArea += $this->calculatePolygonArea($polygon);
                        $totalPerimeter += $this->calculatePolygonPerimeter($polygon);
                    }

                    $properties['area_m2'] = $totalArea;
                    $properties['area_ha'] = $totalArea / 10000;
                    $properties['area_km2'] = $totalArea / 1_000_000;
                    $properties['area_acre'] = $totalArea / 4046.8564224;

                    $properties['perimeter_m'] = $totalPerimeter;
                    $properties['perimeter_km'] = $totalPerimeter / 1000;
                    $properties['perimeter_miles'] = $totalPerimeter / 1609.344;
                } elseif ($geometry['type'] === 'LineString') {
                    $length_m = $this->calculateLineLength($geometry['coordinates']);

                    $properties['length_m'] = $length_m;
                    $properties['length_km'] = $length_m / 1000;
                    $properties['length_miles'] = $length_m / 1609.344;
                } elseif ($geometry['type'] === 'MultiLineString') {
                    $totalLength = 0;
                    foreach ($geometry['coordinates'] as $line) {
                        $totalLength += $this->calculateLineLength($line);
                    }

                    $properties['length_m'] = $totalLength;
                    $properties['length_km'] = $totalLength / 1000;
                    $properties['length_miles'] = $totalLength / 1609.344;
                }


                // Buat Feature GeoJSON
                $feature = [
                    'type' => 'Feature',
                    'geometry' => $geometry,
                    'properties' => $properties,
                ];

                if (isset($feature['bbox']) && (!is_array($feature['bbox']) || array_filter($feature['bbox'], fn($v) => !is_numeric($v)))) {
                    unset($feature['bbox']);
                }

                $features[] = $feature;
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
            case 'Sungai' :
                return $this->saveSungai($request);
            case 'Cekungan Air Tanah' :
                return $this->saveCAT($request);
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
                    }else {
                        continue;
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
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'success'   => false,
                            'message'   => "Gagal Membuat Signature",
                            'data'      => $data,
                        ]);
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
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'success'   => false,
                            'message'   => "Gagal Membuat Signature",
                            'data'      => $data,
                        ]);
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

    private function saveSungai(Request $request) {
        
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

                    $featureToSave = [
                        'type' => 'Feature',
                        'geometry' => $feature['geometry'],
                        'properties' => [
                            'name' => $data['nama_sungai'] ?? null,
                            'tag' => 'Sungai',
                            'properties' => $feature['properties']
                        ]
                    ];
                    $signature = $this->saveGeoJsonFeatureToDatabase($featureToSave);
                    if ($signature) {
                        $data['signature'] = $signature;
                        // $data['instansi_id'] = $request->input('instansi_id');
                        $data['luas_das'] = $data['luas_das'] ?: 0;
                        $data['panjang_sungai'] = $data['panjang_sungai'] ?: 0;
                        $data['ordo'] = $data['ordo'] ? : null;
                        Log::info(json_encode($data));
                        \App\Models\Sungai::create($data);
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'success'   => false,
                            'message'   => "Gagal Membuat Signature",
                            'data'      => $data,
                        ]);
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

    private function saveCAT(Request $request) {
        
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

                    $featureToSave = [
                        'type' => 'Feature',
                        'geometry' => $feature['geometry'],
                        'properties' => [
                            'name' => $data['nama_cat'] ?? null,
                            'tag' => 'Cekungan Air Tanah',
                            'properties' => $feature['properties']
                        ]
                    ];
                    $signature = $this->saveGeoJsonFeatureToDatabase($featureToSave);
                    if ($signature) {
                        $data['signature'] = $signature;
                        // $data['instansi_id'] = $request->input('instansi_id');
                        
                        \App\Models\CekunganAirTanah::create($data);
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'success'   => false,
                            'message'   => "Gagal Membuat Signature",
                            'data'      => $data,
                        ]);
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

    private function calculatePolygonArea(array $coordinates): float
    {
        // Menghitung luas polygon (meter²) menggunakan formula spherical
        // Asumsi koordinat dalam WGS84 (longitude, latitude)
        $earthRadius = 6378137; // meter

        $area = 0;
        foreach ($coordinates as $ring) { // Outer + inner rings
            $ringArea = 0;
            $pointsCount = count($ring);

            for ($i = 0; $i < $pointsCount - 1; $i++) {
                $lon1 = deg2rad($ring[$i][0]);
                $lat1 = deg2rad($ring[$i][1]);
                $lon2 = deg2rad($ring[$i+1][0]);
                $lat2 = deg2rad($ring[$i+1][1]);

                $ringArea += ($lon2 - $lon1) * (2 + sin($lat1) + sin($lat2));
            }

            $area += abs($ringArea);
        }

        return abs($area * $earthRadius * $earthRadius / 2.0); // m²
    }

    private function calculatePolygonPerimeter(array $coordinates): float
    {
        // Hitung keliling (meter)
        $perimeter = 0;
        foreach ($coordinates as $ring) {
            $perimeter += $this->calculateLineLength($ring);
        }
        return $perimeter;
    }

    private function calculateLineLength(array $coordinates): float
    {
        // Menghitung panjang garis (meter) dengan Haversine
        $length = 0;
        $earthRadius = 6378137; // meter

        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $lat1 = deg2rad($coordinates[$i][1]);
            $lon1 = deg2rad($coordinates[$i][0]);
            $lat2 = deg2rad($coordinates[$i+1][1]);
            $lon2 = deg2rad($coordinates[$i+1][0]);

            $dlat = $lat2 - $lat1;
            $dlon = $lon2 - $lon1;

            $a = sin($dlat / 2) ** 2 +
                cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            $length += $earthRadius * $c;
        }

        return $length; // meter
    }

}
