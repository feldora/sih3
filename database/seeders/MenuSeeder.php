<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // Menu Publik
        $informasiH3 = Menu::create([
            'title' => 'Informasi H3',
            'url' => '/informasi-h3',
            'icon' => 'fas fa-info-circle',
            'parent_id' => null,
            'order' => 1,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'parent',
        ]);

        Menu::create([
            'title' => 'Info H3',
            'url' => '/informasi-h3/info',
            'icon' => 'fas fa-info',
            'parent_id' => $informasiH3->id,
            'order' => 1,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Data H3',
            'url' => '/informasi-h3/data',
            'icon' => 'fas fa-database',
            'parent_id' => $informasiH3->id,
            'order' => 2,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Neraca Air',
            'url' => '/informasi-h3/neraca-air',
            'icon' => 'fas fa-water',
            'parent_id' => $informasiH3->id,
            'order' => 3,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'child',
        ]);

        $geospasial = Menu::create([
            'title' => 'Geospasial',
            'url' => '/geospasial',
            'icon' => 'fas fa-globe',
            'parent_id' => null,
            'order' => 2,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'parent',
        ]);

        Menu::create([
            'title' => 'Peta Geospasial',
            'url' => '/geospasial/peta',
            'icon' => 'fas fa-map',
            'parent_id' => $geospasial->id,
            'order' => 1,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Monitoring Data',
            'url' => '/geospasial/monitoring',
            'icon' => 'fas fa-chart-line',
            'parent_id' => $geospasial->id,
            'order' => 2,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Produk Hukum',
            'url' => '/produk-hukum',
            'icon' => 'fas fa-gavel',
            'parent_id' => null,
            'order' => 3,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'link',
        ]);

        Menu::create([
            'title' => 'Kontak',
            'url' => '/kontak',
            'icon' => 'fas fa-envelope',
            'parent_id' => null,
            'order' => 4,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'link',
        ]);

        Menu::create([
            'title' => 'Artikel',
            'url' => '/artikel',
            'icon' => 'fas fa-file-alt',
            'parent_id' => null,
            'order' => 5,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'link',
        ]);

        Menu::create([
            'title' => 'Berita',
            'url' => '/berita',
            'icon' => 'fas fa-bullhorn',
            'parent_id' => null,
            'order' => 6,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'link',
        ]);

        // Menu Admin
        Menu::create([
            'title' => 'Dashboard',
            'url' => '/admin/dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'parent_id' => null,
            'order' => 1,
            'permission_name' => 'admin.dashboard',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);
        Menu::create([
            'title' => 'Postingan',
            'url' => '/admin/posts',
            'icon' => 'fas fa-newspaper',
            'parent_id' => null,
            'order' => 2,
            'permission_name' => 'admin.posts',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);

        $pengaturan = Menu::create([
            'title' => 'Pengaturan',
            'url' => '/admin/settings',
            'icon' => 'fas fa-cogs',
            'parent_id' => null,
            'order' => 999,
            'permission_name' => 'admin.settings',
            'zona' => 'admin',
            'menu_type' => 'parent',
        ]);

        Menu::create([
            'title' => 'Manajemen Menu',
            'url' => '/admin/menus',
            'icon' => 'fas fa-list',
            'parent_id' => $pengaturan->id,
            'order' => 1,
            'permission_name' => 'admin.menus',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Manajemen Pengguna',
            'url' => '/admin/users',
            'icon' => 'fas fa-users',
            'parent_id' => $pengaturan->id,
            'order' => 2,
            'permission_name' => 'admin.users',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Galeri',
            'url' => '/admin/media',
            'icon' => 'fas fa-images',
            'parent_id' => null,
            'order' => 4,
            'permission_name' => 'admin.media',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);
        Menu::create([
            'title' => 'Wilayah Sungai',
            'url' => '/admin/wilayah-sungai',
            'icon' => 'fas fa-map',
            'parent_id' => null,
            'order' => 5,
            'permission_name' => 'admin.wilayah_sungai',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);
        Menu::create([
            'title' => 'Titik Pantau',
            'url' => '/admin/titik-pantau',
            'icon' => 'fas fa-map-marker-alt',
            'parent_id' => null,
            'order' => 6,
            'permission_name' => 'admin.tp',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);
        Menu::create([
            'title' => 'Sungai',
            'url' => '/admin/sungai',
            'icon' => 'fas fa-water',
            'parent_id' => null,
            'order' => 7,
            'permission_name' => 'admin.sungai',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);
    }
}
