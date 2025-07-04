<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PosPantauRepositoryInterface;
use Illuminate\Http\Request;
use App\Services\NominatimGeocodingService;

class PosPantauController extends Controller
{
    protected $posPantauRepository;
    
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
        $posPantau = $this->posPantauRepository->all();
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
            'kewenangan' => 'nullable|string|max:255',
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
}
