<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKabupatenTable extends Migration
{
    public function up()
    {
        Schema::create('kabupaten', function (Blueprint $table) {
            $table->string('id');
            $table->string('nama')->nullable()->default('NULL');
            $table->string('provinsi_id')->nullable()->default('NULL');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kabupaten');
    }
}
