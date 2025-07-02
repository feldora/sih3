<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Shapefile\ShapefileReader;
use Shapefile\ShapefileException;
use ZipArchive;

class LoadShpController extends Controller
{
    public function index()
    {
        return view('admin.pages.loadshp.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'shp_file' => 'required|file|mimes:zip|max:10240',
        ]);

        try {
            $file = $request->file('shp_file');

            if (!$file->isValid()) {
                return response()->json(['success' => false, 'message' => 'File upload tidak valid.'], 400);
            }

            $timestamp = time();
            $tmpName = $timestamp . '_' . $file->getClientOriginalName();
            $tmpZipPath = "/tmp/{$tmpName}";
            $file->move('/tmp', $tmpName);
            Log::info("Uploaded ZIP saved to: {$tmpZipPath}");

            $extractPath = "/tmp/extracted_{$timestamp}";
            File::makeDirectory($extractPath, 0755, true);

            $zip = new ZipArchive;
            $res = $zip->open($tmpZipPath);
            if ($res !== true) {
                Log::error("ZipArchive->open() failed, code={$res}");
                return response()->json(['success' => false, 'message' => "Gagal membuka ZIP (kode $res)"], 400);
            }

            $zip->extractTo($extractPath);
            $zip->close();
            Log::info("ZIP berhasil diekstrak ke: {$extractPath}");

            $shpFile = $this->findShpInDir($extractPath);
            if (!$shpFile) {
                return response()->json(['success' => false, 'message' => 'File .shp tidak ditemukan dalam ZIP'], 400);
            }

            $geoJson = $this->convertShpToGeoJson($shpFile);

            File::delete($tmpZipPath);
            File::deleteDirectory($extractPath);
            // return $geoJson;
            return response()->json(['success' => true, 'geojson' => $geoJson]);
        } catch (\Exception $e) {
            Log::error("Error proses shapefile: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function findShpInDir(string $dir): ?string
    {
        $allFiles = File::allFiles($dir);
        foreach ($allFiles as $f) {
            if (strtolower($f->getExtension()) === 'shp') {
                return $f->getPathname();
            }
        }
        return null;
    }

    private function convertShpToGeoJson(string $shpPath): array
    {
        try {
            $reader = new ShapefileReader($shpPath);
            $features = [];

            while ($rec = $reader->fetchRecord()) {
                if ($rec->isDeleted()) continue;
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => json_decode($rec->getGeoJSON()),
                    'properties' => $rec->getDataArray(),
                ];
            }

            return ['type' => 'FeatureCollection', 'features' => $features];
        } catch (ShapefileException $e) {
            throw new \Exception('Error reading SHP: ' . $e->getMessage());
        }
    }
}
