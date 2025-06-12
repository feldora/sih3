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
        Schema::create('titik_pantau', function (Blueprint $table) {
            $table->id();
            $table->string('nama_titik');
            $table->string('alamat')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('pos_pantau_id')->nullable();
            $table->unsignedBigInteger('wilayah_sungai_id')->nullable();
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('pos_pantau_id')->references('id')->on('pos_pantau')->onDelete('set null');
            $table->foreign('wilayah_sungai_id')->references('id')->on('wilayah_sungai')->onDelete('set null');
            $table->foreign('kategori_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titik_pantau');
    }
};
