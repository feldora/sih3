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
        Schema::create('data_klimatologi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_pantau_id')
                  ->constrained('pos_pantau')
                  ->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam')->nullable();
            $table->decimal('kecepatan_angin', 5, 2)->nullable(); // m/s
            $table->decimal('arah_angin')->nullable();         // misal: Timur Laut
            $table->decimal('kelembapan', 5, 2)->nullable();      // %
            $table->decimal('suhu', 5, 2)->nullable();            // °C
            $table->decimal('curah_hujan', 6, 2)->nullable();     // mm
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_klimatologi');
    }
};
