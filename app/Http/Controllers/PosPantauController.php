<?php

namespace App\Http\Controllers;

use App\Models\PosPantau;
use Illuminate\Http\Request;

class PosPantauController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posPantau = PosPantau::all();
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

        PosPantau::create($request->all());

        return redirect()->route('admin.pos-pengamatan.index')->with('success', 'Pos Pantau created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PosPantau $pos_pengamatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $posPantau = PosPantau::findOrFail($id);
        return view('admin.pages.pos_pantau.edit', compact('posPantau'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosPantau $pos_pengamatan)
    {
        $data = $request->all();
        if (array_key_exists('tahun_pembangunan', $data) && empty($data['tahun_pembangunan'])) {
            $data['tahun_pembangunan'] = null;
        }
        $pos_pengamatan->update($data);

        return redirect()->route('admin.pos-pengamatan.index')->with('success', 'Data Pos Pantau berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosPantau $pos_pengamatan)
    {
        $pos_pengamatan->delete();

        return redirect()->route('admin.pos-pengamatan.index')->with('success', 'Data Pos Pantau berhasil dihapus.');
    }
}
