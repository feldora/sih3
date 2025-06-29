<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\SungaiRepositoryInterface;
use Illuminate\Http\Request;

class SungaiController extends Controller
{
    protected $sungaiRepository;

    public function __construct(SungaiRepositoryInterface $sungaiRepository)
    {
        $this->sungaiRepository = $sungaiRepository;
    }

    // GET /api/sungai
    public function index()
    {
        $data = $this->sungaiRepository->all();
        return response()->json($data);
    }

    // GET /api/sungai/{id}
    public function show($id)
    {
        $data = $this->sungaiRepository->find($id);
        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($data);
    }
}
