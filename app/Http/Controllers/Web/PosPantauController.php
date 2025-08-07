<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PosPantau;
use App\Repositories\Contracts\PosPantauRepositoryInterface;
use Illuminate\Http\Request;
use App\Services\NominatimGeocodingService;
use App\Services\GeoFeatureService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class PosPantauController extends Controller
{
    protected $posPantauRepository;
    // protected $nominatim;
    
    public function __construct(PosPantauRepositoryInterface $posPantauRepository)
    {
        $this->posPantauRepository = $posPantauRepository;
        // $this->nominatim = new NominatimGeocodingService('sih3@sultengprov.go.id');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $posPantau = PosPantau::with(['kewenangan', 'kabupaten', 'kecamatan', 'desa'])->paginate(10);

        return view('admin.pages.pos_pantau.index', compact('posPantau'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.pos_pantau.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_pos' => 'required|string|max:255',
            'nama_pos' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'alamat' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'desa' => 'nullable|string|max:255',
            'nama_pengamat' => 'nullable|string|max:255',
            'tahun_pembangunan' => 'nullable|digits:4',
            'instansi_id' => 'nullable|numeric',
        ]);

        $this->posPantauRepository->create($request->all());

        return redirect()->route('admin.pos-pengamatan.index')->with('success', 'Pos Pantau created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $posPantau = $this->posPantauRepository->find($id);
        // pd($posPantau->toArray());
        return view('admin.pages.pos_pantau.edit', compact('posPantau'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        if (array_key_exists('tahun_pembangunan', $data) && empty($data['tahun_pembangunan'])) {
            $data['tahun_pembangunan'] = null;
        }
        $this->posPantauRepository->update($id, $data);

        return redirect()->route('admin.pos-pengamatan.index')->with('success', 'Data Pos Pantau berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->posPantauRepository->delete($id);

        return redirect()->route('admin.pos-pengamatan.index')->with('success', 'Data Pos Pantau berhasil dihapus.');
    }

    public function importExcel(){
        return view('admin.pages.pos_pantau.importExcel');
    }

    public function templateExcel() {
        return "oke";
    }

    public function prcImport(Request $request)
    {
        $GeoFeatureService = new GeoFeatureService();

        try {
            $postData = $request->input('data');
            $data = json_decode($postData, true);

            if (!is_array($data)) {
                throw new \Exception("Data harus berupa array.");
            }

            $features = [];
            $dataPosPantau = [];
            $signatures = [];

            foreach ($data as $value) {
                $lat = $value['latitude'];
                $lon = $value['longitude'];

                $dataArray = json_decode(json_encode($value), true);
                $feature = $GeoFeatureService->geoJsonFormat($dataArray);

                $feature['properties']['tag'] = "pos pantau";
                $feature['properties']['name'] = $dataArray['nama_pos'];

                $signature = $GeoFeatureService->signature($feature['geometry'], $feature['properties']);
                $feature['__signature'] = $signature;
                $signatures[] = $signature;

                $features[] = $feature;
                $dataArray['geo_feature_signature'] = $signature;
                $dataPosPantau[$signature] = $dataArray;
            }

            // Cek yang sudah ada
            $existing = $GeoFeatureService->existsBulk($signatures);
            $toInsert = [];
            $results = [];

            foreach ($features as $feature) {
                $sig = $feature['__signature'];
                if (!isset($existing[$sig])) {
                    $toInsert[] = $feature;
                }

                $results[] = [
                    "nama" => $feature['properties']['name'] ?? '-',
                    "signature" => $sig,
                    "status" => !isset($existing[$sig]),
                ];
            }

            DB::beginTransaction();

            if (count($toInsert)) {
                $GeoFeatureService->bulkCreateFromGeoJson($toInsert);
            }

            foreach ($results as &$res) {
                $sig = $res['signature'];
                $dataInsert = $dataPosPantau[$sig];

                // Pastikan dataInsert memiliki koordinat
                if (!empty($dataInsert['latitude']) && !empty($dataInsert['longitude'])) {
                    $lon = $dataInsert['longitude'];
                    $lat = $dataInsert['latitude'];

                    // Cari fitur kabupaten berdasarkan koordinat
                    // $kecamatan = $GeoFeatureService->findFeatureContainingPoint($lat, $lon, 'kecamatan');
                    $kecamatan = $GeoFeatureService->findFeatureContainingPoint($lon, $lat, 'kecamatan');
                    // $kecamatan = $GeoFeatureService->findContainingPointInPolygon('-0.856096', '123.041325',  'kecamatan');
                    
                    if (!empty($kecamatan)) {
                        $properties = json_decode($kecamatan->properties, true);
                        $dataInsert['kabupaten_id'] = $properties['KDWKB'] ?? null;
                        $dataInsert['kecamatan_id'] = $properties['KDWKC'] ?? null;
                    }
                }

                // Simpan data pos pantau
                $inserted = $this->posPantauRepository->create($dataInsert);
                $res['status'] = $inserted ? true : false;
            }

            DB::commit();

            return response()->json([
                "success" => true,
                "data" => $results,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                "success" => false,
                "message" => "Terjadi kesalahan saat memproses data: " . $e->getMessage(),
            ], 500);
        }
    }

}
