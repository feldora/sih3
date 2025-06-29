<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\WilayahSungaiRepositoryInterface;
use Illuminate\Http\Request;

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
        $wilayahSungais = $this->wilayahSungaiRepository->paginate(10, ['id', 'name', 'description']);
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
        ]);

        $geoJson = [
            "type" => "FeatureCollection",
            "features" => [
                [
                    "type" => "Feature",
                    "geometry" => [
                        "type" => "Polygon",
                        "coordinates" => json_decode($request['coordinates']) // Mengonversi string JSON menjadi array
                    ],
                    "properties" => [
                        "name" => $request['name'],
                        "description" => $request['description'],
                        "luas_area" => $request['luas_area'],
                        "keliling_area" => $request['keliling_area'],
                    ]
                ]
            ]
        ];

        $storeData = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => 'active',
            'geojson' => json_encode($geoJson),
        ];

        $this->wilayahSungaiRepository->create($storeData);

        return redirect()->route('admin.wilayah-sungai.index')
                        ->with('success', 'Wilayah Sungai berhasil ditambahkan.');
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

        return view('admin.pages.ws.edit', compact('wilayahSungai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        // pd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $geoJson = [
            "type" => "FeatureCollection",
            "features" => [
                [
                    "type" => "Feature",
                    "geometry" => [
                        "type" => "Polygon",
                        "coordinates" => json_decode($request['coordinates']) // Mengonversi string JSON menjadi array
                    ],
                    "properties" => [
                        "name" => $request['name'],
                        "description" => $request['description'],
                        "luas_area" => $request['luas_area'],
                        "keliling_area" => $request['keliling_area'],
                    ]
                ]
            ]
        ];


        $dataStore = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => 'active',
            'geojson' => json_encode($geoJson),
        ];

        $this->wilayahSungaiRepository->update($id, $dataStore);

        return redirect()->route('admin.wilayah-sungai.index')->with('success', 'Wilayah Sungai berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->wilayahSungaiRepository->delete($id);

        return redirect()->route('admin.wilayah-sungai.index')->with('success', 'Wilayah Sungai berhasil dihapus.');
    }
}
