<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat user administrator
        User::factory()->create([
            'name' => 'administrator',
            'email' => 'admin@localhost',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Operator BWS Sulawesi III Palu',
            'email' => 'bws@localhost',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Operator BMKG Sulawesi',
            'email' => 'bmkg@localhost',
            'password' => bcrypt('password'),
        ]);

        $this->call([
            PermissionRoleMenuSeeder::class,
            MenuSeeder::class,
            CategorySeeder::class,
            TagsTableSeeder::class,
            PostSeeder::class,
            WilayahSungaiSeeder::class,
            assignRoleToUser::class,
            ProvinsiSeeder::class,
            KabupatenSeeder::class,
            KecamatanSeeder::class,
            DesaSeeder::class,
        ]);
    }
}
