<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sungai', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sungai');
            $table->float('panjang_sungai');
            $table->float('luas_das');
            $table->text('hasil_uji_kualitas_air')->nullable();
            $table->json('geojson')->nullable();
            $table->string('status')->default('aktif');
            $table->unsignedBigInteger('wilayah_sungai_id');
            $table->timestamps();
        });

        Schema::table('sungai', function (Blueprint $table) {
            $table->foreign('wilayah_sungai_id')
                ->references('id')
                ->on('wilayah_sungai')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sungai', function (Blueprint $table) {
            $table->dropForeign(['wilayah_sungai_id']);
        });
        Schema::dropIfExists('sungai');
    }
};
