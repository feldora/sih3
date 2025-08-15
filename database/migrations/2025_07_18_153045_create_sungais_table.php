<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sungai', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sungai');
            $table->float('panjang_sungai');
            $table->float('luas_das');
            $table->float('ordo');
            $table->string('signature')->nullable();
            $table->timestamps();
        });

        // Schema::table('geo_features', function (Blueprint $table) {
        //     $table->foreign('signature')
        //         ->references('signature')
        //         ->on('geo_features')
        //         ->onDelete('cascade');
        // });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('geo_features', function (Blueprint $table) {
            $table->dropForeign(['signature']);
        });

        Schema::dropIfExists('sungai');
    }

};
