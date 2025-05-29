@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">

    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Edit Menu</h2>

    <!-- Form Edit Menu -->
    <form action="{{ route('menus.update', $menu) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Nama Menu -->
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Nama Menu</label>
            <input type="text" name="title" id="title" value="{{ old('title', $menu->title) }}" class="form-input mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            @error('title')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- URL -->
        <div class="mb-4">
            <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
            <input type="text" name="url" id="url" value="{{ old('url', $menu->url) }}" class="form-input mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            @error('url')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Icon -->
        <x-input-icon label="Icon (Optional)" name="icon" :value="old('icon', $menu->icon)" />
            
        <!-- Parent Menu -->
        <div class="mb-4">
            <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Menu</label>
            <select name="parent_id" id="parent_id" class="form-select mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Parent Menu --</option>
                @foreach($menus as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : '' }}>
                        {{ $parent->title }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Order (Urutan Menu) -->
        <div class="mb-4">
            <label for="order" class="block text-sm font-medium text-gray-700">Order</label>
            <input type="number" name="order" id="order" value="{{ old('order', $menu->order) }}" class="form-input mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="1">
            @error('order')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Permission Name -->
        <div class="mb-4">
            <label for="permission_name" class="block text-sm font-medium text-gray-700">Permission Name (Optional)</label>
            <input type="text" name="permission_name" id="permission_name" value="{{ old('permission_name', $menu->permission_name) }}" class="form-input mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('permission_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Menu Type -->
        <div class="mb-4">
            <label for="menu_type" class="block text-sm font-medium text-gray-700">Tipe Menu</label>
            <select name="menu_type" id="menu_type" class="form-select mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="admin" {{ old('menu_type', $menu->menu_type) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="public" {{ old('menu_type', $menu->menu_type) == 'public' ? 'selected' : '' }}>Public</option>
            </select>
            @error('menu_type')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Tombol Submit -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('menus.index') }}" class="btn btn-sm btn-secondary px-4 py-2 text-white bg-gray-500 hover:bg-gray-600 rounded-md">Batal</a>
            <button type="submit" class="btn btn-sm btn-primary px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-md">Simpan</button>
        </div>
    </form>
</div>
@endsection
