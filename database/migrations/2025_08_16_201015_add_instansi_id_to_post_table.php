<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {

            $table->dropColumn('role');
            
            $table->foreignId('instansi_id')
                  ->nullable()
                  ->constrained('instansis')
                  ->onDelete('set null');

            $table->foreignId('pos_pantau_id')
                  ->nullable()
                  ->constrained('pos_pantau')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {

            $table->dropForeign(['instansi_id']);
            $table->dropColumn('instansi_id');
            
            $table->dropForeign(['pos_pantau_id']);
            $table->dropColumn('pos_pantau_id');
            
            $table->string('role')->nullable();
        });
    }
};
