<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElasticsearchService;

class IndexGeoFeaturesToElasticsearch extends Command
{
    protected $signature = 'geo:index-elasticsearch';
    protected $description = 'Indeks data geojson dari database MySQL ke Elasticsearch';

    protected $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        parent::__construct();
        $this->elasticsearchService = $elasticsearchService;
    }

    public function handle()
    {
        $this->info('Memulai proses indeksasi data geojson ke Elasticsearch...');

        try {
            // Memanggil method untuk mengindeks data GeoJSON
            $result = $this->elasticsearchService->indexGeoJSONData();
            $this->info($result);
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
