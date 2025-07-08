<?php

namespace App\Services;

use App\Services\GeoFeatureService;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\DB;

class ElasticsearchService
{
    protected $client;
    protected $geoFeatureService;

    public function __construct(GeoFeatureService $geoFeatureService)
    {
        $this->client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'http://localhost:9200')])->build();
        $this->geoFeatureService = $geoFeatureService;
    }

    /**
     * Indeks data GeoJSON dari MySQL ke Elasticsearch.
     */
    public function indexGeoJSONData()
    {
        $geoFeatures = $this->geoFeatureService->getAllAsGeoJson()['features'];

        foreach ($geoFeatures as $feature) {
            $params = [
                'index' => 'geo_features',
                'id'    => $feature['properties']['id'],
                'body'  => [
                    'name'       => $feature['properties']['name'],
                    'tag'        => $feature['properties']['tag'],
                    'properties' => $feature['properties']['properties'],
                    'signature'  => $feature['properties']['signature'],
                    'geom'       => $feature['geometry'], // Asumsikan geometry sudah dalam format GeoJSON
                ]
            ];

            // Kirim data ke Elasticsearch
            $this->client->index($params);
        }

        return "Data berhasil diindeks ke Elasticsearch.";
    }

    /**
     * Mencari data berdasarkan kata kunci (name, tag).
     */
    public function searchByKeyword($keyword)
    {
        $params = [
            'index' => 'geo_features',
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query'  => $keyword,
                        'fields' => ['name', 'tag']
                    ]
                ]
            ]
        ];

        // Eksekusi pencarian
        $response = $this->client->search($params);

        return $response['hits']['hits'];
    }

    /**
     * Mencari data geospasial dalam radius tertentu.
     */
    public function searchGeoSpatial($longitude, $latitude, $distance)
    {
        $params = [
            'index' => 'geo_features',
            'body'  => [
                'query' => [
                    'geo_distance' => [
                        'distance' => $distance,
                        'geom'     => [$longitude, $latitude]
                    ]
                ]
            ]
        ];

        // Eksekusi pencarian geospasial
        $response = $this->client->search($params);

        return $response['hits']['hits'];
    }

    /**
     * Menghitung total data di Elasticsearch.
     */
    public function countGeoFeatures()
    {
        $params = [
            'index' => 'geo_features',
            'body'  => [
                'query' => [
                    'match_all' => new \stdClass()  // Ambil semua dokumen
                ]
            ]
        ];

        $response = $this->client->count($params);

        return $response['count'];
    }

    /**
     * Update data fitur di Elasticsearch berdasarkan ID.
     */
    public function updateGeoFeature($id, array $data)
    {
        $params = [
            'index' => 'geo_features',
            'id'    => $id,
            'body'  => [
                'doc' => [
                    'name'       => $data['name'],
                    'tag'        => $data['tag'],
                    'properties' => $data['properties'],
                    'signature'  => $data['signature'],
                    'geom'       => $data['geom'], // Asumsikan geometry sudah dalam format GeoJSON
                ]
            ]
        ];

        // Kirim update data ke Elasticsearch
        $this->client->update($params);

        return "Data dengan ID $id berhasil diperbarui.";
    }

    /**
     * Hapus data berdasarkan ID dari Elasticsearch.
     */
    public function deleteGeoFeature($id)
    {
        $params = [
            'index' => 'geo_features',
            'id'    => $id
        ];

        // Hapus data dari Elasticsearch
        $this->client->delete($params);

        return "Data dengan ID $id berhasil dihapus.";
    }

    /**
     * Mengambil semua data geospasial dalam bentuk GeoJSON.
     */
    public function getAllGeoFeatures()
    {
        $params = [
            'index' => 'geo_features',
            'body'  => [
                'query' => [
                    'match_all' => new \stdClass()  // Ambil semua dokumen
                ]
            ]
        ];

        // Eksekusi pencarian semua data
        $response = $this->client->search($params);

        return $response['hits']['hits'];
    }
}
