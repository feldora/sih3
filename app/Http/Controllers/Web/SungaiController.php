<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\SungaiRepositoryInterface;
use App\Models\WilayahSungai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SungaiController extends Controller
{
    protected $sungaiRepository;

    public function __construct(SungaiRepositoryInterface $sungaiRepository)
    {
        $this->sungaiRepository = $sungaiRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sungai = $this->sungaiRepository->paginate(10);
        return view('admin.pages.sungai.index', compact('sungai'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $wilayahSungai = WilayahSungai::all();
        return view('admin.pages.sungai.create', compact('wilayahSungai'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_sungai' => 'required|string|max:255',
            'panjang_sungai' => 'required|numeric|min:0',
            'luas_das' => 'required|numeric|min:0',
            'hasil_uji_kualitas_air' => 'nullable|string',
            'geojson' => 'nullable',
            'status' => 'required|in:aktif,nonaktif',
            'wilayah_sungai_id' => 'required|exists:wilayah_sungai,id',
        ], [
            'nama_sungai.required' => 'Nama sungai wajib diisi',
            'nama_sungai.max' => 'Nama sungai maksimal 255 karakter',
            'panjang_sungai.required' => 'Panjang sungai wajib diisi',
            'panjang_sungai.numeric' => 'Panjang sungai harus berupa angka',
            'panjang_sungai.min' => 'Panjang sungai tidak boleh negatif',
            'luas_das.required' => 'Luas DAS (Daerah Aliran Sungai) wajib diisi',
            'luas_das.numeric' => 'Luas DAS harus berupa angka',
            'luas_das.min' => 'Luas DAS tidak boleh negatif',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
            'wilayah_sungai_id.required' => 'Wilayah sungai wajib dipilih',
            'wilayah_sungai_id.exists' => 'Wilayah sungai yang dipilih tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->sungaiRepository->create($request->all());
            return redirect()->route('admin.sungai.index')
                ->with('success', 'Data sungai berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data sungai: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sungai = $this->sungaiRepository->find($id);
        return view('admin.pages.sungai.show', compact('sungai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $sungai = $this->sungaiRepository->find($id);
        $wilayahSungai = WilayahSungai::all();
        return view('admin.pages.sungai.edit', compact('sungai', 'wilayahSungai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_sungai' => 'required|string|max:255',
            'panjang_sungai' => 'required|numeric|min:0',
            'luas_das' => 'required|numeric|min:0',
            'hasil_uji_kualitas_air' => 'nullable|string',
            'geojson' => 'nullable',
            'status' => 'required|in:aktif,nonaktif',
            'wilayah_sungai_id' => 'required|exists:wilayah_sungai,id',
        ], [
            'nama_sungai.required' => 'Nama sungai wajib diisi',
            'nama_sungai.max' => 'Nama sungai maksimal 255 karakter',
            'panjang_sungai.required' => 'Panjang sungai wajib diisi',
            'panjang_sungai.numeric' => 'Panjang sungai harus berupa angka',
            'panjang_sungai.min' => 'Panjang sungai tidak boleh negatif',
            'luas_das.required' => 'Luas DAS (Daerah Aliran Sungai) wajib diisi',
            'luas_das.numeric' => 'Luas DAS harus berupa angka',
            'luas_das.min' => 'Luas DAS tidak boleh negatif',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
            'wilayah_sungai_id.required' => 'Wilayah sungai wajib dipilih',
            'wilayah_sungai_id.exists' => 'Wilayah sungai yang dipilih tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->sungaiRepository->update($id, $request->all());
            return redirect()->route('admin.sungai.index')
                ->with('success', 'Data sungai berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data sungai: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->sungaiRepository->delete($id);
            return redirect()->route('admin.sungai.index')
                ->with('success', 'Data sungai berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data sungai: ' . $e->getMessage());
        }
    }
}