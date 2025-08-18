<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InstansiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.pages.instansi.index');
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request): JsonResponse
    {
        $query = Instansi::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('nama', 'like', "%{$searchValue}%")
                  ->orWhere('singkatan', 'like', "%{$searchValue}%");
            });
        }

        // Get total records before filtering
        $totalRecords = Instansi::count();
        $filteredRecords = $query->count();

        // Ordering
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDir = $request->order[0]['dir'];
            
            $columns = ['id', 'nama', 'singkatan', 'created_at'];
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

        $instansis = $query->get();

        // Format data for DataTables
        $data = $instansis->map(function($instansi, $index) use ($request) {
            return [
                'DT_RowId' => $instansi->id,
                'no' => ($request->start ?? 0) + $index + 1,
                'nama' => $instansi->nama,
                'singkatan' => $instansi->singkatan ?? '-',
                'users_count' => $instansi->users()->count(),
                'created_at' => $instansi->created_at->format('d/m/Y H:i'),
                'action' => $instansi->id
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
        return view('admin.pages.instansi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'singkatan' => 'nullable|string|max:50',
        ]);

        try {
            $instansi = Instansi::create([
                'nama' => $request->nama,
                'singkatan' => $request->singkatan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Instansi berhasil ditambahkan',
                'data' => $instansi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan instansi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Instansi $instansi): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $instansi->load('users')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instansi $instansi): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $instansi
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Instansi $instansi): JsonResponse
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'singkatan' => 'nullable|string|max:50',
        ]);

        try {
            $instansi->update([
                'nama' => $request->nama,
                'singkatan' => $request->singkatan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Instansi berhasil diperbarui',
                'data' => $instansi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui instansi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instansi $instansi): JsonResponse
    {
        try {
            // Check if instansi has users
            if ($instansi->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus instansi yang masih memiliki pengguna'
                ], 422);
            }

            $instansi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Instansi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus instansi: ' . $e->getMessage()
            ], 500);
        }
    }
}