<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instansi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InstansiSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Balai Wilayah Sungai Sulawesi III Palu (Kementerian PUPR)',
                'singkatan' => 'BWS Sulawesi III',
            ],
            [
                'nama' => 'Stasiun BMKG Lore Lindu Bariri',
                'singkatan' => 'BMKG Lore Lindu Bariri',
            ],
            [
                'nama' => 'Dinas Cipta Karya dan SDA Provinsi Sulawesi Tengah',
                'singkatan' => 'Dinas Cikasda',
            ],
            [
                'nama' => 'Dinas Komunikasi Informatika Persandian dan Statistik Prov. Sulteng',
                'singkatan' => 'DKIPS Sulteng',
            ],
            [
                'nama' => 'Dinas Lingkungan Hidup Provinsi Sulawesi Tengah',
                'singkatan' => 'Dinas LH Sulteng',
            ],
            [
                'nama' => 'Dinas Energi dan Sumber Daya Mineral Provinsi Sulteng',
                'singkatan' => 'Dinas ESDM Sulteng',
            ],
            [
                'nama' => 'Badan Pengelola Daerah Aliran Sungai dan Hutan Lindung Palu-Poso',
                'singkatan' => 'BPDAS-HL Palu-Poso',
            ],
        ];

        foreach ($data as $instansi) {
            Instansi::create([
                'nama' => $instansi['nama'],
                'singkatan' => $instansi['singkatan'],
            ]);
        }
    }
}
