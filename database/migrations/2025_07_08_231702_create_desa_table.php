<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesaTable extends Migration
{
    public function up()
    {
        Schema::create('desa', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama')->nullable();
            $table->string('kecamatan_id')->nullable();
            // $table->timestamps(); // opsional, jika mau simpan created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('desa');
    }
}
