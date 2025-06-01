<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tags')->insert([
            // Hidrologi
            [
                'name' => 'sungai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'danau',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'waduk',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Hidrometeorologi
            [
                'name' => 'curah hujan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'banjir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'kekeringan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Hidrogeologi
            [
                'name' => 'air tanah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'akuifer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'sumur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
