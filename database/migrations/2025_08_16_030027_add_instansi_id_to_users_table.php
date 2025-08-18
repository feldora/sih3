<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('instansi_id')
                  ->nullable()
                  ->constrained('instansis')
                  ->onDelete('set null');
            $table->boolean('is_admin')->default(false);
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['instansi_id']);
            $table->dropColumn('instansi_id');
            $table->dropColumn('is_admin');
        });
    }
};
