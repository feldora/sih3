<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KabupatenSeeder extends Seeder
{
    public function run()
    {
        DB::table('kabupaten')->insert([
                ['id' => '72.01', 'nama' => 'Banggai', 'provinsi_id' => '72'],
                ['id' => '72.02', 'nama' => 'Poso', 'provinsi_id' => '72'],
                ['id' => '72.03', 'nama' => 'Donggala', 'provinsi_id' => '72'],
                ['id' => '72.04', 'nama' => 'Toli Toli', 'provinsi_id' => '72'],
                ['id' => '72.05', 'nama' => 'Buol', 'provinsi_id' => '72'],
                ['id' => '72.06', 'nama' => 'Morowali', 'provinsi_id' => '72'],
                ['id' => '72.07', 'nama' => 'Banggai Kepulauan', 'provinsi_id' => '72'],
                ['id' => '72.08', 'nama' => 'Parigi Moutong', 'provinsi_id' => '72'],
                ['id' => '72.09', 'nama' => 'Tojo Una Una', 'provinsi_id' => '72'],
                ['id' => '72.10', 'nama' => 'Sigi', 'provinsi_id' => '72'],
                ['id' => '72.11', 'nama' => 'Banggai Laut', 'provinsi_id' => '72'],
                ['id' => '72.12', 'nama' => 'Morowali Utara', 'provinsi_id' => '72'],
                ['id' => '72.71', 'nama' => 'Kota Palu', 'provinsi_id' => '72'],
        ]);

    }
}
