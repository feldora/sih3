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
        Schema::create('wilayah_sungai', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('luas', 15, 2)->nullable();
            $table->string('instansi_id')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->string('signature')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayah_sungai');
    }
};
