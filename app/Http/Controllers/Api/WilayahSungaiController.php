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
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $data = $this->wilayahSungaiRepository->paginate($perPage, ['id', 'name', 'description', 'geojson', 'status']);
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
