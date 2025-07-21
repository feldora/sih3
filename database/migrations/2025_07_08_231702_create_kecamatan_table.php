<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKecamatanTable extends Migration
{
    public function up()
    {
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama')->nullable();
            $table->string('kabupaten_id')->nullable();
            // $table->timestamps(); // opsional jika ingin simpan waktu
        });
    }

    public function down()
    {
        Schema::dropIfExists('kecamatan');
    }
}
