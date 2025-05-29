<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // Menu Publik
        $dashboard = Menu::create([
            'title' => 'Dashboard',
            'url' => '/dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'parent_id' => null,
            'order' => 1,
            'permission_name' => null,
            'menu_type' => 'public',
        ]);

        $informasiH3 = Menu::create([
            'title' => 'Informasi H3',
            'url' => '/informasi-h3',
            'icon' => 'fas fa-info-circle',
            'parent_id' => null,
            'order' => 2,
            'permission_name' => null,
            'menu_type' => 'public',
        ]);

        Menu::create([
            'title' => 'Info H3',
            'url' => '/informasi-h3/info',
            'icon' => 'fas fa-info',
            'parent_id' => $informasiH3->id,
            'order' => 1,
            'permission_name' => null,
            'menu_type' => 'public',
        ]);

        Menu::create([
            'title' => 'Data H3',
            'url' => '/informasi-h3/data',
            'icon' => 'fas fa-database',
            'parent_id' => $informasiH3->id,
            'order' => 2,
            'permission_name' => null,
            'menu_type' => 'public',
        ]);

        Menu::create([
            'title' => 'Neraca Air',
            'url' => '/informasi-h3/neraca-air',
            'icon' => 'fas fa-water',
            'parent_id' => $informasiH3->id,
            'order' => 3,
            'permission_name' => null,
            'menu_type' => 'public',
        ]);

        $geospasial = Menu::create([
            'title' => 'Geospasial',
            'url' => '/geospasial',
            'icon' => 'fas fa-globe',
            'parent_id' => null,
            'order' => 3,
            'permission_name' => null,
            'menu_type' => 'public',
        ]);

        Menu::create([
            'title' => 'Peta Geospasial',
            'url' => '/geospasial/peta',
            'icon' => 'fas fa-map',
            'parent_id' => $geospasial->id,
            'order' => 1,
            'permission_name' => null,
            'menu_type' => 'public',
        ]);

        Menu::create([
            'title' => 'Monitoring Data',
            'url' => '/geospasial/monitoring',
            'icon' => 'fas fa-chart-line',
            'parent_id' => $geospasial->id,
            'order' => 2,
            'permission_name' => null,
            'menu_type' => 'public',
        ]);

        Menu::create([
            'title' => 'Produk Hukum',
            'url' => '/produk-hukum',
            'icon' => 'fas fa-gavel',
            'parent_id' => null,
            'order' => 4,
            'permission_name' => null,
            'menu_type' => 'public',
        ]);

        Menu::create([
            'title' => 'Kontak',
            'url' => '/kontak',
            'icon' => 'fas fa-envelope',
            'parent_id' => null,
            'order' => 5,
            'permission_name' => null,
            'menu_type' => 'public',
        ]);

        // Menu Admin
        Menu::create([
            'title' => 'Manajemen Menu',
            'url' => '/admin/menus',
            'icon' => 'fas fa-list',
            'parent_id' => null,
            'order' => 1,
            'permission_name' => 'admin.menus',
            'menu_type' => 'admin',
        ]);

        Menu::create([
            'title' => 'Manajemen Pengguna',
            'url' => '/admin/users',
            'icon' => 'fas fa-users',
            'parent_id' => null,
            'order' => 2,
            'permission_name' => 'admin.users',
            'menu_type' => 'admin',
        ]);

        Menu::create([
            'title' => 'Pengaturan',
            'url' => '/admin/settings',
            'icon' => 'fas fa-cogs',
            'parent_id' => null,
            'order' => 3,
            'permission_name' => 'admin.settings',
            'menu_type' => 'admin',
        ]);
    }
}
