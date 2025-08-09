<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosPantau;
use Illuminate\Http\Request;
use App\Services\GeoFeatureService;

class PosPantauController extends Controller
{
    public function index(Request $request) {

        $filters = [
            'tag' => ['Pos Klimatologi','Pos Duga Air','Pos Curah Hujan']
        ];
        $geoFeatureService = new GeoFeatureService();
        $geoJson = $geoFeatureService->getAllAsGeoJson($filters);
        foreach ($geoJson['features'] as $key => $Feature) {
                $PosPantau = PosPantau::with([
                    'kewenangan', 
                    'ws', 
                    'kecamatan', 
                    'kabupaten', 
                    'desa',
                    'dataCurahHujan',
                    'dataKlimatologi',
                    'dataTinggiMukaAir'
                ])->where('geo_feature_signature', $Feature['signature'])->first()->toArray();
                $geoJson['features'][$key] = array_merge($Feature, $PosPantau);
        }

        $headers = [] ;
        return response()->json($geoJson, 200, $headers);
    }
    // GET /api/pos-pantau
    public function getAllDetail(Request $request)
    {
        if ($request->ajax()) {
            $query = $request->get('q');
            $jenis_pos = $request->get('jenis_pos');
            
            $queryBuilder = PosPantau::with([
                'kewenangan', 
                'ws', 
                'kecamatan', 
                'kabupaten', 
                'desa'
            ])->where('nama_pos', 'like', "%$query%");
            
            if ($jenis_pos) {
                $queryBuilder->where('jenis_pos', $jenis_pos);
            }
            
            $data = $queryBuilder->limit(10)
                ->get()
                ->map(fn($pos) => [
                    'value' => $pos->id,
                    'label' => $pos->nama_pos,
                ]);

            return response()->json($data);
        }

        // Ambil semua data untuk non-AJAX request dengan relasi
        $data = PosPantau::with([
            'kewenangan',
            'ws',
            'geoFeature',
            'kecamatan',
            'kabupaten',
            'desa',
            'dataCurahHujan',
            'dataKlimatologi',
            'dataTinggiMukaAir'
        ])->get();

        return response()->json($data);
    }

    // GET /api/pos-pantau/{id}
    public function show($id)
    {
        $data = PosPantau::with([
            'kewenangan',
            'ws',
            'geoFeature',
            'kecamatan',
            'kabupaten',
            'desa',
            'dataCurahHujan',
            'dataKlimatologi',
            'dataTinggiMukaAir'
        ])->find($id);
        
        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }
        
        return response()->json($data);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        return PosPantau::where('nama_pos', 'like', "%$query%")
            ->limit(10)
            ->get()
            ->map(fn($pos) => [
                'value' => $pos->id,
                'label' => $pos->nama_pos, // Diperbaiki dari $user->name menjadi $pos->nama_pos
            ]);
    }
}