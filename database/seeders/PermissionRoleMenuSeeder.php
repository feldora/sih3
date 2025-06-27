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
    Permission::create(['name' => 'admin.dashboard']);
    Permission::create(['name' => 'admin.posts']);
    Permission::create(['name' => 'admin.menus']);
    Permission::create(['name' => 'admin.users']);
    Permission::create(['name' => 'admin.settings']);
    Permission::create(['name' => 'admin.media']);
    Permission::create(['name' => 'admin.wilayah_sungai']);
    Permission::create(['name' => 'admin.tp']);
    Permission::create(['name' => 'admin.sungai']);

    // Buat role dan assign permission
    $admin = Role::create(['name' => 'admin']);
    $admin->givePermissionTo([
        'admin.dashboard',
        'admin.posts',
        'admin.menus',
        'admin.users',
        'admin.settings',
        'admin.media',
        'admin.wilayah_sungai',
        'admin.tp',
        'admin.sungai',
    ]);

    // $user = Role::create(['name' => 'user']);
    // $user->givePermissionTo('admin.dashboard');

    $user_bws = Role::create(['name' => 'bws']);
    $user_bws->givePermissionTo('admin.dashboard');

    $user_bmkg = Role::create(['name' => 'bmkg']);
    $user_bmkg->givePermissionTo('admin.dashboard');

    $user_esdm = Role::create(['name' => 'esdm']);
    $user_esdm->givePermissionTo('admin.dashboard');

    // Assign role ke user id 1
    $adminUser = User::find(1);
    if ($adminUser) {
        $adminUser->assignRole('admin');
    }

}

}
