<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Menu;
use App\Models\Menu;
// use Spatie\Permission\Models\User;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class AksesRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all roles with their permissions
        $roles = Role::with('permissions')->get();

        // Get all permissions
        $permissions = Permission::all();

        // Get all menus
        $menus = Menu::all();

        // Get all users
        $users = User::with('roles')->get();

        return view('admin.pages.akses_role.index', compact('roles', 'permissions', 'menus', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all roles with their permissions
        $roles = Role::with('permissions')->get();

        // Get all permissions
        $permissions = Permission::all();

        // Get all menus
        $menus = Menu::all();

        // Get all users
        $users = User::with('roles')->get();

        return view('admin.pages.akses_role.create', compact('permissions', 'menus', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = Role::create($request->only('name'));

        // Sync permissions
        $role->syncPermissions($request->input('permissions', []));

        // Sync members
        $role->users()->sync($request->input('members', []));

        return redirect()->route('admin.akses-role.index')->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Temukan role berdasarkan ID
        $role = Role::with('permissions', 'users')->findOrFail($id);
        
        // Ambil semua permissions, menus, dan users
        $permissions = Permission::all();
        $menus = Menu::all();
        $users = User::all();

        return view('admin.pages.akses_role.edit', compact('role', 'permissions', 'menus', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'members' => 'array',
        ]);

        // Temukan role berdasarkan ID
        $role = Role::findOrFail($id);

        // Update nama role
        $role->name = $request->input('name');
        $role->save();

        // Sync permissions
        $role->syncPermissions($request->input('permissions', []));

        // Sync members
        $role->users()->sync($request->input('members', []));

        return redirect()->route('admin.akses-role.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Temukan role berdasarkan ID
        $role = Role::with('permissions', 'users')->findOrFail($id);

        // Hapus role dan semua permissions yang terkait
        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return redirect()->route('admin.akses-role.index')->with('success', 'Role deleted successfully.');
    }
}
