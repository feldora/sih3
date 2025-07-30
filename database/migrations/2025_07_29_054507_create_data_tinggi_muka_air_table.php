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
        Schema::create('data_tinggi_muka_air', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_pantau_id')
                  ->constrained('pos_pantau')
                  ->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam')->nullable();
            $table->decimal('tinggi_muka_air', 8, 2)->comment('dalam cm');
            
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['pos_pantau_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_tinggi_muka_air');
    }
};
