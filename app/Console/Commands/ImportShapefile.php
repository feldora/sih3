<?php

namespace App\Console\Commands;

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0); 

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Shapefile\ShapefileReader;
use Shapefile\ShapefileException;
use App\Services\GeoFeatureService;

class ImportShapefile extends Command
{
    protected $signature = 'shp:import
        {zip_path : Path ke file .zip yang berisi shapefile}
        {--name-field=name : Nama kolom di properties untuk digunakan sebagai "name"}
        {--tag= : Nilai kolom tag untuk semua fitur (opsional)}';

    protected $description = 'Import SHP (zip) dan simpan ke tabel geo_features';

    protected GeoFeatureService $geoFeatureService;

    public function __construct(GeoFeatureService $geoFeatureService)
    {
        parent::__construct();
        $this->geoFeatureService = $geoFeatureService;
    }

    public function handle()
    {
        $zipPath = $this->argument('zip_path');
        $nameField = $this->option('name-field') ?? 'name';
        $tag = $this->option('tag'); // bisa null

        if (!file_exists($zipPath)) {
            $this->error("❌ File tidak ditemukan: $zipPath");
            return 1;
        }

        $extractPath = storage_path('app/tmp_shp_' . time());
        File::makeDirectory($extractPath);

        $zip = new \ZipArchive;
        if ($zip->open($zipPath) !== true) {
            $this->error("❌ Gagal membuka file ZIP.");
            return 1;
        }

        $zip->extractTo($extractPath);
        $zip->close();

        $shpFile = collect(File::allFiles($extractPath))->first(fn($file) => strtolower($file->getExtension()) === 'shp');

        if (!$shpFile) {
            $this->error("❌ File .shp tidak ditemukan di dalam ZIP.");
            File::deleteDirectory($extractPath);
            return 1;
        }

        $this->info("📦 Mengimpor: " . $shpFile->getFilename());

        try {
            $reader = new ShapefileReader($shpFile->getPathname());
            $saved = 0;

            while ($record = $reader->fetchRecord()) {
                if ($record->isDeleted()) continue;

                $geoArray = json_decode($record->getGeoJSON(), true);
                $geometry = $this->sanitizeGeometry($geoArray);

                $properties = $record->getDataArray();
                $name = $properties[$nameField] ?? null;

                $geojson = [
                    'type' => 'Feature',
                    'properties' => [
                        'name' => $name,
                        'tag' => $tag, // langsung dari opsi CLI
                        'properties' => $properties,
                    ],
                    'geometry' => json_decode($geometry, true),
                ];

                // cek duplikat dan simpan via service
                if ($this->geoFeatureService->exists($geojson)) {
                    continue;
                }

                $this->geoFeatureService->createFromGeoJson($geojson);
                $saved++;
            }

            $this->info("✅ Berhasil menyimpan $saved fitur baru.");
        } catch (ShapefileException $e) {
            $this->error("❌ Gagal membaca SHP: " . $e->getMessage());
        } catch (\Exception $e) {
            $this->error("❌ Error saat menyimpan ke database: " . $e->getMessage());
            $this->line($e->getTraceAsString());
        } finally {
            File::deleteDirectory($extractPath);
        }

        return 0;
    }

    private function sanitizeGeometry(array $geometry): string
    {
        if (isset($geometry['type'])) {
            $geometry['type'] = preg_replace('/[ZM]+$/', '', $geometry['type']);
        }

        $geometry['coordinates'] = $this->sanitizeCoordinates($geometry['coordinates']);

        return json_encode($geometry);
    }

    private function sanitizeCoordinates($coords)
    {
        if (!is_array($coords)) return $coords;

        if (isset($coords[0]) && is_array($coords[0]) && is_numeric($coords[0][0])) {
            return array_map(fn($point) => array_slice($point, 0, 2), $coords);
        }

        return array_map([$this, 'sanitizeCoordinates'], $coords);
    }
}
