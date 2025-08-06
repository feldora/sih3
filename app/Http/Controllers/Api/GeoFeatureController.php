<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GeoFeatureService;
use App\Models\Kabupaten;
use App\Models\GeoFeature;
use App\Models\PosPantau;

class GeoFeatureController extends Controller
{
    protected GeoFeatureService $geoFeatureService;

    public function __construct(GeoFeatureService $geoFeatureService)
    {
        $this->geoFeatureService = $geoFeatureService;
    }

    // GET /api/geo-features
    public function index(Request $request)
    {
        if ($request->filled('search')) {
            $filtered = \App\Models\GeoFeature::where('name', 'like', '%' . $request->search . '%')->pluck('id')->toArray();
            $features = collect($filtered)->map(fn($id) => $this->geoFeatureService->getOneAsGeoJson($id))->filter();
        } else {
            $features = $this->geoFeatureService->getAllAsGeoJson()['features'];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    // GET /api/geo-features/{id}
    public function show($id)
    {
        $feature = $this->geoFeatureService->getOneAsGeoJson((int) $id);

        if (!$feature) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($feature);
    }

    // POST /api/geo-features
    public function store(Request $request)
    {
        $request->validate([
            'properties' => 'required|array',
            'geometry' => 'required|array',
        ]);

        $geometryJson = json_encode($request->geometry, JSON_UNESCAPED_UNICODE);
        $propertiesJson = json_encode($request->properties, JSON_UNESCAPED_UNICODE);

        $signature = hash('sha256', $geometryJson . $propertiesJson);

        $exists = \App\Models\GeoFeature::where('signature', $signature)->exists();
        if ($exists) {
            return response()->json(['message' => 'Fitur sudah ada'], 409);
        }

        $geojson = [
            'type' => 'Feature',
            'properties' => [
                'name' => $request->input('name'),
                'tag' => $request->input('tag'),
                'properties' => $request->properties,
            ],
            'geometry' => $request->geometry,
        ];

        $feature = $this->geoFeatureService->createFromGeoJson($geojson);

        return response()->json(['message' => 'Berhasil disimpan', 'data' => $feature], 201);
    }

    // PUT /api/geo-features/{id}
    public function update(Request $request, $id)
    {
        $request->validate([
            'properties' => 'required|array',
            'geometry' => 'required|array',
        ]);

        $geojson = [
            'type' => 'Feature',
            'properties' => [
                'name' => $request->input('name'),
                'tag' => $request->input('tag'),
                'properties' => $request->properties,
            ],
            'geometry' => $request->geometry,
        ];

        $feature = $this->geoFeatureService->updateFromGeoJson((int) $id, $geojson);

        if (!$feature) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Berhasil diperbarui']);
    }

    // DELETE /api/geo-features/{id}
    public function destroy($id)
    {
        $deleted = $this->geoFeatureService->delete((int) $id);

        if (!$deleted) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Berhasil dihapus']);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        if (empty($keyword)) {
            return response()->json([]);
        }

        $wsResults = \App\Models\WilayahSungai::where('name', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'Wilayah Sungai',
                    'name' => $item->name,
                    'description' => $item->description,
                    'id' => $item->id,
                    'geojson' => $item->geojson,
                ];
            });

        $posResults = \App\Models\PosPantau::where('nama_pos', 'like', "%{$keyword}%")
            ->orWhere('alamat', 'like', "%{$keyword}%")
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'Pos Pantau',
                    'name' => $item->nama_pos,
                    'description' => $item->alamat,
                    'id' => $item->id,
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                ];
            });

        $tpResults = \App\Models\TitikPantau::where('nama_titik', 'like', "%{$keyword}%")
            ->orWhere('alamat', 'like', "%{$keyword}%")
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'Titik Pantau',
                    'name' => $item->nama_titik,
                    'description' => $item->alamat,
                    'id' => $item->id,
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                ];
            });

        $geoFeatureResults = collect($this->geoFeatureService->search($keyword)['features'])
            ->map(function ($item) {
                return [
                    'type' => 'Geo Feature',
                    'name' => $item['properties']['name'],
                    'description' => json_encode($item['properties']['properties']),
                    'id' => $item['properties']['id'],
                    'geojson' => $item,
                ];
            });

        $results = $wsResults->concat($posResults)->concat($tpResults)->concat($geoFeatureResults);

        return response()->json($results);
    }

    public function getProvinsi()
    {
        return response()->json(\App\Models\Provinsi::orderBy('nama')->get());
    }

    public function getKabupaten(Request $request)
    {
        $provinsi_id = $request->query('provinsi_id');

        $kabupaten = \App\Models\Kabupaten::when($provinsi_id, function ($query, $provinsi_id) {
            return $query->where('provinsi_id', $provinsi_id);
        })->orderBy('nama')->get();

        return response()->json($kabupaten);
    }

    public function getMapKabupaten() {
        $filters = [
            'tag' => 'kabupaten',
            'properties->KDWPR' => '72',
        ];
        $features = $this->geoFeatureService->getAllAsGeoJson($filters);
        $headers = [] ;
        return response()->json($features, 200, $headers);
    }

    public function getMapKabupatenInfo(Request $request){
        $kab_id = $request->query('KDWKB');
        $data['kab_id'] = $kab_id;

        // Mendapatkan tanggal satu bulan terakhir
        $oneMonthAgo = now()->subMonth(); // `now()` adalah fungsi helper Laravel untuk tanggal saat ini

        // Ambil data titik_pantau berdasarkan kabupaten_id
        $titik_pantau = PosPantau::where('kabupaten_id', $kab_id)->where('status', 'Aktif')
                                ->with([
                                    'dataCurahHujan' => function($query) use ($oneMonthAgo) {
                                        $query->where('tanggal', '>=', $oneMonthAgo);
                                    },
                                    'dataKlimatologi' => function($query) use ($oneMonthAgo) {
                                        $query->where('tanggal', '>=', $oneMonthAgo);
                                    },
                                    'dataTinggiMukaAir' => function($query) use ($oneMonthAgo) {
                                        $query->where('tanggal', '>=', $oneMonthAgo);
                                    }
                                ])
                                ->limit(10)
                                ->get();

        // Menyusun data yang akan dikembalikan ke response
        $data['titik_pantau'] = $titik_pantau;
        
        $headers = [];
        return response()->json($data, 200, $headers);
    }


    public function getKecamatan(Request $request)
    {
        $kabupaten_id = $request->query('kabupaten_id');
        if (!$kabupaten_id) {
            return response()->json(['message' => 'Kabupaten is required'], 400);
        }
        return response()->json(\App\Models\Kecamatan::where('kabupaten_id', $kabupaten_id)->orderBy('nama')->get());
    }

    public function getDesa(Request $request)
    {
        $kecamatan_id = $request->query('kecamatan_id');
        if (!$kecamatan_id) {
            return response()->json(['message' => 'Kecamatan is required'], 400);
        }
        return response()->json(\App\Models\Desa::where('kecamatan_id', $kecamatan_id)->orderBy('nama')->get());
    }

    public function filter(Request $request)
    {
        $filters = $request->only(['desa']);
        $data = $this->geoFeatureService->getFilteredAsGeoJson($filters);

        return response()->json($data);
    }

}
