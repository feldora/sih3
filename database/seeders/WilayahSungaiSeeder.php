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
                'name' => 'Sungai Bongka',
                'description' => 'DAS Bongka',
            ],
            [
                'name' => 'Sungai Laa',
                'description' => 'DAS Laa',
            ],
            [
                'name' => 'Sungai Lariang',
                'description' => 'DAS Lariang',
            ],
            [
                'name' => 'Sungai Palu',
                'description' => 'DAS Palu',
            ],
            [
                'name' => 'Sungai Poso',
                'description' => 'DAS Poso',
            ],
        ]);
    }
}
