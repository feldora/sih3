<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;

use App\Models\Sungai;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SungaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sungai = Sungai::with('media'); //->select(['id', 'nama_sungai', 'panjang_sungai', 'luas_das', 'ordo', 'created_at']);

            return DataTables::of($sungai)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="flex gap-2" role="group">';
                    $btn .= '<a href="' . route('admin.sungai.show', $row->id) . '" class="btn btn-info btn-sm">Detail</a>';
                    $btn .= '<a href="' . route('admin.sungai.edit', $row->id) . '" class="btn btn-warning btn-sm">Edit</a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">Hapus</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->addColumn('media_count', function ($row) {
                    return $row->getMedia('dokumen sungai')->count() . ' file(s)';
                })
                ->editColumn('panjang_sungai', function ($row) {
                    return $row->panjang_sungai ? $row->panjang_sungai . ' km' : '-';
                })
                ->editColumn('luas_das', function ($row) {
                    return $row->luas_das ? number_format($row->luas_das, 2) . ' km²' : '-';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.pages.sungai.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        // return view('admin.pages.sungai.create');
        return redirect()->route('admin.loadshp.index', ['type' => 'Sungai']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'nama_sungai' => 'required|string|max:255',
            'panjang_sungai' => 'nullable|numeric|min:0',
            'luas_das' => 'nullable|numeric|min:0',
            'ordo' => 'nullable|integer|min:1',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240'
        ]);

        $sungai = Sungai::create($request->only([
            'nama_sungai',
            'panjang_sungai', 
            'luas_das',
            'ordo'
        ]));

        // Handle file uploads if any
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $sungai->addMediaFromRequest('documents')
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection('documents');
                    });
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data sungai berhasil ditambahkan.',
                'data' => $sungai
            ]);
        }

        return redirect()->route('admin.sungai.index')->with('success', 'Data sungai berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sungai $sungai): View|JsonResponse
    {
        $sungai->load('media');

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $sungai
            ]);
        }
        
        return view('admin.pages.sungai.show', compact('sungai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sungai $sungai): View
    {
        $sungai->load('media');
        return view('admin.pages.sungai.edit', compact('sungai'));
    }

/**
 * Update the specified resource in storage.
 */
public function update(Request $request, Sungai $sungai): JsonResponse|RedirectResponse
{
    try {
        $request->validate([
            'nama_sungai' => 'required|string|max:255',
            'panjang_sungai' => 'nullable|numeric|min:0',
            'luas_das' => 'nullable|numeric|min:0',
            'ordo' => 'nullable|integer|min:1|max:9',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'integer|exists:media,id'
        ]);

        // Update basic data
        $sungai->update($request->only([
            'nama_sungai',
            'panjang_sungai',
            'luas_das', 
            'ordo'
        ]));

        // Handle file deletions FIRST
        if ($request->has('delete_media') && is_array($request->delete_media)) {
            foreach ($request->delete_media as $mediaId) {
                $media = $sungai->getMedia('dokumen sungai')->where('id', $mediaId)->first();
                if ($media) {
                    $media->delete();
                }
            }
        }

        // Handle new file uploads - FIXED VERSION
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $sungai->addMedia($file)
                    ->toMediaCollection('documents');
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data sungai berhasil diperbarui.',
                'data' => $sungai->fresh()->load('media')
            ]);
        }

        return redirect()->route('admin.sungai.index')->with('success', 'Data sungai berhasil diperbarui.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        return back()->withErrors($e->errors())->withInput();

    } catch (\Exception $e) {
        \Log::error('Error updating sungai: ' . $e->getMessage());
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.'
            ], 500);
        }
        return back()->with('error', 'Terjadi kesalahan saat memperbarui data.')->withInput();
    }
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sungai $sungai): JsonResponse|RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Hapus semua media terkait
            $sungai->clearMediaCollection('documents');

            // Hapus geo_feature terkait kalau ada
            if ($sungai->feature) {
                $sungai->feature->delete();
            }

            // Hapus record sungai
            $sungai->delete();

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data sungai (dan geo_feature terkait) berhasil dihapus.'
                ]);
            }

            return redirect()
                ->route('admin.sungai.index')
                ->with('success', 'Data sungai (dan geo_feature terkait) berhasil dihapus.');

        } catch (\Throwable $e) {
            DB::rollBack();

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data sungai.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()
                ->route('admin.sungai.index')
                ->with('error', 'Gagal menghapus data sungai.');
        }
    }


    /**
     * Download media file
     */
    public function downloadMedia($sungaiId, $mediaId): BinaryFileResponse
    {
        $sungai = Sungai::findOrFail($sungaiId);
        $media = $sungai->getMedia('dokumen sungai')->where('id', $mediaId)->firstOrFail();

        $mimeType = $media->mime_type; // Contoh: application/pdf
        $extension = strtolower($media->extension); // Contoh: pdf

        // Tentukan mode tampilan
        $inlineExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm'];

        if (in_array($extension, $inlineExtensions)) {
            return response()->file($media->getPath(), [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.$media->file_name.'"'
            ]);
        }

        // Default: download file
        return response()->download($media->getPath(), $media->file_name, [
            'Content-Type' => $mimeType
        ]);
    }

    /**
     * List media file
     */
    public function listMedia($sungaiId): JsonResponse
    {
        $sungai = Sungai::findOrFail($sungaiId);
        $mediaCollection = $sungai->getMedia('dokumen sungai');

        $mediaList = $mediaCollection->map(function ($media) {
            return [
                'id' => $media->id,
                'file_name' => $media->file_name,
                'extension' => $media->extension,
                'mime_type' => $media->mime_type,
                'size_kb' => round($media->size / 1024, 2),
                'url' => $media->getUrl(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $mediaList
        ]);
    }


    /**
     * Delete specific media file
     */
    public function deleteMedia($sungaiId, $mediaId): JsonResponse
    {
        $sungai = Sungai::findOrFail($sungaiId);
        $media = $sungai->getMedia('dokumen sungai')->where('id', $mediaId)->first();
        
        if ($media) {
            $media->delete();
            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File tidak ditemukan.'
        ], 404);
    }
}