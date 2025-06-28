<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class PermissionRoleMenuSeeder extends Seeder
{
public function run()
{
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Permission::truncate();
    Role::truncate();
    Menu::truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // Buat permission
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

    // Buat role dan assign permission
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
    ]);

    $roles = ['bws', 'bmkg', 'esdm'];
    $permissions = ['dashboard', 'posts', 'media', 'pos_pengamatan', 'wilayah_sungai', 'titik_pantau'];

    foreach ($roles as $roleName) {
        $role = Role::create(['name' => $roleName]);
        $role->givePermissionTo($permissions);
    }

    $adminUser = User::find(1);
    if ($adminUser) {
        $adminUser->assignRole('admin');
    }

}

}
