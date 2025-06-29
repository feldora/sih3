<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PosPantauRepositoryInterface;
use Illuminate\Http\Request;

class PosPantauController extends Controller
{
    protected $posPantauRepository;

    public function __construct(PosPantauRepositoryInterface $posPantauRepository)
    {
        $this->posPantauRepository = $posPantauRepository;
    }

    // GET /api/pos-pantau
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $data = $this->posPantauRepository->paginate($perPage);
        return response()->json($data);
    }

    // GET /api/pos-pantau/{id}
    public function show($id)
    {
        $data = $this->posPantauRepository->find($id);
        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($data);
    }
}
