<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinsiSeeder extends Seeder
{
    public function run()
    {
        DB::table('provinsi')->insert([
                ['id' => '72', 'nama' => 'Sulawesi Tengah'],
        ]);
    }
}
