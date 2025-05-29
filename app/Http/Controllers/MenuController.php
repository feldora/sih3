<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $menus = Menu::with('parent')->paginate(12);
        $parents = Menu::whereNotNull('parent_id')->get();

        return view('admin.pages.menu.index', compact('menus', 'parents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menus = Menu::whereNull('parent_id')->get();  // Ambil menu yang tidak memiliki parent
        return view('admin.pages.menu.create', compact('menus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'required|integer|min:1',
            'permission_name' => 'nullable|string|max:255',
            'menu_type' => 'required|in:admin,public',
        ]);

        Menu::create($validated);

        return redirect()->route('menus.index')->with('success', 'Menu berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $menu = Menu::findOrFail($id);
        return view('admin.pages.menu.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit(Menu $menu)
{
    // Ambil semua menu untuk dropdown parent
    $menus = Menu::whereNull('parent_id')->get();  // Menu yang tidak memiliki parent
    return view('admin.pages.menu.edit', compact('menu', 'menus'));
}

public function update(Request $request, Menu $menu)
{
    // Validasi data
    $request->validate([
        'title' => 'required|string|max:255',
        'url' => 'required|string|max:255',
        'icon' => 'nullable|string|max:255',
        'parent_id' => 'nullable|exists:menus,id',
        'order' => 'required|integer|min:1',
        'permission_name' => 'nullable|string|max:255',
        'menu_type' => 'required|in:admin,public',
    ]);

    // Update menu dengan data baru
    $menu->update([
        'title' => $request->title,
        'url' => $request->url,
        'icon' => $request->icon,
        'parent_id' => $request->parent_id,
        'order' => $request->order,
        'permission_name' => $request->permission_name,
        'menu_type' => $request->menu_type,
    ]);

    return redirect()->route('menus.index')->with('success', 'Menu berhasil diperbarui!');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully.');
    }
}
