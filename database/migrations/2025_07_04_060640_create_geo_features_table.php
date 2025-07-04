<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeoFeaturesTable extends Migration
{
    public function up()
    {
        Schema::create('geo_features', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->json('properties')->nullable();
            $table->geometry('geom');
            $table->timestamps();
            $table->spatialIndex('geom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_features');
    }
};
