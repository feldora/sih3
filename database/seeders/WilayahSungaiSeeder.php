<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class WilayahSungaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('wilayah_sungai')->insert([
            [
                'name' => 'Palu - Lariang',
                'description' => '',
            ],
            [
                'name' => 'Parigi - Poso',
                'description' => '',
            ],
        ]);
    }
}
