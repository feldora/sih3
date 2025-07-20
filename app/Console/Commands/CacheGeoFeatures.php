<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GeoService;

class CacheGeoFeatures extends Command
{
    protected $signature = 'geo:cache {tag=kabupaten}';

    protected $description = 'Cache polygons from geo_features table into Redis';

    protected $geoService;

    public function __construct(GeoService $geoService)
    {
        parent::__construct();
        $this->geoService = $geoService;
    }

    public function handle()
    {
        $tag = $this->argument('tag');
        $this->geoService->cachePolygons($tag);

        $this->info("Polygon cache untuk tag '$tag' berhasil disimpan ke Redis.");
    }
}
