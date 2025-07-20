<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        return;
        Schema::create('geo_feature_points', function (Blueprint $table) {
            $table->id(); // UNSIGNED BIGINT AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('id_geo_features');
            $table->double('longitude');
            $table->double('latitude');
            $table->geometry('point', 4326);

            // Indexes
            $table->index('id_geo_features');
            $table->spatialIndex('point', 'idx_point');

            // Foreign Key
            $table->foreign('id_geo_features')
                  ->references('id')
                  ->on('geo_features')
                  ->onDelete('cascade'); // Optional, adjust as needed
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('geo_feature_points');
    }
};
