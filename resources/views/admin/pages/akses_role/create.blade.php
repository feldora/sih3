@extends('layouts.admin')

@section('title', 'Tambah Role & Akses')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Tambah Role</h1>
        <form action="{{ route('admin.akses-role.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium mb-1">Nama Role</label>
                <input type="text" name="name" id="name" class="form-input w-full" required>
            </div>
            
            <x-choices-multiple
                name="permissions"
                label="Permissions"
                :options="$permissions->map(function($permission) {
                    return ['value' => $permission->name, 'label' => $permission->name];
                })->toArray()"
                :selected="old('permissions', [])"
                placeholder="Select permissions..."
            />
            
            <x-choices-multiple
                name="members"
                label="Members"
                :options="$users->map(function($user) {
                    return ['value' => $user->id, 'label' => $user->name];
                })->toArray()"
                :selected="old('members', [])"
                placeholder="Select members..."
            />
            

            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
@endsection