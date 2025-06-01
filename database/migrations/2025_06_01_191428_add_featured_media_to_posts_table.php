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
        // Add featured_media_id column to posts table
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('featured_media_id')->nullable()->after('content');
        });

        // Add foreign key constraint if the featured_media_id references a media table
        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('featured_media_id')->references('id')->on('media')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['featured_media_id']);
            $table->dropColumn('featured_media_id');
        });
    }
};
