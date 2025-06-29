<?php

namespace App\Http\Controllers\Api;

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

    // GET /api/wilayah-sungai
    public function index()
    {
        $data = $this->wilayahSungaiRepository->all(['id', 'name', 'description', 'geojson', 'status']);
        // Pastikan geojson sudah didecode ke array/object
        $data = collect($data)->map(function($item) {
            if (isset($item['geojson']) && is_string($item['geojson'])) {
                $item['geojson'] = json_decode($item['geojson'], true);
            }
            return $item;
        });
        return response()->json($data);
    }

    // GET /api/wilayah-sungai/{id}
    public function show($id)
    {
        $data = $this->wilayahSungaiRepository->find($id);
        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($data);
    }
}
