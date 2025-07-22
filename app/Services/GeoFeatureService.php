<?php

namespace App\Services;

use App\Models\GeoFeature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use geoPHP;

class GeoFeatureService
{
    protected array $pointCache = [];

    public function getAllAsGeoJson(): array
    {
        $features = DB::table('geo_features')
            ->select('id', 'name', 'tag', 'properties', DB::raw('ST_AsGeoJSON(geom) as geometry'))
            ->get()
            ->map($this->geoJsonMap());

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    public function paginate(array $params = []): array
    {
        $search = $params['search'] ?? null;
        $perPage = max((int)($params['per_page'] ?? 10), 1);
        $page = max((int)($params['page'] ?? 1), 1);

        $query = DB::table('geo_features')
            ->select('id', 'name', 'tag', 'properties', DB::raw('ST_AsGeoJSON(geom) as geometry'));

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%$search%")
                  ->orWhere('tag', 'ILIKE', "%$search%");
            });
        }

        $total = $query->count();

        $results = $query->forPage($page, $perPage)->get()->map($this->geoJsonMap());

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

    public function getOneAsGeoJson(int $id): ?array
    {
        $feature = DB::table('geo_features')
            ->where('id', $id)
            ->select('id', 'name', 'tag', 'properties', DB::raw('ST_AsGeoJSON(geom) as geometry'))
            ->first();

        if (!$feature) return null;

        return ($this->geoJsonMap())($feature);
    }

    public function createFromGeoJson(array $geojson): GeoFeature
    {
        $geometry = $geojson['geometry'];
        $properties = $geojson['properties']['properties'] ?? $geojson['properties'];
        $signature = $this->signature($geometry, $properties);

        return GeoFeature::create([
            'name' => $geojson['properties']['name'] ?? null,
            'tag' => $geojson['properties']['tag'] ?? null,
            'properties' => $properties,
            'signature' => $signature,
            'geom' => DB::raw("ST_SetSRID(ST_GeomFromGeoJSON(" . DB::getPdo()->quote(json_encode($geometry)) . "), 4326)"),
        ]);
    }

    public function bulkCreateFromGeoJson(array $features): void
    {
        $insertData = [];

        foreach ($features as $feature) {
            $geometry = $feature['geometry'];
            $properties = $feature['properties']['properties'] ?? $feature['properties'];
            $signature = $this->signature($geometry, $properties);

            $insertData[] = [
                'name' => $feature['properties']['name'] ?? null,
                'tag' => $feature['properties']['tag'] ?? null,
                'properties' => json_encode($properties, JSON_UNESCAPED_UNICODE),
                'signature' => $signature,
                'geom' => DB::raw("ST_SetSRID(ST_GeomFromGeoJSON(" . DB::getPdo()->quote(json_encode($geometry)) . "), 4326)"),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        GeoFeature::insert($insertData);
    }

    public function updateFromGeoJson(int $id, array $geojson): ?GeoFeature
    {
        $feature = GeoFeature::find($id);
        if (!$feature) return null;

        $geometryJson = json_encode($geojson['geometry']);
        $propertiesArray = $geojson['properties']['properties'] ?? [];

        $signature = $this->signature($geojson['geometry'], $propertiesArray);

        $feature->update([
            'name' => $geojson['properties']['name'] ?? $feature->name,
            'tag' => $geojson['properties']['tag'] ?? $feature->tag,
            'properties' => $propertiesArray,
            'geom' => DB::raw("ST_SetSRID(ST_GeomFromGeoJSON(" . DB::getPdo()->quote($geometryJson) . "), 4326)"),
            'signature' => $signature,
        ]);

        return $feature->fresh();
    }

    public function delete(int $id): bool
    {
        return GeoFeature::destroy($id) > 0;
    }

    public function findFeatureContainingPoint(float $lon, float $lat, $tag = 'kabupaten')
    {
        $pointWKT = "POINT($lon $lat)";

        return DB::table('geo_features')
            ->whereRaw("ST_Intersects(geom, ST_SetSRID(ST_MakePoint(?, ?), 4326))", [$lon, $lat])
            ->where('tag', $tag)
            ->first();
    }

    public function PnP(float $lon, float $lat, $tag = 'kecamatan')
    {
        require_once base_path('vendor/phayes/geophp/geoPHP.inc');

        $cachedPolygons = Cache::get("polygons:$tag");

        if (!$cachedPolygons) {
            $geoService = new \App\Services\GeoService();
            $geoService->cachePolygons($tag);
            $cachedPolygons = Cache::get("polygons:$tag");
            if (!$cachedPolygons) {
                Log::warning("Cache polygon untuk tag $tag masih kosong setelah fallback.");
                return null;
            }
        }

        $point = geoPHP::load("POINT($lon $lat)", 'wkt');

        foreach ($cachedPolygons as $feature) {
            if (empty($feature['wkt'])) continue;

            $polygon = geoPHP::load($feature['wkt'], 'wkt');
            if ($polygon && $polygon->contains($point)) {
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
        $signature = $this->signature($geojson['geometry'], $propertiesArray);

        return GeoFeature::where('signature', $signature)->first() ?: false;
    }

    public function existsBulk(array $signatures): array
    {
        return GeoFeature::whereIn('signature', $signatures)
            ->pluck('signature')
            ->flip()
            ->toArray();
    }

    public function search(string $keyword): array
    {
        $results = DB::table('geo_features')
            ->where('name', 'ILIKE', '%' . $keyword . '%')
            ->orWhere('tag', 'ILIKE', '%' . $keyword . '%')
            ->select('id', 'name', 'tag', 'properties', DB::raw('ST_AsGeoJSON(geom) as geometry'))
            ->get()
            ->map($this->geoJsonMap());

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

        $jsonKey = '';
        $tag = '';
        $wilayah = null;

        if ($kode_desa) {
            $wilayah = \App\Models\Desa::find($kode_desa);
            $jsonKey = 'KDEPUM';
            $tag = 'desa';
        } elseif ($kode_kecamatan) {
            $wilayah = \App\Models\Kecamatan::find($kode_kecamatan);
            $jsonKey = 'KDCPUM';
            $tag = 'kecamatan';
        } elseif ($kode_kabupaten) {
            $wilayah = \App\Models\Kabupaten::find($kode_kabupaten);
            $jsonKey = 'KDPKAB';
            $tag = 'kabupaten';
        }

        if ($wilayah) {
            $areaGeomSubquery = DB::table('geo_features')
                ->select('geom')
                ->where('tag', $tag)
                ->whereRaw("properties->>'$jsonKey' = ?", [$wilayah->kode])
                ->limit(1);

            if ($areaGeomSubquery->exists()) {
                $bindings = $areaGeomSubquery->getBindings();
                $query->where(function ($q) use ($areaGeomSubquery, $bindings, $jsonKey, $tag, $wilayah) {
                    $q->whereRaw("ST_Within(geom, ({$areaGeomSubquery->toSql()}))", $bindings)
                      ->orWhere(function ($orQ) use ($jsonKey, $wilayah, $tag) {
                          $orQ->where('tag', $tag)
                              ->whereRaw("properties->>'$jsonKey' = ?", [$wilayah->kode]);
                      });
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            $features = $this->getAllAsGeoJson()['features'];
            return [
                'type' => 'FeatureCollection',
                'features' => $features,
            ];
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
            $geometry = json_decode($f->geometry, true);

            // Cek jika koordinat sangat besar (kemungkinan EPSG:3857)
            if ($this->isMercator($geometry)) {
                $geometry = $this->convertMercatorToWGS84($geometry);
            }

            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $f->id,
                    'name' => $f->name,
                    'tag' => $f->tag,
                    'properties' => json_decode($f->properties ?? '{}', true),
                ],
                'geometry' => $geometry,
            ];
        };
    }

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
        return hash('sha256', json_encode($geometry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK) . json_encode($properties, JSON_UNESCAPED_UNICODE));
    }

    public function cachePolygons($tag = 'kecamatan')
    {
        $geoService = new \App\Services\GeoService();
        $geoService->cachePolygons($tag);
    }

    protected function isMercator(array $geometry): bool
    {
        $coords = $geometry['coordinates'] ?? null;

        if (!$coords || !is_array($coords)) return false;

        $first = $this->extractFirstCoordinate($coords);

        if (!$first || !is_numeric($first[0]) || !is_numeric($first[1])) return false;

        // Jika X jauh lebih besar dari 180 derajat → kemungkinan besar ini EPSG:3857
        return abs($first[0]) > 180 || abs($first[1]) > 90;
    }

    protected function extractFirstCoordinate($coords)
    {
        while (is_array($coords[0])) {
            $coords = $coords[0];
        }
        return $coords;
    }

    protected function convertMercatorToWGS84(array $geometry): array
    {
        $geometry['coordinates'] = $this->convertRecursive($geometry['coordinates']);
        return $geometry;
    }

    protected function convertRecursive($coords)
    {
        if (!is_array($coords[0])) {
            return $this->fromMercatorToLatLng($coords[0], $coords[1]);
        }

        return array_map(function ($c) {
            return $this->convertRecursive($c);
        }, $coords);
    }

    protected function fromMercatorToLatLng(float $x, float $y): array
    {
        $R = 6378137.0;
        $lng = ($x / $R) * (180 / pi());
        $lat = rad2deg(atan(sinh($y / $R)));
        return [$lng, $lat];
    }

}
