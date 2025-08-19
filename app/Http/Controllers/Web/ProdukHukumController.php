<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProdukHukum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProdukHukumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.pages.produk_hukum.index');
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request): JsonResponse
    {
        $query = ProdukHukum::query();

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('judul', 'like', "%{$searchValue}%")
                  ->orWhere('tahun', 'like', "%{$searchValue}%")
                  ->orWhere('deskripsi', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = ProdukHukum::count();
        $filteredRecords = $query->count();

        // Ordering
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDir = $request->order[0]['dir'];
            
            $columns = ['id', 'tahun', 'judul', 'deskripsi', 'created_at'];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDir);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $produkHukums = $query->get();

        $data = $produkHukums->map(function($produk, $index) use ($request) {
            return [
                'DT_RowId' => $produk->id,
                'no' => ($request->start ?? 0) + $index + 1,
                'tahun' => $produk->tahun,
                'judul' => $produk->judul,
                'deskripsi' => $produk->deskripsi ?? '-',
                'file' => $produk->getFirstMediaUrl($produk->collectionName) ?: '-',
                'created_at' => $produk->created_at->format('d/m/Y H:i'),
                'action' => $produk->id
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.pages.produk_hukum.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'tahun' => 'required|integer',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048'
        ]);

        try {
            $produk = ProdukHukum::create([
                'tahun' => $request->tahun,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
            ]);

            if ($request->hasFile('file')) {
                $produk->addMediaFromRequest('file')->toMediaCollection($produk->collectionName);
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk hukum berhasil ditambahkan',
                'data' => $produk
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk hukum: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProdukHukum $produkHukum): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $produkHukum->load('media')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProdukHukum $produkHukum): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $produkHukum
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProdukHukum $produkHukum): JsonResponse
    {
        $request->validate([
            'tahun' => 'required|integer',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048'
        ]);

        try {
            $produkHukum->update([
                'tahun' => $request->tahun,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
            ]);

            if ($request->hasFile('file')) {
                $produkHukum->clearMediaCollection($produkHukum->collectionName);
                $produkHukum->addMediaFromRequest('file')->toMediaCollection($produkHukum->collectionName);
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk hukum berhasil diperbarui',
                'data' => $produkHukum
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produk hukum: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProdukHukum $produkHukum): JsonResponse
    {
        try {
            $produkHukum->clearMediaCollection($produkHukum->collectionName);
            $produkHukum->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk hukum berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk hukum: ' . $e->getMessage()
            ], 500);
        }
    }
}
