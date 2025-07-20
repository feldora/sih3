<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportGeoFeaturesSqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Path ke file SQL
        $path = database_path('sql/geo_features.sql');

        // Ambil isi file
        $sql = File::get($path);

        // Jalankan SQL
        DB::unprepared($sql);

        // $this->command->info('SQL file berhasil dijalankan.');
    }
}
