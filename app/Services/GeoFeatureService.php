<?php

namespace App\Services;

use App\Models\GeoFeature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use geoPHP;
use Illuminate\Support\Facades\Cache;

class GeoFeatureService
{
    protected array $pointCache = [];
    
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
        $geometry = $geojson['geometry'];
        $properties = $geojson['properties']['properties'] ?? $geojson['properties'];
        $propertiesArray = $properties ?? [];
        
        $signature = $this->signature($geometry, $propertiesArray);

        return GeoFeature::create([
            'name' => $geojson['properties']['name'] ?? null,
            'tag' => $geojson['properties']['tag'] ?? null,
            'properties' => $propertiesArray,
            'signature' => $signature,
            'geom' => DB::raw("ST_GeomFromGeoJSON(" . DB::getPdo()->quote(json_encode($geometry)) . ")"),
        ]);
    }

    /**
     * 
     */
    public function bulkCreateFromGeoJson(array $features): void
    {
        $insertData = [];

        foreach ($features as $feature) {
            $geometry = $feature['geometry'];
            $properties = $feature['properties']['properties'] ?? $feature['properties'];
            $signature = $this->signature($geometry, $properties);

            // $insertData[] = [
            //     'name' => $feature['properties']['name'] ?? null,
            //     'tag' => $feature['properties']['tag'] ?? null,
            //     'properties' => $properties,
            //     'signature' => $signature,
            //     'geom' => DB::raw("ST_GeomFromGeoJSON(" . DB::getPdo()->quote(json_encode($geometry)) . ")"),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ];
            $insertData[] = [
                'name' => $feature['properties']['name'] ?? null,
                'tag' => $feature['properties']['tag'] ?? null,
                'properties' => json_encode($properties, JSON_UNESCAPED_UNICODE),
                'signature' => $signature,
                'geom' => DB::raw("ST_GeomFromGeoJSON(" . DB::getPdo()->quote(json_encode($geometry)) . ")"),
                'created_at' => now(),
                'updated_at' => now(),
            ];

        }

        GeoFeature::insert($insertData);
    }


    /**
     * Fungsi untuk mengubah data menjadi geojson
     */
    public function geoJsonFormat(array $data): array
    {
        if (isset($data['type'], $data['geometry'], $data['properties']) && $data['type'] === 'Feature') {
            return $data;
        }

        $geometry = null;
        if (isset($data['geom'])) {
            $geometry = is_array($data['geom']) ? $data['geom'] : null;
        } elseif (isset($data['latitude'], $data['longitude'])) {
            $geometry = [
                "type" => "Point",
                "coordinates" => [(float)$data['longitude'], (float)$data['latitude']],
            ];
        }

        $exclude = ['geom', 'geometry', 'latitude', 'longitude'];
        $properties = array_diff_key($data, array_flip($exclude));

        return [
            "type" => "Feature",
            "geometry" => $geometry ?? (object)[],
            "properties" => $properties,
        ];
    }


    public function signature($geometry, $properties): string
    {
        if (is_array($geometry)) {
            $geometryJson = json_encode($geometry);
        } else {
            $geometryJson = $geometry;
        }

        return hash('sha256', $geometryJson . json_encode($properties, JSON_UNESCAPED_UNICODE));
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
    public function findFeatureContainingPoint(float $longitude, float $latitude, $tag = 'kabupaten')
    {
        // $pointWKT = "POINT($longitude $latitude)";
        $pointWKT = "POINT($latitude $longitude)";

        return DB::table('geo_features')
            ->whereRaw("ST_Intersects(geom, ST_GeomFromText(?, 4326))", [$pointWKT])
            ->where('tag', $tag)
            ->first();
    }

    public function PnP(float $longitude, float $latitude, $tag = 'kecamatan')
    {
        require_once base_path('vendor/phayes/geophp/geoPHP.inc');

        // Ambil dari Redis
        $cachedPolygons = Cache::get("polygons:$tag");

        if (!$cachedPolygons) {
            // Coba isi ulang dari database
            $geoService = new \App\Services\GeoService();
            $geoService->cachePolygons($tag); // isi ulang
            $cachedPolygons = Cache::get("polygons:$tag");

            if (!$cachedPolygons) {
                Log::warning("Cache polygon untuk tag $tag masih kosong setelah fallback.");
                return null;
            }
        }

        // Buat titik point (lng lat — urutan benar)
        $point = geoPHP::load("POINT($longitude $latitude)", 'wkt');

        // Loop semua polygon dan cari yang mengandung titik
        foreach ($cachedPolygons as $feature) {
            if (empty($feature['wkt'])) continue;

            $polygon = geoPHP::load($feature['wkt'], 'wkt');
            if ($polygon && $polygon->contains($point)) {
                // Ambil data asli dari DB berdasarkan id
                return DB::table('geo_features')
                    ->where('id', $feature['id'])
                    ->first();
            }
        }

        return null;
    }



    public function findFeatureContainingPointCached(float $lon, float $lat, string $tag = 'kabupaten')
    {
        $key = "{$tag}:" . round($lon, 5) . "," . round($lat, 5);

        if (isset($this->pointCache[$key])) {
            return $this->pointCache[$key];
        }

        $result = $this->findFeatureContainingPoint($lon, $lat, $tag);
        $this->pointCache[$key] = $result;

        return $result;
    }



    public function exists(array $geojson)
    {
        $geometryJson = json_encode($geojson['geometry']);
        $prop = $geojson['properties']['properties'] ?? $geojson['properties'];
        $propertiesArray = $prop ?? [];
        
        $signature = $this->signature($geometryJson , $propertiesArray);
        // $signature = hash('sha256', $geometryJson . json_encode($propertiesArray, JSON_UNESCAPED_UNICODE));

        $feature = GeoFeature::where('signature', $signature);
        if($feature->exists()) {
            return $feature->first();
        } else {
            return false;
        }
    }
    /**
     * 
     */
    public function existsBulk(array $signatures): array
    {
        return GeoFeature::whereIn('signature', $signatures)
            ->pluck('signature')
            ->flip()
            ->toArray();
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

    public function cachePolygons($tag = 'kecamatan')
    {
        $geoService = new \App\Services\GeoService();
        $geoService->cachePolygons($tag);
    }


}
