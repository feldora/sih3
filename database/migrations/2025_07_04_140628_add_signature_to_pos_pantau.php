<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_pantau', function (Blueprint $table) {
            $table->string('geo_feature_signature', 64)->nullable();

            // Foreign key ke geo_features(signature)
            $table->foreign('geo_feature_signature')
                  ->references('signature')
                  ->on('geo_features')
                  ->onDelete('set null') // atau 'cascade' sesuai kebutuhan
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('pos_pantau', function (Blueprint $table) {
            $table->dropForeign(['geo_feature_signature']);
            $table->dropColumn('geo_feature_signature');
        });
    }
};
