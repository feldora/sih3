<?php

namespace App\Console\Commands;

use App\Services\GeoFeatureService;
use Illuminate\Console\Command;

class FindGeoFeatureContainingPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geo:find-feature ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mencari fitur yang mengandung titik berdasarkan koordinat longitude dan latitude.';

    /**
     * Instance of GeoFeatureService.
     *
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
     * @return void
     */
    public function handle()
    {
        $longitude = '120.7373889';//$this->argument('longitude');
        $latitude = '-2.1449444';//$this->argument('latitude');
        
        $this->info("Searching with POINT($longitude $latitude)");

        // Panggil fungsi findFeatureContainingPoint dari GeoFeatureService
        $feature = $this->geoFeatureService->findFeatureContainingPoint($longitude, $latitude);
        // $this->info("ID: {$feature->id}, Name: " . mb_convert_encoding($feature->name, 'UTF-8', 'UTF-8'));
        if (empty($feature)) {
            $this->info("Tidak ada fitur yang mengandung titik pada koordinat ($longitude, $latitude).");
        } else {
            $this->info("Fitur yang mengandung titik ($longitude, $latitude):");
            $this->info("ID: {$feature->id}");
            $this->info("Name: " . mb_convert_encoding($feature->name, 'UTF-8', 'UTF-8'));

            $properties = json_decode($feature->properties, true);

            if (is_array($properties)) {
                foreach ($properties as $key => $value) {
                    $this->info("{$key}: {$value}");
                }
            } else {
                $this->warn("Properti tidak bisa dibaca.");
            }
        }

    }

}
