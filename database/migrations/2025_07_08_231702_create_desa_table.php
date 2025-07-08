<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesaTable extends Migration
{
    public function up()
    {
        Schema::create('desa', function (Blueprint $table) {
            $table->string('id');
            $table->string('nama')->nullable()->default('NULL');
            $table->string('kecamatan_id')->nullable()->default('NULL');
        });
    }

    public function down()
    {
        Schema::dropIfExists('desa');
    }
}
