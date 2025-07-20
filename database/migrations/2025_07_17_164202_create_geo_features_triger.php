<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    public function up(): void
    {
        return;
        // Path file SQL relatif ke folder project
        $path = database_path('sql/geo_features_triger.sql');

        // Baca isi file
        $sql = File::get($path);

        // Jalankan SQL secara langsung
        DB::unprepared($sql);
    }

    public function down(): void
    {
        // Jika ingin rollback, kamu bisa drop trigger/procedure jika ada
        // Contoh:
        DB::unprepared('DROP TRIGGER IF EXISTS after_insert_geo_features;');
        DB::unprepared('DROP TRIGGER IF EXISTS after_update_geo_features;');
        DB::unprepared('DROP PROCEDURE IF EXISTS insert_geo_feature_points;');
    }
};
