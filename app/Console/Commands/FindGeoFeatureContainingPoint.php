<?php

namespace App\Console\Commands;

use App\Services\GeoFeatureService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class FindGeoFeatureContainingPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Sekarang sudah menerima 2 argument wajib: longitude dan latitude
     *
     * @var string
     */
    protected $signature = 'geo:find-feature
        {longitude : Longitude titik geo}
        {latitude  : Latitude titik geo}
        {--tag= : Tag fitur (default: kecamatan)}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mencari fitur yang mengandung titik berdasarkan koordinat longitude dan latitude.';

    /**
     * @var GeoFeatureService
     */
    protected $geoFeatureService;

    /**
     * Create a new command instance.
     *
     * @param GeoFeatureService $geoFeatureService
     */
    public function __construct(GeoFeatureService $geoFeatureService)
    {
        parent::__construct();
        $this->geoFeatureService = $geoFeatureService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $longitude = $this->argument('longitude');
        $latitude = $this->argument('latitude');
        $tag = $this->option('tag') ?: 'kabupaten';

        // Validasi input agar benar‑benar numeric
        $validator = Validator::make(
            compact('longitude', 'latitude'),
            [
                'longitude' => 'required|numeric|between:-180,180',
                'latitude'  => 'required|numeric|between:-90,90',
                'tag' => 'nullable|string|max:255',
            ]
        );

        if ($validator->fails()) {
            $this->error('Input tidak valid:');
            foreach ($validator->errors()->all() as $err) {
                $this->line("  • $err");
            }
            return Command::FAILURE;
        }

        $this->info("Mencari fitur pada koordinat: POINT($longitude $latitude)");

        try {
            $feature = $this->geoFeatureService
                ->findFeatureContainingPoint($longitude, $latitude, $tag);
        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan saat mencari fitur: " . $e->getMessage());
            return Command::FAILURE;
        }

        if (empty($feature)) {
            $this->info("Tidak ada fitur yang mengandung titik ($longitude, $latitude).");
        } else {
            $this->info("✅ Fitur ditemukan:");
            $this->info("  • ID   : {$feature->id}");
            $this->info("  • Nama : " . mb_convert_encoding($feature->name, 'UTF-8', 'UTF-8'));

            $properties = json_decode($feature->properties, true);

            if (is_array($properties)) {
                $this->info("  • Properties:");
                foreach ($properties as $key => $value) {
                    $this->info("      • {$key}: {$value}");
                }
            } else {
                $this->warn("⚠ Properti fitur tidak bisa dibaca sebagai JSON.");
            }
        }

        return Command::SUCCESS;
    }
}
