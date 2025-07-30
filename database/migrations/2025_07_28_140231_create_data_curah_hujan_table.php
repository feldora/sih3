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
        Schema::create('data_curah_hujan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_pantau_id')
                  ->constrained('pos_pantau')
                  ->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam')->nullable();
            $table->decimal('curah_hujan', 8, 2)->comment('dalam mm');
            $table->string('kategori', 20)->nullable()->comment('ringan, sedang, lebat, sangat_lebat');
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
        Schema::dropIfExists('data_curah_hujan');
    }
};
