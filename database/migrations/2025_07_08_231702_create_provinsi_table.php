<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvinsiTable extends Migration
{
    public function up()
    {
        Schema::create('provinsi', function (Blueprint $table) {
            $table->string('id');
            $table->string('nama')->nullable()->default('NULL');
        });
    }

    public function down()
    {
        Schema::dropIfExists('provinsi');
    }
}
