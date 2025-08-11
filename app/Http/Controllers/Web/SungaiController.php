<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sungai;
use App\Models\WilayahSungai;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SungaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Sungai::with(['wilayahSungai', 'media']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_sungai', 'LIKE', "%{$search}%")
                  ->orWhere('ordo', 'LIKE', "%{$search}%")
                  ->orWhereHas('wilayahSungai', function ($subQ) use ($search) {
                      $subQ->where('nama', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by wilayah sungai
        if ($request->filled('wilayah_sungai_id')) {
            $query->where('wilayah_sungai_id', $request->get('wilayah_sungai_id'));
        }

        $sungais = $query->orderBy('created_at', 'desc')->paginate(15);
        $wilayahSungais = WilayahSungai::orderBy('name')->get();

        return view('admin.pages.sungai.index', compact('sungais', 'wilayahSungais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $wilayahSungais = WilayahSungai::orderBy('nama')->get();
        return view('admin.pages.sungai.create', compact('wilayahSungais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_sungai' => 'required|string|max:255',
            'panjang_sungai' => 'nullable|numeric|min:0',
            'luas_das' => 'nullable|numeric|min:0',
            'ordo' => 'nullable|integer|min:1',
            'wilayah_sungai_id' => 'required|exists:wilayah_sungai,id',
            'media_files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,bmp,svg,webp'
        ], [
            'nama_sungai.required' => 'Nama sungai wajib diisi.',
            'nama_sungai.max' => 'Nama sungai maksimal 255 karakter.',
            'panjang_sungai.numeric' => 'Panjang sungai harus berupa angka.',
            'panjang_sungai.min' => 'Panjang sungai tidak boleh negatif.',
            'luas_das.numeric' => 'Luas DAS harus berupa angka.',
            'luas_das.min' => 'Luas DAS tidak boleh negatif.',
            'ordo.integer' => 'Ordo harus berupa angka.',
            'ordo.min' => 'Ordo minimal 1.',
            'wilayah_sungai_id.required' => 'Wilayah sungai wajib dipilih.',
            'wilayah_sungai_id.exists' => 'Wilayah sungai yang dipilih tidak valid.',
            'media_files.*.file' => 'File yang diupload tidak valid.',
            'media_files.*.max' => 'Ukuran file maksimal 10MB.',
            'media_files.*.mimes' => 'Format file tidak didukung. Format yang diizinkan: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF, BMP, SVG, WEBP.'
        ]);

        try {
            DB::beginTransaction();

            $sungai = Sungai::create($validated);

            // Handle media files upload
            if ($request->hasFile('media_files')) {
                foreach ($request->file('media_files') as $file) {
                    $sungai->addMediaFromRequest('media_files')
                           ->each(function ($fileAdder) {
                               $fileAdder->toMediaCollection('documents');
                           });
                }
            }

            DB::commit();

            return redirect()->route('admin.sungai.index')
                           ->with('success', 'Data sungai berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sungai: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sungai $sungai): View
    {
        $sungai->load(['wilayahSungai', 'media']);
        
        // Group media by type for better display
        $mediaByType = $sungai->getMedia('documents')->groupBy(function ($media) {
            $mimeType = $media->mime_type;
            
            if (str_starts_with($mimeType, 'image/')) {
                return 'images';
            } elseif (str_contains($mimeType, 'pdf')) {
                return 'pdf';
            } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
                return 'documents';
            } elseif (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet')) {
                return 'spreadsheets';
            } else {
                return 'others';
            }
        });

        return view('admin.pages.sungai.show', compact('sungai', 'mediaByType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sungai $sungai): View
    {
        $sungai->load('media');
        $wilayahSungais = WilayahSungai::orderBy('nama')->get();
        
        return view('admin.pages.sungai.edit', compact('sungai', 'wilayahSungais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sungai $sungai): RedirectResponse
    {
        $validated = $request->validate([
            'nama_sungai' => 'required|string|max:255',
            'panjang_sungai' => 'nullable|numeric|min:0',
            'luas_das' => 'nullable|numeric|min:0',
            'ordo' => 'nullable|integer|min:1',
            'wilayah_sungai_id' => 'required|exists:wilayah_sungai,id',
            'media_files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,bmp,svg,webp',
            'remove_media' => 'nullable|array',
            'remove_media.*' => 'exists:media,id'
        ], [
            'nama_sungai.required' => 'Nama sungai wajib diisi.',
            'nama_sungai.max' => 'Nama sungai maksimal 255 karakter.',
            'panjang_sungai.numeric' => 'Panjang sungai harus berupa angka.',
            'panjang_sungai.min' => 'Panjang sungai tidak boleh negatif.',
            'luas_das.numeric' => 'Luas DAS harus berupa angka.',
            'luas_das.min' => 'Luas DAS tidak boleh negatif.',
            'ordo.integer' => 'Ordo harus berupa angka.',
            'ordo.min' => 'Ordo minimal 1.',
            'wilayah_sungai_id.required' => 'Wilayah sungai wajib dipilih.',
            'wilayah_sungai_id.exists' => 'Wilayah sungai yang dipilih tidak valid.',
            'media_files.*.file' => 'File yang diupload tidak valid.',
            'media_files.*.max' => 'Ukuran file maksimal 10MB.',
            'media_files.*.mimes' => 'Format file tidak didukung. Format yang diizinkan: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF, BMP, SVG, WEBP.'
        ]);

        try {
            DB::beginTransaction();

            $sungai->update($validated);

            // Remove selected media files
            if ($request->filled('remove_media')) {
                foreach ($request->remove_media as $mediaId) {
                    $media = $sungai->media()->find($mediaId);
                    if ($media) {
                        $media->delete();
                    }
                }
            }

            // Handle new media files upload
            if ($request->hasFile('media_files')) {
                foreach ($request->file('media_files') as $file) {
                    $sungai->addMediaFromRequest('media_files')
                           ->each(function ($fileAdder) {
                               $fileAdder->toMediaCollection('documents');
                           });
                }
            }

            DB::commit();

            return redirect()->route('admin.sungai.show', $sungai)
                           ->with('success', 'Data sungai berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating sungai: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sungai $sungai): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Delete all associated media files
            $sungai->clearMediaCollection('documents');
            
            $sungai->delete();

            DB::commit();

            return redirect()->route('admin.sungai.index')
                           ->with('success', 'Data sungai berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting sungai: ' . $e->getMessage());
            
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.');
        }
    }

    /**
     * Download media file
     */
    public function downloadMedia(Sungai $sungai, $mediaId)
    {
        $media = $sungai->media()->findOrFail($mediaId);
        
        return response()->download($media->getPath(), $media->name);
    }

    /**
     * Delete single media file
     */
    public function deleteMedia(Sungai $sungai, $mediaId): RedirectResponse
    {
        try {
            $media = $sungai->media()->findOrFail($mediaId);
            $media->delete();

            return redirect()->back()
                           ->with('success', 'File berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error deleting media: ' . $e->getMessage());
            
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus file.');
        }
    }
}