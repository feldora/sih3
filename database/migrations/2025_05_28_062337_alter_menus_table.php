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
        Schema::table('menus', function (Blueprint $table) {
            $table->string('menu_type', 20)->default('link')->check("menu_type IN ( 'link', 'parent', 'child')")->after('icon');
            $table->string('zona', 10)->default('public')->check("zona IN ('public', 'admin')");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('zona');
            $table->dropColumn('menu_type');
        });
    }
};
