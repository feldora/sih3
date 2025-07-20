<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use geoPHP;

class GeoService
{
    public function __construct()
    {
        require_once base_path('vendor/phayes/geophp/geoPHP.inc');
    }

    public function cachePolygons($tag = 'kabupaten')
    {
        try {
            Log::info("Mulai proses caching untuk tag: $tag");

            // Cek koneksi ke Redis terlebih dahulu
            if (!$this->isRedisConnected()) {
                Log::error("Tidak dapat terhubung ke Redis. Proses caching dibatalkan.");
                return;
            }

            // Ambil data dari database
            $features = DB::table('geo_features')
                ->where('tag', $tag)
                ->select('id', DB::raw('ST_AsText(geom) as wkt'))
                ->get();

            // Jika data tidak ditemukan, log informasi dan hentikan proses
            if ($features->isEmpty()) {
                Log::info("Data tidak ditemukan untuk tag: $tag");
                return;
            }

            $maxSize = 104857600; // 1MB (Ukuran maksimal untuk WKT)
            $data = [];
            $chunkSize = 1000; // Tentukan jumlah data per chunk
            $chunks = [];

            // Proses setiap feature dan periksa ukuran WKT
            foreach ($features as $f) {
                // Mengonversi WKT ke WKB
                $geometry = geoPHP::load($f->wkt, 'wkt');
                $wkb = $geometry->out('wkb'); // Menggunakan format WKB
                
                // Cek ukuran WKB, jika terlalu besar, beri peringatan dan skip
                if (strlen($wkb) > $maxSize) {
                    Log::warning("WKB untuk ID {$f->id} terlalu besar untuk disimpan di cache (lebih dari 1MB).");
                    continue;
                }

                // Kompres WKB sebelum disimpan
                $compressedWkb = gzcompress($wkb); // Kompres data WKB
                $data[] = [
                    'id' => $f->id,
                    'wkb' => $compressedWkb,
                ];

                // Segmentasi data ke dalam chunks
                if (count($data) >= $chunkSize) {
                    $chunks[] = $data;
                    $data = []; // Reset data untuk chunk berikutnya
                }
            }

            // Jangan lupa menambahkan sisa data jika ada
            if (count($data) > 0) {
                $chunks[] = $data;
            }

            // Simpan data dalam chunks ke Redis
            foreach ($chunks as $index => $chunk) {
                $cacheKey = "polygons:{$tag}:chunk:{$index}"; // Setiap chunk memiliki key unik
                Cache::put($cacheKey, $chunk, now()->addHours(1));
            }

            // Log bahwa proses caching berhasil
            Log::info("Data berhasil disimpan ke Redis", ['chunks' => count($chunks)]);

            // Cek dan log apakah data bisa diambil kembali dari cache
            $this->retrieveCacheData($tag);

        } catch (Exception $e) {
            // Tangkap exception dan log error dengan pesan detail
            Log::error("Terjadi kesalahan: " . $e->getMessage(), ['exception' => $e]);
            Log::error("Proses caching untuk tag $tag gagal.");
        }
    }

    /**
     * Memeriksa koneksi ke Redis.
     *
     * @return bool
     */
    private function isRedisConnected()
    {
        try {
            // Coba akses Redis dengan sebuah perintah sederhana
            $redis = Cache::store('redis')->get('test_connection_key');
            return true; // Jika berhasil, Redis terhubung
        } catch (Exception $e) {
            Log::error("Redis connection error: " . $e->getMessage());
            return false; // Jika gagal, kembalikan false
        }
    }

    /**
     * Mengambil data dari cache dan mendekompresinya.
     *
     * @param string $tag
     */
    private function retrieveCacheData($tag)
    {
        $allData = [];
        $chunkIndex = 0;

        while (true) {
            $cacheKey = "polygons:{$tag}:chunk:{$chunkIndex}";
            $chunk = Cache::get($cacheKey);
            if (!$chunk) {
                Log::info('Hentikan jika tidak ada data lebih lanjut');
                break;
            }

            // Dekompresi dan konversi WKB ke WKT
            foreach ($chunk as &$item) {
                $wkb = gzuncompress($item['wkb']);
                $geometry = geoPHP::load($wkb, 'wkb');
                $item['wkt'] = $geometry ? $geometry->out('wkt') : null;
                unset($item['wkb']); // Hapus wkb agar ringan jika tidak dibutuhkan
            }

            $allData = array_merge($allData, $chunk);
            $chunkIndex++;
        }

        // Simpan semua data dalam 1 key agar bisa dipakai PnP()
        Cache::put("polygons:{$tag}", $allData, now()->addHours(1));

        Log::info("Cache data gabungan disimpan ke key: polygons:$tag", ['total' => count($allData)]);
    }

}
