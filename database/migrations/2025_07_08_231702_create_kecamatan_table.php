<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKecamatanTable extends Migration
{
    public function up()
    {
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->string('id');
            $table->string('nama')->nullable()->default('NULL');
            $table->string('kabupaten_id')->nullable()->default('NULL');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kecamatan');
    }
}
