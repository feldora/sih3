<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KabupatenSeeder extends Seeder
{
    public function run()
    {
        DB::table('kabupaten')->insert([
                ['id' => '72.01', 'nama' => 'Kab. Banggai', 'provinsi_id' => '72'],
                ['id' => '72.02', 'nama' => 'Kab. Poso', 'provinsi_id' => '72'],
                ['id' => '72.03', 'nama' => 'Kab. Donggala', 'provinsi_id' => '72'],
                ['id' => '72.04', 'nama' => 'Kab. Toli Toli', 'provinsi_id' => '72'],
                ['id' => '72.05', 'nama' => 'Kab. Buol', 'provinsi_id' => '72'],
                ['id' => '72.06', 'nama' => 'Kab. Morowali', 'provinsi_id' => '72'],
                ['id' => '72.07', 'nama' => 'Kab. Banggai Kepulauan', 'provinsi_id' => '72'],
                ['id' => '72.08', 'nama' => 'Kab. Parigi Moutong', 'provinsi_id' => '72'],
                ['id' => '72.09', 'nama' => 'Kab. Tojo Una Una', 'provinsi_id' => '72'],
                ['id' => '72.10', 'nama' => 'Kab. Sigi', 'provinsi_id' => '72'],
                ['id' => '72.11', 'nama' => 'Kab. Banggai Laut', 'provinsi_id' => '72'],
                ['id' => '72.12', 'nama' => 'Kab. Morowali Utara', 'provinsi_id' => '72'],
                ['id' => '72.71', 'nama' => 'Kota Palu', 'provinsi_id' => '72'],
        ]);
    }
}
