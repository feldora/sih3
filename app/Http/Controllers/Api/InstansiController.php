<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Instansi;

class InstansiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instansis = Instansi::all();
        return response()->json([
            'success' => true,
            'data' => $instansis
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'singkatan' => 'nullable|string|max:100',
        ]);

        $instansi = Instansi::create($validated);

        return response()->json([
            'success' => true,
            'data' => $instansi
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $instansi = Instansi::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $instansi
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'singkatan' => 'nullable|string|max:100',
        ]);

        $instansi = Instansi::findOrFail($id);
        $instansi->update($validated);

        return response()->json([
            'success' => true,
            'data' => $instansi
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $instansi = Instansi::findOrFail($id);
        $instansi->delete();

        return response()->json(null, 204);
    }

    
}
