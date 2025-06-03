<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Media::query();

        // Filter berdasarkan collection
        if ($request->filled('collection')) {
            $query->where('collection_name', $request->collection);
        }

        // Filter berdasarkan mime type
        if ($request->filled('type')) {
            $query->where('mime_type', 'like', $request->type . '%');
        }

        // Search berdasarkan nama file
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('file_name', 'like', '%' . $request->search . '%');
        }

        $media = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get collections untuk filter
        $collections = Media::distinct('collection_name')
            ->whereNotNull('collection_name')
            ->pluck('collection_name');

        // Get mime types untuk filter
        $mimeTypes = [
            'image' => 'Images',
            'video' => 'Videos',
            'audio' => 'Audio',
            'application' => 'Documents'
        ];

        return view('admin.pages.media.index', compact('media', 'collections', 'mimeTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.media.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // 10MB
            'collection' => 'nullable|string|max:255',
        ]);

        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Untuk Spatie Media Library, biasanya kita perlu model yang memiliki media
                // Jika Anda ingin upload langsung ke media table, Anda perlu membuat model khusus
                // atau menggunakan cara lain
                
                // Contoh alternatif: simpan ke storage dan buat record manual
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('media', $fileName, 'public');
                
                $media = new Media();
                $media->model_type = 'App\Models\Media'; // Atau model yang sesuai
                $media->model_id = 0; // Temporary, bisa diubah sesuai kebutuhan
                $media->uuid = Str::uuid();
                $media->collection_name = $request->collection ?? 'default';
                $media->name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $media->file_name = $fileName;
                $media->mime_type = $file->getMimeType();
                $media->disk = 'public';
                $media->size = $file->getSize();
                $media->manipulations = json_encode([]);
                $media->custom_properties = json_encode([]);
                $media->generated_conversions = json_encode([]);
                $media->responsive_images = json_encode([]);
                $media->order_column = 1;
                $media->save();

                $uploadedFiles[] = $media;
            }
        }

        return redirect()->route('admin.media.index')
            ->with('success', count($uploadedFiles) . ' file(s) berhasil diupload.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        return view('admin.pages.media.show', compact('media'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $media)
    {
        return view('admin.pages.media.edit', compact('media'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Media $media)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'collection_name' => 'nullable|string|max:255',
            'custom_properties' => 'nullable|json',
        ]);

        $customProperties = [];
        if ($request->filled('custom_properties')) {
            $customProperties = json_decode($request->custom_properties, true) ?? [];
        }

        $media->update([
            'name' => $request->name,
            'collection_name' => $request->collection_name,
            'custom_properties' => json_encode($customProperties),
        ]);

        return redirect()->route('admin.media.index')
            ->with('success', 'Media berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        try {
            // Hapus file fisik
            if (Storage::disk($media->disk)->exists($media->getPath())) {
                Storage::disk($media->disk)->delete($media->getPath());
            }

            // Hapus record dari database
            $media->delete();

            return redirect()->route('admin.media.index')
                ->with('success', 'Media berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.media.index')
                ->with('error', 'Terjadi kesalahan saat menghapus media.');
        }
    }

    /**
     * Download media file
     */
    public function download(Media $media)
    {
        $pathToFile = Storage::disk($media->disk)->path($media->getPath());
        
        if (!file_exists($pathToFile)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($pathToFile, $media->file_name);
    }

    /**
     * Bulk delete media files
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:media,id'
        ]);

        $deletedCount = 0;
        
        foreach ($request->ids as $id) {
            $media = Media::find($id);
            if ($media) {
                try {
                    // Hapus file fisik
                    if (Storage::disk($media->disk)->exists($media->getPath())) {
                        Storage::disk($media->disk)->delete($media->getPath());
                    }
                    
                    $media->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    // Log error tapi lanjut ke file berikutnya
                    Log::error('Error deleting media ID ' . $id . ': ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.media.index')
            ->with('success', $deletedCount . ' media berhasil dihapus.');
    }
    /**
     * Get media file URL
     */
    public function getMediaUrl(Media $media)
    {
        return response()->json([
            'url' => $media->getUrl(),
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
        ]);
    }
    /**
     * Get media file thumbnail URL
     */
    public function getMediaThumbnailUrl(Media $media)
    {
        if ($media->hasGeneratedConversion('thumb')) {
            return response()->json([
                'thumbnail_url' => $media->getUrl('thumb'),
            ]);
        }

        return response()->json([
            'thumbnail_url' => $media->getUrl(),
        ]);
    }
    /**
     * Get media file details
     */
    public function getMediaDetails(Media $media)
    {
        return response()->json([
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'collection_name' => $media->collection_name,
            'created_at' => $media->created_at,
            'updated_at' => $media->updated_at,
            'custom_properties' => $media->custom_properties,
        ]);
    }
    /**
     * Get media file metadata
     */
    public function getMediaMetadata(Media $media)
    {
        return response()->json([
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'collection_name' => $media->collection_name,
            'created_at' => $media->created_at,
            'updated_at' => $media->updated_at,
            'custom_properties' => $media->custom_properties,
        ]);
    }
}