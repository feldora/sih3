<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CekunganAirTanah;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class CekunganAirTanahController extends Controller
{
  
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CekunganAirTanah::select(['id', 'nama_cat', 'luas_cat_ha', 'potensi_air_tanah_bebas', 'potensi_air_tanah_tertekan', 'created_at', 'updated_at']);
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<div class="flex gap-2">
                        <a href="' . route('admin.cat.show', $row->id) . '" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . route('admin.cat.edit', $row->id) . '" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteData(' . $row->id . ')" class="btn btn-error btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                // ->addColumn('total_potensi', function($row){
                //     $total = ($row->potensi_air_tanah_bebas ?? 0) + ($row->potensi_air_tanah_tertekan ?? 0);
                //     return number_format($total, 2);
                // })
                ->editColumn('luas_cat_ha', function($row){
                    return number_format($row->luas_cat_ha ?? 0, 2) . ' m2';
                })
                ->editColumn('potensi_air_tanah_bebas', function($row){
                    return number_format($row->potensi_air_tanah_bebas ?? 0, 2);
                })
                ->editColumn('potensi_air_tanah_tertekan', function($row){
                    return number_format($row->potensi_air_tanah_tertekan ?? 0, 2);
                })
                // ->editColumn('created_at', function($row){
                //     return $row->created_at->format('d/m/Y H:i');
                // })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.pages.cat.index');
    }

    public function create()
    {
        return redirect()->route('admin.loadshp.index', ['type' => 'Cekungan Air Tanah']);
        // return view('admin.pages.cat.create');
    }

    public function store(Request $request)
    {
      return false;
        $request->validate([
            'nama_cat' => 'required|string|max:255',
            'luas_cat_ha' => 'nullable|numeric|min:0',
            'potensi_air_tanah_bebas' => 'nullable|numeric|min:0',
            'potensi_air_tanah_tertekan' => 'nullable|numeric|min:0',
        ], [
            'nama_cat.required' => 'Nama Cekungan Air Tanah harus diisi.',
            'nama_cat.string' => 'Nama Cekungan Air Tanah harus berupa teks.',
            'nama_cat.max' => 'Nama Cekungan Air Tanah maksimal 255 karakter.',
            'luas_cat_ha.numeric' => 'Luas CAT harus berupa angka.',
            'luas_cat_ha.min' => 'Luas CAT tidak boleh negatif.',
            'potensi_air_tanah_bebas.numeric' => 'Potensi air tanah bebas harus berupa angka.',
            'potensi_air_tanah_bebas.min' => 'Potensi air tanah bebas tidak boleh negatif.',
            'potensi_air_tanah_tertekan.numeric' => 'Potensi air tanah tertekan harus berupa angka.',
            'potensi_air_tanah_tertekan.min' => 'Potensi air tanah tertekan tidak boleh negatif.',
        ]);

        try {
            CekunganAirTanah::create($request->only([
                'nama_cat',
                'luas_cat_ha',
                'potensi_air_tanah_bebas',
                'potensi_air_tanah_tertekan'
            ]));

            return redirect()->route('admin.cat.index')
                           ->with('success', 'Data Cekungan Air Tanah berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show(CekunganAirTanah $cekunganAirTanah)
    {
        return view('admin.pages.cat.show', compact('cekunganAirTanah'));
    }

    public function edit(CekunganAirTanah $cekunganAirTanah)
    {
        return view('admin.pages.cat.edit', compact('cekunganAirTanah'));
    }

    public function update(Request $request, CekunganAirTanah $cekunganAirTanah)
    {
        $request->validate([
            'nama_cat' => 'required|string|max:255',
            'luas_cat_ha' => 'nullable|numeric|min:0',
            'potensi_air_tanah_bebas' => 'nullable|numeric|min:0',
            'potensi_air_tanah_tertekan' => 'nullable|numeric|min:0',
        ], [
            'nama_cat.required' => 'Nama Cekungan Air Tanah harus diisi.',
            'nama_cat.string' => 'Nama Cekungan Air Tanah harus berupa teks.',
            'nama_cat.max' => 'Nama Cekungan Air Tanah maksimal 255 karakter.',
            'luas_cat_ha.numeric' => 'Luas CAT harus berupa angka.',
            'luas_cat_ha.min' => 'Luas CAT tidak boleh negatif.',
            'potensi_air_tanah_bebas.numeric' => 'Potensi air tanah bebas harus berupa angka.',
            'potensi_air_tanah_bebas.min' => 'Potensi air tanah bebas tidak boleh negatif.',
            'potensi_air_tanah_tertekan.numeric' => 'Potensi air tanah tertekan harus berupa angka.',
            'potensi_air_tanah_tertekan.min' => 'Potensi air tanah tertekan tidak boleh negatif.',
        ]);

        try {
            $cekunganAirTanah->update($request->only([
                'nama_cat',
                'luas_cat_ha',
                'potensi_air_tanah_bebas',
                'potensi_air_tanah_tertekan'
            ]));

            return redirect()->route('admin.cat.index')
                           ->with('success', 'Data Cekungan Air Tanah berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

public function destroy(CekunganAirTanah $cekunganAirTanah)
{
    try {
        DB::transaction(function () use ($cekunganAirTanah) {
            if ($cekunganAirTanah->feature) {
                $cekunganAirTanah->feature()->delete();
            }

            $cekunganAirTanah->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Data Cekungan Air Tanah berhasil dihapus.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
        ], 500);
    }
}

}