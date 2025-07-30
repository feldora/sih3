<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PosPantauRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\PosPantau;

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
        if ($request->ajax()) {
            $query = $request->get('q');
            $jenis_pos = $request->get('jenis_pos');
            
            $queryBuilder = PosPantau::where('nama_pos', 'like', "%$query%");
            
            if ($jenis_pos) {
                $queryBuilder->where('jenis_pos', $jenis_pos);
            }
            
            $data = $queryBuilder->limit(10)
                ->get()
                ->map(fn($user) => [
                    'value' => $user->id,
                    'label' => $user->nama_pos,
                ]);

            return response()->json($data);
        }

        // Ambil semua data untuk non-AJAX request
        $data = $this->posPantauRepository->all();

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

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        return PosPantau::where('nama_pos', 'like', "%$query%")
            ->limit(10)
            ->get()
            ->map(fn($user) => [
                'value' => $user->id,
                'label' => $user->name,
            ]);
    }

}
