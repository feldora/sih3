<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $order = 0;
        // Menu Publik
        $informasiH3 = Menu::create([
            'title' => 'Informasi H3',
            'url' => '/informasi-h3',
            'icon' => 'fas fa-info-circle',
            'parent_id' => null,
            'order' => $order++,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'parent',
        ]);

        Menu::create([
            'title' => 'Info H3',
            'url' => '/informasi-h3/info',
            'icon' => 'fas fa-info',
            'parent_id' => $informasiH3->id,
            'order' => $order++,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Data H3',
            'url' => '/informasi-h3/data',
            'icon' => 'fas fa-database',
            'parent_id' => $informasiH3->id,
            'order' => $order++,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Neraca Air',
            'url' => '/informasi-h3/neraca-air',
            'icon' => 'fas fa-water',
            'parent_id' => $informasiH3->id,
            'order' => $order++,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'child',
        ]);

        $geospasial = Menu::create([
            'title' => 'Geospasial',
            'url' => '/geospasial',
            'icon' => 'fas fa-globe',
            'parent_id' => null,
            'order' => $order++,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'parent',
        ]);
        $geospasial->orderChildren = 0;

        Menu::create([
            'title' => 'Peta Geospasial',
            'url' => '/geospasial/peta',
            'icon' => 'fas fa-map',
            'parent_id' => $geospasial->id,
            'order' => $geospasial->orderChildren++,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Monitoring Data',
            'url' => '/geospasial/monitoring',
            'icon' => 'fas fa-chart-line',
            'parent_id' => $geospasial->id,
            'order' => $geospasial->orderChildren++,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Produk Hukum',
            'url' => '/produk-hukum',
            'icon' => 'fas fa-gavel',
            'parent_id' => null,
            'order' => $order++,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'link',
        ]);

        Menu::create([
            'title' => 'Kontak',
            'url' => '/kontak',
            'icon' => 'fas fa-envelope',
            'parent_id' => null,
            'order' => $order++,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'link',
        ]);

        Menu::create([
            'title' => 'Artikel',
            'url' => '/artikel',
            'icon' => 'fas fa-file-alt',
            'parent_id' => null,
            'order' => $order++,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'link',
        ]);

        Menu::create([
            'title' => 'Berita',
            'url' => '/berita',
            'icon' => 'fas fa-bullhorn',
            'parent_id' => null,
            'order' => $order++,
            'permission_name' => null,
            'zona' => 'public',
            'menu_type' => 'link',
        ]);

        // Menu Admin
        $adminMenuOrder = 0;
        Menu::create([
            'title' => 'Dashboard',
            'url' => '/admin/dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'parent_id' => null,
            'order' => $adminMenuOrder++,
            'permission_name' => 'dashboard',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);
        Menu::create([
            'title' => 'Postingan',
            'url' => '/admin/posts',
            'icon' => 'fas fa-newspaper',
            'parent_id' => null,
            'order' => $adminMenuOrder++,
            'permission_name' => 'posts',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);

        $pengaturan = Menu::create([
            'title' => 'Pengaturan',
            'url' => '/admin/settings',
            'icon' => 'fas fa-cogs',
            'parent_id' => null,
            'order' => 999,
            'permission_name' => 'settings',
            'zona' => 'admin',
            'menu_type' => 'parent',
        ]);
        $pengaturan->orderChildren = 0;
        Menu::create([
            'title' => 'Manajemen Menu',
            'url' => '/admin/menus',
            'icon' => 'fas fa-list',
            'parent_id' => $pengaturan->id,
            'order' => $pengaturan->orderChildren++,
            'permission_name' => 'menus',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Manajemen Pengguna',
            'url' => '/admin/users',
            'icon' => 'fas fa-users',
            'parent_id' => $pengaturan->id,
            'order' => $pengaturan->orderChildren++,
            'permission_name' => 'users',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);
        Menu::create([
            'title' => 'Manajemen Peran',
            'url' => '/admin/akses-role',
            'icon' => 'fas fa-user-shield',
            'parent_id' => $pengaturan->id,
            'order' => $pengaturan->orderChildren++,
            'permission_name' => 'roles',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Galeri',
            'url' => '/admin/media',
            'icon' => 'fas fa-images',
            'parent_id' => null,
            'order' => $adminMenuOrder++,
            'permission_name' => 'media',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);
        Menu::create([
            'title' => 'Pos Pengamatan',
            'url' => '/admin/pos-pengamatan',
            'icon' => 'fas fa-binoculars',
            'parent_id' => null,
            'order' => $adminMenuOrder++,
            'permission_name' => 'pos_pengamatan',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);

        Menu::create([
            'title' => 'Wilayah Sungai',
            'url' => '/admin/wilayah-sungai',
            'icon' => 'fas fa-map',
            'parent_id' => null,
            'order' => $adminMenuOrder++,
            'permission_name' => 'wilayah_sungai',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);
        Menu::create([
            'title' => 'Titik Pantau',
            'url' => '/admin/titik-pantau',
            'icon' => 'fas fa-map-marker-alt',
            'parent_id' => null,
            'order' => $adminMenuOrder++,
            'permission_name' => 'titik_pantau',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);
        Menu::create([
            'title' => 'Sungai',
            'url' => '/admin/sungai',
            'icon' => 'fas fa-water',
            'parent_id' => null,
            'order' => $adminMenuOrder++,
            'permission_name' => 'sungai',
            'zona' => 'admin',
            'menu_type' => 'link',
        ]);

        // Add Data Hidrologi menu and children
        $dataHidrologi = Menu::create([
            'title' => 'Data Hidrologi',
            'url' => '/admin/hidrologi',
            'icon' => 'fas fa-water',
            'parent_id' => null,
            'order' => $adminMenuOrder++,
            'permission_name' => 'hidrologi',
            'zona' => 'admin',
            'menu_type' => 'parent',
        ]);

        Menu::create([
            'title' => 'Data Tinggi Muka Air',
            'url' => '/admin/hidrologi/tinggi-muka-air',
            'icon' => 'fas fa-water',
            'parent_id' => $dataHidrologi->id,
            'order' => 1,
            'permission_name' => 'tinggi_muka_air',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Data Debit',
            'url' => '/admin/hidrologi/debit',
            'icon' => 'fas fa-tint',
            'parent_id' => $dataHidrologi->id,
            'order' => 2,
            'permission_name' => 'debit',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Data Sedimen',
            'url' => '/admin/hidrologi/sedimen',
            'icon' => 'fas fa-layer-group',
            'parent_id' => $dataHidrologi->id,
            'order' => 3,
            'permission_name' => 'sedimen',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        // Add Data Meteorologi menu and child
        $dataMeteo = Menu::create([
            'title' => 'Data Meteorologi',
            'url' => '/admin/meteorologi',
            'icon' => 'fas fa-cloud',
            'parent_id' => null,
            'order' => $adminMenuOrder++,
            'permission_name' => 'meteorologi',
            'zona' => 'admin',
            'menu_type' => 'parent',
        ]);

        Menu::create([
            'title' => 'Data Curah Hujan',
            'url' => '/admin/meteorologi/curah-hujan',
            'icon' => 'fas fa-cloud-rain',
            'parent_id' => $dataMeteo->id,
            'order' => 1,
            'permission_name' => 'curah_hujan',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        // Add Data Geologi menu and children
        $dataGeologi = Menu::create([
            'title' => 'Data Geologi',
            'url' => '/admin/geologi',
            'icon' => 'fas fa-mountain',
            'parent_id' => null,
            'order' => $adminMenuOrder++,
            'permission_name' => 'geologi',
            'zona' => 'admin',
            'menu_type' => 'parent',
        ]);

        Menu::create([
            'title' => 'Data Muka Air Tanah',
            'url' => '/admin/geologi/muka-air-tanah',
            'icon' => 'fas fa-water',
            'parent_id' => $dataGeologi->id,
            'order' => 1,
            'permission_name' => 'muka_air_tanah',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Data Minatan Hidrogeologi',
            'url' => '/admin/geologi/minatan-hidrogeologi',
            'icon' => 'fas fa-map',
            'parent_id' => $dataGeologi->id,
            'order' => 2,
            'permission_name' => 'minatan_hidrogeologi',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Data Kualitas Air Tanah',
            'url' => '/admin/geologi/kualitas-air-tanah',
            'icon' => 'fas fa-flask',
            'parent_id' => $dataGeologi->id,
            'order' => 3,
            'permission_name' => 'kualitas_air_tanah',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Cekungan Air Tanah',
            'url' => '/admin/geologi/cekungan-air-tanah',
            'icon' => 'fas fa-chart-area',
            'parent_id' => $dataGeologi->id,
            'order' => 4,
            'permission_name' => 'cekungan_air_tanah',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);

        Menu::create([
            'title' => 'Data Hidrogeologi',
            'url' => '/admin/geologi/hidrogeologi',
            'icon' => 'fas fa-water',
            'parent_id' => $dataGeologi->id,
            'order' => 5,
            'permission_name' => 'hidrogeologi',
            'zona' => 'admin',
            'menu_type' => 'child',
        ]);
    }
}
