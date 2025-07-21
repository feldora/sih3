<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateGeoFeaturesTable extends Migration
{
    public function up()
    {
        // Pastikan ekstensi PostGIS sudah aktif
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        Schema::create('geo_features', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('tag')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });

        // Tambah kolom geometry secara manual dengan SRID 4326 dan spatial index
        DB::statement('ALTER TABLE geo_features ADD COLUMN geom geometry(Geometry, 4326)');
        DB::statement('CREATE INDEX geo_features_geom_gist ON geo_features USING GIST (geom)');
    }

    public function down(): void
    {
        Schema::dropIfExists('geo_features');
    }
}
