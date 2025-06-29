<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TitikPantauRepositoryInterface;
use Illuminate\Http\Request;

class TitikPantauController extends Controller
{
    protected $titikPantauRepository;

    public function __construct(TitikPantauRepositoryInterface $titikPantauRepository)
    {
        $this->titikPantauRepository = $titikPantauRepository;
    }

    // GET /api/titik-pantau
    public function index()
    {
        $data = $this->titikPantauRepository->all();
        return response()->json($data);
    }

    // GET /api/titik-pantau/{id}
    public function show($id)
    {
        $data = $this->titikPantauRepository->find($id);
        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($data);
    }
}
