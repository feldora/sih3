<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\WilayahSungaiRepositoryInterface;
use Illuminate\Http\Request;
use App\Services\GeoFeatureService;
use App\Models\WilayahSungai;
use Illuminate\Support\Facades\DB;


class WilayahSungaiController extends Controller
{
    protected $wilayahSungaiRepository;

    public function __construct(WilayahSungaiRepositoryInterface $wilayahSungaiRepository)
    {
        $this->wilayahSungaiRepository = $wilayahSungaiRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $wilayahSungais = $this->wilayahSungaiRepository->paginate(10, ['id', 'name', 'description']);
        $wilayahSungais = WilayahSungai::with('kewenangan')->paginate(10);
        return view('admin.pages.ws.index', compact('wilayahSungais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.ws.create', [
            'wilayahSungai' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instansi_id' => 'required|integer|exists:instansis,id',
            'luas_area'   => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $geoJson = [
                "type" => "Feature",
                "geometry" => [
                    "type" => "Polygon",
                    "coordinates" => [
                        array_map(function ($pair) {
                            return [floatval($pair[0]), floatval($pair[1])];
                        }, json_decode($request['coordinates'], true))
                    ]
                ],
                "properties" => [
                    "tag"  => "Wilayah Sungai",
                    "name" => $request['name'],
                    "description" => $request['description'],
                    "luas_area" => $request['luas_area'],
                    "keliling_area" => $request['keliling_area'],
                ]
            ];

            $GeoFeatureService = new GeoFeatureService();
            $geo = $GeoFeatureService->createFromGeoJson($geoJson);
            \Log::info("message", [$geo->signature]);
            $storeData = [
                'name' => $validated['name'],
                'description' => $validated['description'],
                'instansi_id' => $validated['instansi_id'],
                'luas'        => $validated['luas_area'],
                'status'      => 'active',
                'signature'   => $geo->signature,
                // 'geojson' => json_encode($geoJson),
            ];

            $this->wilayahSungaiRepository->create($storeData);

            DB::commit();

            return redirect()
                ->route('admin.wilayah-sungai.index')
                ->with('success', 'Wilayah Sungai berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Gagal menyimpan Wilayah Sungai: " . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data.']);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $wilayahSungai = $this->wilayahSungaiRepository->find($id);

        return view('admin.pages.ws.show', compact('wilayahSungai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $wilayahSungai = $this->wilayahSungaiRepository->find($id);
        $wilayahSungai->load('feature');
        
        return view('admin.pages.ws.edit', compact('wilayahSungai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instansi_id' => 'required|integer|exists:instansis,id',
            'luas_area' => 'nullable|numeric|min:0',
        ]);
        $ws = WilayahSungai::findOrFail($id);
        // Buat GeoJSON
        $geoJson = [
            "type" => "Feature",
            "geometry" => [
                "type" => "Polygon",
                "coordinates" => [
                    array_map(function ($pair) {
                        return [floatval($pair[0]), floatval($pair[1])];
                    }, json_decode($request['coordinates'], true))
                ]
            ],
            "properties" => [
                "tag" => "Wilayah Sungai",
                "name" => $request['name'],
                "description" => $request['description'],
                "luas_area" => $request['luas_area'],
                "keliling_area" => $request['keliling_area'],
            ]
        ];
        $geoService = new GeoFeatureService();
        $geo = $geoService->updateBySignature($geoJson, $ws->signature);

        // Simpan ke tabel wilayah_sungai
        $dataStore = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'instansi_id' => $validated['instansi_id'],
            'luas' => $validated['luas_area'],
            'status' => 'active',
            'signature' => $geo->signature,
            // 'geojson' => json_encode($geoJson), // kalau kamu ingin simpan GeoJSON-nya
        ];

        $this->wilayahSungaiRepository->update($id, $dataStore);

        return redirect()->route('admin.wilayah-sungai.index')
            ->with('success', 'Wilayah Sungai berhasil diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ws = WilayahSungai::where('id', $id)->first();

        $this->wilayahSungaiRepository->delete($id);
        
        return redirect()->route('admin.wilayah-sungai.index')->with('success', 'Wilayah Sungai berhasil dihapus.');
    }
}
