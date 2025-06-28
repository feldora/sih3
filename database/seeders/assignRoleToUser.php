<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class assignRoleToUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bwsUser = User::where('email', "bws@localhost")->first();
        if ($bwsUser) {
            $bwsRole = Role::firstOrCreate(['name' => 'bws']);
            $bwsUser->assignRole($bwsRole);
        }
        $bmkgUser = User::where('email', "bmkg@localhost")->first();
        if ($bmkgUser) {
            $bmkgRole = Role::firstOrCreate(['name' => 'bmkg']);
            $bmkgUser->assignRole($bmkgRole);
        }
    }
}
