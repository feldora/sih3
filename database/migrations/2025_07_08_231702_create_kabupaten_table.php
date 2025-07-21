<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKabupatenTable extends Migration
{
    public function up()
    {
        Schema::create('kabupaten', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama')->nullable();
            $table->string('provinsi_id')->nullable();
            // $table->timestamps(); // opsional, kalau mau simpan created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('kabupaten');
    }
}
