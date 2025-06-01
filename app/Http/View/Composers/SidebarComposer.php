<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class SidebarComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();

        // Mendapatkan menu admin dengan filter berdasarkan permission
        $menus_admin = Menu::whereNull('parent_id')
            ->where('menu_type', 'admin')  // Pindahkan 'where' untuk 'menu_type' ke query
            ->orderBy('order')
            ->with('children') // Memuat relasi children tanpa filter
            ->get()
            ->filter(function ($menu) use ($user) {
                // Filter berdasarkan permission untuk menu utama
                if ($menu->permission_name) {
                    return $user && method_exists($user, 'can') && $user->can($menu->permission_name);
                }
                return true;
            });

        // Mendapatkan menu public tanpa filter permission
        $menus_public = Menu::whereNull('parent_id')
            ->where('menu_type', 'public')  // Pindahkan 'where' untuk 'menu_type' ke query
            ->orderBy('order')
            ->with('children')  // Tidak ada filtering untuk public menu
            ->get();

        // Kirimkan data ke view
        $view->with([
            'menus_public' => $menus_public,
            'menus_admin' => $menus_admin
        ]);
    }
}
 