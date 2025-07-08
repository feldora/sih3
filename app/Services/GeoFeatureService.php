<?php

namespace App\Services;

use App\Models\GeoFeature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GeoFeatureService
{
    /**
     * Ambil semua fitur sebagai GeoJSON FeatureCollection
     */
    public function getAllAsGeoJson(): array
    {
        $features = DB::table('geo_features')
            ->select('id', 'name', 'tag', 'properties', DB::raw('ST_AsGeoJSON(geom) as geometry'))
            ->get()
            ->map(function ($f) {
                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $f->id,
                        'name' => $f->name,
                        'tag' => $f->tag,
                        'properties' => json_decode($f->properties ?? '{}', true),
                    ],
                    'geometry' => json_decode($f->geometry),
                ];
            });

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    /**
     * Ambil data fitur dengan opsi pencarian dan pagination (GeoJSON FeatureCollection)
     *
     * @param array $params [ 'search' => string|null, 'per_page' => int, 'page' => int ]
     * @return array
     */
    public function paginate(array $params = []): array
    {
        $search = $params['search'] ?? null;
        $perPage = max((int)($params['per_page'] ?? 10), 1);
        $page = max((int)($params['page'] ?? 1), 1);

        $query = DB::table('geo_features')
            ->select('id', 'name', 'tag', 'properties', DB::raw('ST_AsGeoJSON(geom) as geometry'));

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('tag', 'like', "%$search%");
            });
        }

        $total = $query->count();

        $results = $query->forPage($page, $perPage)->get()->map(function ($f) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $f->id,
                    'name' => $f->name,
                    'tag' => $f->tag,
                    'properties' => json_decode($f->properties ?? '{}', true),
                ],
                'geometry' => json_decode($f->geometry),
            ];
        });

        return [
            'type' => 'FeatureCollection',
            'features' => $results,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
            ]
        ];
    }

    /**
     * Ambil satu fitur berdasarkan ID dalam bentuk GeoJSON
     */
    public function getOneAsGeoJson(int $id): ?array
    {
        $feature = DB::table('geo_features')
            ->where('id', $id)
            ->select('id', 'name', 'tag', 'properties', DB::raw('ST_AsGeoJSON(geom) as geometry'))
            ->first();

        if (!$feature) return null;

        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $feature->id,
                'name' => $feature->name,
                'tag' => $feature->tag,
                'properties' => json_decode($feature->properties ?? '{}', true),
            ],
            'geometry' => json_decode($feature->geometry),
        ];
    }

    /**
     * Simpan fitur baru dari GeoJSON
     */
    public function createFromGeoJson(array $geojson): GeoFeature
    {
        $geometryJson = json_encode($geojson['geometry']);
        $propertiesArray = $geojson['properties']['properties'] ?? [];

        $signature = hash('sha256', $geometryJson . json_encode($propertiesArray, JSON_UNESCAPED_UNICODE));

        return GeoFeature::create([
            'name' => $geojson['properties']['name'] ?? null,
            'tag' => $geojson['properties']['tag'] ?? null,
            'properties' => $propertiesArray,
            'signature' => $signature,
            'geom' => DB::raw("ST_GeomFromGeoJSON(" . DB::getPdo()->quote($geometryJson) . ")"),
        ]);
    }


    /**
     * Update fitur berdasarkan ID
     */
    public function updateFromGeoJson(int $id, array $geojson): ?GeoFeature
    {
        $feature = GeoFeature::find($id);
        if (!$feature) return null;

        $geometryJson = json_encode($geojson['geometry']);
        $propertiesArray = $geojson['properties']['properties'] ?? [];

        $signature = hash('sha256', $geometryJson . json_encode($propertiesArray, JSON_UNESCAPED_UNICODE));

        $feature->update([
            'name' => $geojson['properties']['name'] ?? $feature->name,
            'tag' => $geojson['properties']['tag'] ?? $feature->tag,
            'properties' => $propertiesArray,
            'geom' => DB::raw("ST_GeomFromGeoJSON(" . DB::getPdo()->quote($geometryJson) . ")"),
            'signature' => $signature,
        ]);

        return $feature->fresh();
    }


    /**
     * Hapus fitur
     */
    public function delete(int $id): bool
    {
        return GeoFeature::destroy($id) > 0;
    }

    /**
     * Temukan fitur berdasarkan titik (lon, lat)
     */
    public function findFeatureContainingPoint(float $longitude, float $latitude)
    {
        // $pointWKT = "POINT($longitude $latitude)";
        $pointWKT = "POINT($latitude $longitude)";
        // return GeoFeature::whereRaw("ST_Contains(geom, ST_GeomFromText(?, 4326))", [$pointWKT])->where('tag', 'desa')->first();
        return DB::table('geo_features')->whereRaw("ST_Contains(geom, ST_GeomFromText(?, 4326))", [$pointWKT])
                ->where('tag', 'desa')
                ->first();

    }


    public function exists(array $geojson): bool
    {
        $geometryJson = json_encode($geojson['geometry']);
        $propertiesArray = $geojson['properties']['properties'] ?? [];

        $signature = hash('sha256', $geometryJson . json_encode($propertiesArray, JSON_UNESCAPED_UNICODE));

        return GeoFeature::where('signature', $signature)->exists();
    }

    /**
     * Cari fitur berdasarkan nama (dan bisa dikembangkan untuk full-text atau Elasticsearch)
     */
    public function search(string $keyword): array
    {
        $results = DB::table('geo_features')
            ->where('name', 'like', '%' . $keyword . '%')
            ->orWhere('tag', 'like', '%' . $keyword . '%')
            ->select('id', 'name', 'tag', 'properties', DB::raw('ST_AsGeoJSON(geom) as geometry'))
            ->get()
            ->map(function ($f) {
                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $f->id,
                        'name' => $f->name,
                        'tag' => $f->tag,
                        'properties' => json_decode($f->properties ?? '{}', true),
                    ],
                    'geometry' => json_decode($f->geometry),
                ];
            });

        return [
            'type' => 'FeatureCollection',
            'features' => $results,
        ];
    }

    public function getFilteredAsGeoJson(array $filters = []): array
    {
        $query = DB::table('geo_features')
            ->select('id', 'name', 'tag', 'properties', DB::raw('ST_AsGeoJSON(geom) as geometry'));

        $kode_desa = $filters['desa'] ?? null;
        $kode_kecamatan = $filters['kecamatan'] ?? null;
        $kode_kabupaten = $filters['kabupaten'] ?? null;

        if ($kode_desa) {
            $wilayah = \App\Models\Desa::where('id', $kode_desa)->first();
            $tag = 'desa';
            $jsonKey = '$.KDEPUM';
        } elseif ($kode_kecamatan) {
            $wilayah = \App\Models\Kecamatan::where('id', $kode_kecamatan)->first();
            $tag = 'deta';
            $jsonKey = '$.KDCPUM';
        } elseif ($kode_kabupaten) {
            $wilayah = \App\Models\Kabupaten::where('id', $kode_kabupaten)->first();
            $tag = 'desa';
            $jsonKey = '$.KDPKAB';
        } else {
            // Tidak ada filter yang diberikan
            // $query->whereRaw('1 = 0');
            // $features = $query->get()->map($this->geoJsonMap());
            $features = $this->getAllAsGeoJson()['features'];
            return [
                'type' => 'FeatureCollection',
                'features' => $features, //->map($this->geoJsonMap()),
            ];
        }

        if ($wilayah) {
            $areaGeomSubquery = DB::table('geo_features')
                ->select('geom')
                ->where('tag', $tag)
                ->where("properties->'{$jsonKey}'", $wilayah->kode)
                ->limit(1);

            if ($areaGeomSubquery->exists()) {
                $bindings = $areaGeomSubquery->getBindings();

                $query->where(function ($q) use ($areaGeomSubquery, $tag, $wilayah, $jsonKey, $bindings) {
                    $q->whereRaw("ST_Within(geom, (" . $areaGeomSubquery->toSql() . "))")
                        ->addBinding($bindings, 'where')
                        ->orWhere(function ($orQ) use ($tag, $wilayah, $jsonKey) {
                            $orQ->where('tag', $tag)
                                ->where("properties->'{$jsonKey}'", $wilayah->kode);
                        });
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            $query->whereRaw('1 = 0');
        }

        $features = $query->get()->map($this->geoJsonMap());

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    protected function geoJsonMap(): \Closure
    {
        return function ($f) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $f->id,
                    'name' => $f->name,
                    'tag' => $f->tag,
                    'properties' => json_decode($f->properties ?? '{}', true),
                ],
                'geometry' => json_decode($f->geometry),
            ];
        };
    }

    /**
     * Ambil data GeoFeature dalam potongan (chunks) untuk diproses.
     *
     * @param callable $callback
     * @param int $chunkSize
     * @return void
     */
    public function chunkGeoFeatures(int $chunkSize = 100, callable $callback)
    {
        GeoFeature::chunk($chunkSize, function ($geoFeatures) use ($callback) {
            // Ambil setiap fitur dan konversi kolom geom menjadi GeoJSON
            $geoFeaturesWithGeoJSON = $geoFeatures->map(function ($feature) {
                // Mengonversi geom menjadi GeoJSON
                $geometry = DB::selectOne("SELECT ST_AsGeoJSON(geom) AS geometry FROM geo_features WHERE id = ?", [$feature->id]);
                $geometry = json_decode($geometry->geometry, true); // Mengonversi GeoJSON menjadi array
                
                // Menambahkan GeoJSON ke dalam fitur
                $feature->geometry = $geometry; // Menambahkan 'geometry' sebagai GeoJSON
                
                return $feature;
            });

            // Panggil callback dengan fitur yang sudah dimodifikasi
            $callback($geoFeaturesWithGeoJSON);
        });
    }

}
