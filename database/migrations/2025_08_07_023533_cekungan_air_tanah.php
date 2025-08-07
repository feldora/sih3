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
        Schema::create('cekungan_air_tanah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cat');
            $table->double('luas_cat_ha')->nullable()->comment('Luas cekungan air tanah dalam hektar');
            $table->string('status_pengelolaan')->nullable()->comment('Status pengelolaan cekungan (misal: Terlindungi, Dikembangkan, Terancam)');
            $table->integer('jumlah_sumur')->nullable()->comment('Jumlah sumur yang terdata dalam cekungan air tanah');
            $table->string('jenis_akuifer')->nullable()->comment('Jenis akuifer (misal: Akuifer bebas, tertekan, campuran)');
            $table->float('kedalaman_akuifer')->nullable()->comment('Kedalaman rata-rata akuifer dalam meter');
            $table->double('kapasitas_air_tanah')->nullable()->comment('Estimasi kapasitas atau potensi air tanah (L/detik atau m³/tahun)');
            $table->date('tanggal_pembaruan')->nullable()->comment('Tanggal terakhir data diperbarui');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cekungan_air_tanah');
    }
};
