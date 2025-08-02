<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'hidrologi',
                'slug' => 'hidrologi',
                'type' => 'post',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'hidrometeorologi',
                'slug' => 'hidrometeorologi',
                'type' => 'post',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'hidrogeologi',
                'slug' => 'hidrogeologi',
                'type' => 'post',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            /**
             * ====================================
             */
            [
                'id' => 4,
                'name' => 'data muka air tanah',
                'slug' => 'data-muka-air-tanah',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'data minatan hidrogeologi',
                'slug' => 'data-minatan-hidrogeologi',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'data kualitas air tanah',
                'slug' => 'data-kualitas-air-tanah',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'data curah hujan',
                'slug' => 'data-curah-hujan',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'data tinggi muka air',
                'slug' => 'data-tinggi-muka-air',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'name' => 'data debit',
                'slug' => 'data-debit',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'name' => 'data sedimen',
                'slug' => 'data-sedimen',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'name' => 'analisis dan prakiraan iklim dasarian',
                'slug' => 'analisis-prakiraan-iklim-dasarian',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'name' => 'peringatan dini cuaca dan iklim (pdci) dasarian',
                'slug' => 'peringatan-dini-cuaca-iklim-dasarian',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'name' => 'buletin bulanan iklim prov. sulteng',
                'slug' => 'buletin-bulanan-iklim-sulteng',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 14,
                'name' => 'buletin musim prov. sulteng',
                'slug' => 'buletin-musim-sulteng',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 15,
                'name' => 'buletin tahunan prov. sulteng',
                'slug' => 'buletin-tahunan-sulteng',
                'type' => 'data',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
