<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TitikPantau;
use Illuminate\Http\Request;

class TitikPantauController extends Controller
{
    // GET /api/titik-pantau
    public function index()
    {
        $data = TitikPantau::all();
        return response()->json($data);
    }

    // GET /api/titik-pantau/{id}
    public function show($id)
    {
        $data = TitikPantau::find($id);
        
        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }
        
        return response()->json($data);
    }
}