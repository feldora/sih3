@extends('layouts.admin')

@section('content')

<div class="mx-auto p-4 bg-white shadow-md rounded-lg">
    <h2 class="text-xl font-bold mb-4">Edit User</h2>
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <x-input label="Nama" name="name" type="text" :value="old('name', $user->name)" required class="flex-1" />

        <x-input label="Email" name="email" type="email" :value="old('email', $user->email)" required class="flex-1" />

        <x-input label="Password (Biarkan kosong jika tidak ingin mengubah)" name="password" type="password" class="flex-1" />

        <x-input label="Konfirmasi Password" name="password_confirmation" type="password" class="flex-1" />

        {{-- <x-select-multiple label="Roles" :name="'roles[]'" :options="$roles->pluck('name', 'name')" :value="old('roles', $user->roles->pluck('name')->toArray())" required class="flex-1" /> --}}
        <x-choices-multiple
            name="roles"
            label="Roles"
            :options="$roles->map(function($role) {
            return ['value' => $role->name, 'label' => $role->name];
            })->toArray()"
            :selected="old('roles', $user->roles->pluck('name')->toArray())"
            placeholder="Pilih roles..."
            required
            class="flex-1"
        />

        <div class="form-control flex flex-row justify-end">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection