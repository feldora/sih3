<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('type')->default('post');
            $table->timestampsTz();

            $table->unique(['slug', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
