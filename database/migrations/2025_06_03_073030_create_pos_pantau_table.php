<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosPantauTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_pantau', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_pos');
            $table->string('nama_pos');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('alamat')->nullable();
            $table->string('kabupaten_id')->nullable();
            $table->string('kecamatan_id')->nullable();
            $table->string('desa_id')->nullable();
            $table->string('nama_pengamat')->nullable();
            $table->year('tahun_pembangunan')->nullable();
            $table->string('instansi_id')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_pantau');
    }
}
