<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PermissionRoleMenuSeeder extends Seeder
{
    public function run()
    {
        // Hapus cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Nonaktifkan sementara foreign key constraints (bisa dipakai untuk PostgreSQL dan MySQL)
        Schema::disableForeignKeyConstraints();
        Permission::truncate();
        Role::truncate();
        Menu::truncate();
        Schema::enableForeignKeyConstraints();

        // Buat permissions
        Permission::create(['name' => 'dashboard']);
        Permission::create(['name' => 'posts']);
        Permission::create(['name' => 'menus']);
        Permission::create(['name' => 'users']);
        Permission::create(['name' => 'settings']);
        Permission::create(['name' => 'media']);
        Permission::create(['name' => 'pos_pengamatan']);
        Permission::create(['name' => 'wilayah_sungai']);
        Permission::create(['name' => 'titik_pantau']);
        Permission::create(['name' => 'sungai']);
        Permission::create(['name' => 'hidrologi']);
        Permission::create(['name' => 'tinggi_muka_air']);
        Permission::create(['name' => 'debit']);
        Permission::create(['name' => 'sedimen']);
        Permission::create(['name' => 'meteorologi']);
        Permission::create(['name' => 'curah_hujan']);
        Permission::create(['name' => 'geologi']);
        Permission::create(['name' => 'muka_air_tanah']);
        Permission::create(['name' => 'minatan_hidrogeologi']);
        Permission::create(['name' => 'kualitas_air_tanah']);
        Permission::create(['name' => 'cekungan_air_tanah']);
        Permission::create(['name' => 'hidrogeologi']);

        // Buat role admin dan assign semua permission
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'dashboard',
            'posts',
            'menus',
            'users',
            'settings',
            'media',
            'wilayah_sungai',
            'titik_pantau',
            'sungai',
            'pos_pengamatan',
            'hidrologi',
            'tinggi_muka_air',
            'debit',
            'sedimen',
            'meteorologi',
            'curah_hujan',
            'geologi',
            'muka_air_tanah',
            'minatan_hidrogeologi',
            'kualitas_air_tanah',
            'cekungan_air_tanah',
            'hidrogeologi',
        ]);

        // Buat role lain dan assign sebagian permission
        $roles = ['bws', 'bmkg', 'esdm'];
        $permissions = ['dashboard', 'posts', 'media', 'pos_pengamatan', 'wilayah_sungai', 'titik_pantau'];

        foreach ($roles as $roleName) {
            $role = Role::create(['name' => $roleName]);
            $role->givePermissionTo($permissions);
        }

        // Assign role admin ke user ID 1 jika ada
        $adminUser = User::find(1);
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }
    }
}
