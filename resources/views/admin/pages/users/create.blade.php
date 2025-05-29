@extends('layouts.admin')

@section('content')

<div class="mx-auto p-4 bg-white shadow-md rounded-lg">
    <h2 class="text-xl font-bold mb-4">Tambah User</h2>
    <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
        @csrf
        <x-input label="Nama" name="name" type="text" :value="old('name')" required class="flex-1" />

        <x-input label="Email" name="email" type="email" :value="old('email')" required class="flex-1" />

        <x-input label="Password" name="password" type="password" required class="flex-1" />

        <x-input label="Konfirmasi Password" name="password_confirmation" type="password" required class="flex-1" />

        <x-select-multiple :isMultiple="true" label="Roles" :name="'roles[]'" :options="$roles->pluck('name', 'name')" multiple required class="flex-1" />


        <div class="form-control flex flex-row justify-end">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>
@endsection
