<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvinsiTable extends Migration
{
    public function up()
    {
        Schema::create('provinsi', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama')->nullable();
            // $table->timestamps(); // opsional, jika ingin simpan created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('provinsi');
    }
}
