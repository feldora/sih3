<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('menu_type', 20)->default('link')->after('icon');
            $table->string('zona', 10)->default('public');
        });

        // Tambahkan constraint CHECK manual untuk PostgreSQL
        DB::statement("ALTER TABLE menus ADD CONSTRAINT menu_type_check CHECK (menu_type IN ('link', 'parent', 'child'))");
        DB::statement("ALTER TABLE menus ADD CONSTRAINT zona_check CHECK (zona IN ('public', 'admin'))");
    }

    public function down(): void
    {
        // Drop constraint terlebih dahulu sebelum drop kolom
        DB::statement("ALTER TABLE menus DROP CONSTRAINT IF EXISTS menu_type_check");
        DB::statement("ALTER TABLE menus DROP CONSTRAINT IF EXISTS zona_check");

        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('zona');
            $table->dropColumn('menu_type');
        });
    }
};
