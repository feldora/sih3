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
        Schema::table('geo_features', function (Blueprint $table) {
            $table->string('signature', 64)->unique()->index(); // SHA-256 panjang 64 karakter heksadesimal
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('geo_features', function (Blueprint $table) {
            $table->dropUnique(['signature']); // Hapus unique constraint
            $table->dropIndex(['signature']);  // Hapus index
            $table->dropColumn('signature');   // Hapus kolom
        });
    }
};
