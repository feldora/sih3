<?php

namespace App\Http\Controllers;

use App\Models\WilayahSungai;
use Illuminate\Http\Request;

class WilayahSungaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wilayahSungais = WilayahSungai::select('id', 'name', 'description')->paginate(10);
        return view('admin.pages.ws.index', compact('wilayahSungais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.ws.create');
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
            // 'coordinates' => 'nullable|array',
        ]);

        $storeData = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => 'active',
            'geojson' => $request->has('coordinates') ? json_encode($request->input('coordinates')) : null,
        ];

        WilayahSungai::create($storeData);

        return redirect()->route('admin.wilayah-sungai.index')
                        ->with('success', 'Wilayah Sungai berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     */
    public function show(WilayahSungai $wilayahSungai)
    {

        return view('admin.pages.ws.show', compact('wilayahSungai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WilayahSungai $wilayahSungai)
    {
        return view('admin.pages.ws.edit', compact('wilayahSungai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WilayahSungai $wilayahSungai)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $wilayahSungai->update($validated);

        return redirect()->route('admin.wilayah-sungai.index')->with('success', 'Wilayah Sungai berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WilayahSungai $wilayahSungai)
    {
        $wilayahSungai->delete();

        return redirect()->route('admin.wilayah-sungai.index')->with('success', 'Wilayah Sungai berhasil dihapus.');
    }
}
