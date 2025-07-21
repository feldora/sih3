<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Akan menjadi BIGSERIAL di PostgreSQL
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestampsTz(); // Gunakan timestamps with timezone (lebih umum untuk PostgreSQL)
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
