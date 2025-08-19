<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class KontakController extends Controller
{
    /**
     * Tampilkan halaman index (hanya 1 view).
     */
    public function index(): View
    {
        return view('admin.pages.kontak.index');
    }

    /**
     * Ambil data untuk DataTables.
     */
    public function getData(Request $request): JsonResponse
    {
        $query = Kontak::with('instansi');

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('nama', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('telp', 'like', "%{$searchValue}%")
                  ->orWhere('website', 'like', "%{$searchValue}%");
            });
        }

        // Hitung total & filtered
        $totalRecords = Kontak::count();
        $filteredRecords = $query->count();

        // Ordering
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDir = $request->order[0]['dir'];
            
            $columns = ['id', 'nama', 'email', 'telp', 'website', 'created_at'];
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

        $kontaks = $query->get();

        // Format untuk DataTables
        $data = $kontaks->map(function($kontak, $index) use ($request) {
            return [
                'DT_RowId' => $kontak->id,
                'no' => ($request->start ?? 0) + $index + 1,
                'instansi' => $kontak->instansi->nama ?? '-',
                'nama' => $kontak->nama,
                'alamat' => $kontak->alamat ?? '-',
                'email' => $kontak->email ?? '-',
                'telp' => $kontak->telp ?? '-',
                'website' => $kontak->website ?? '-',
                'created_at' => $kontak->created_at->format('d/m/Y H:i'),
                'action' => $kontak->id
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
     * Simpan data baru.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'instansi_id' => 'nullable|exists:instansis,id',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telp' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $kontak = Kontak::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil ditambahkan',
                'data' => $kontak->load('instansi')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kontak: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan detail.
     */
    public function show(Kontak $kontak): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $kontak->load('instansi')
        ]);
    }

    /**
     * Edit (ambil data kontak).
     */
    public function edit(Kontak $kontak): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $kontak
        ]);
    }

    /**
     * Update data kontak.
     */
    public function update(Request $request, Kontak $kontak): JsonResponse
    {
        $request->validate([
            'instansi_id' => 'nullable|exists:instansis,id',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telp' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $kontak->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil diperbarui',
                'data' => $kontak->load('instansi')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kontak: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus kontak.
     */
    public function destroy(Kontak $kontak): JsonResponse
    {
        try {
            $kontak->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kontak: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPublicData(Request $request): JsonResponse
    {
        $instansi = Instansi::with('kontak')->get();

        return response()->json([
            'draw' => intval($request->input('draw')),   // DataTables draw counter
            'recordsTotal' => $instansi->count(),
            'recordsFiltered' => $instansi->count(),
            'data' => $instansi,
        ]);
    }
}
