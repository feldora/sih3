<?php

namespace App\Http\Controllers;

use App\Models\TitikPantau;
use Illuminate\Http\Request;

class TitikPantauController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil parameter search dari request
        $search = $request->input('search');

        // Query untuk mendapatkan titik pantau, bisa menyesuaikan kolom yang ingin dicari
        $titikPantau = TitikPantau::when($search, function($query) use ($search) {
            return $query->where('nama_titik', 'like', '%' . $search . '%')
                         ->orWhere('alamat', 'like', '%' . $search . '%')
                         ->orWhere('keterangan', 'like', '%' . $search . '%');
        })
        ->paginate(10); // Sesuaikan jumlah item per halaman sesuai kebutuhan

        // Kirim data titik pantau dan query pencarian ke tampilan
        return view('admin.pages.tp.index', compact('titikPantau'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $posPantau = \App\Models\PosPantau::all();
        $wilayahSungai = \App\Models\WilayahSungai::all();
        $kategori = \App\Models\Kategori::all();
        // pd([
        //     'posPantau' => $posPantau,
        //     'wilayahSungai' => $wilayahSungai,
        //     'kategori' => $kategori
        // ]);
        return view('admin.pages.tp.create', compact('posPantau', 'wilayahSungai', 'kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // pd($request->all());
        $validated = $request->validate([
            'nama_titik' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'keterangan' => 'required|string',
            // 'pos_pantau_id' => 'required|integer|exists:pos_pantau,id',
            // 'wilayah_sungai_id' => 'required|integer|exists:wilayah_sungai,id',
            // 'kategori_id' => 'required|integer|exists:kategori,id',
        ]);

        $dataStore = [
            'nama_titik' => $request->input('nama_titik'),
            'alamat' => $request->input('alamat'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'keterangan' => $request->input('keterangan'),
            'pos_pantau_id' => $request->input('pos_pantau_id'),
            'wilayah_sungai_id' => $request->input('wilayah_sungai_id'),
            'kategori_id' => $request->input('kategori_id'),
            'status' => 'active',
        ];

        // pd($dataStore);

        TitikPantau::create($dataStore);

        return redirect()->route('admin.titik-pantau.index')->with('success', 'Titik Pantau berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TitikPantau $titikPantau)
    {
        return view('admin.pages.tp.show', compact('titikPantau'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TitikPantau $titikPantau)
    {
        // Ambil data relasi untuk dropdown
        $posPantau = \App\Models\PosPantau::all();
        $wilayahSungai = \App\Models\WilayahSungai::all();
        $kategori = \App\Models\Kategori::all();

        return view('admin.pages.tp.edit', compact('titikPantau', 'posPantau', 'wilayahSungai', 'kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TitikPantau $titikPantau)
    {
        $request->validate([
            'nama_titik' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'pos_pantau_id' => 'nullable|integer|exists:pos_pantau,id',
            'wilayah_sungai_id' => 'nullable|integer|exists:wilayah_sungai,id',
            'kategori_id' => 'nullable|integer|exists:categories,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $titikPantau->update($request->all());

        return redirect()->route('admin.titik-pantau.index')->with('success', 'Titik Pantau berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TitikPantau $titikPantau)
    {
        $titikPantau->delete();

        return redirect()->route('admin.titik-pantau.index')->with('success', 'Titik Pantau berhasil dihapus.');
    }
}
