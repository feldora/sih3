@extends('layouts.admin')

@section('title', 'Manajemen Role & Akses')

@section('content')
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Daftar Role</h1>
            <a href="{{ route('admin.akses-role.create') }}" class="btn btn-primary">+ Tambah Role</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif
        <div class="card bg-white shadow-md rounded-lg">
            <div class="card-body">
                <div class="overflow-x-auto bg-white rounded shadow">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">#</th>
                                <th class="px-4 py-2">Nama Role</th>
                                <th class="px-4 py-2 w-1/3">Permissions</th>
                                <th class="px-4 py-2 w-1/6">Members</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 font-semibold">{{ $role->name }}</td>
                                    <td class="px-4 py-2">
                                        @foreach ($role->permissions as $perm)
                                            <span class="badge badge-info mr-1 mb-1">{{ $perm->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-2">
                                        @if ($role->users->count() > 0)
                                            <div class="tooltip">
                                                <div class="tooltip-content">
                                                    <div class="">
                                                        <ul>
                                                            @foreach ($role->users as $user)
                                                                <li class="mb-1">
                                                                        {{ $user->name }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                                {{-- <button class="btn "> --}}
                                                    <i class="fas fa-users"></i>
                                                    {{ $role->users->count() }} Anggota
                                                {{-- </button> --}}
                                            </div>
                                        @else
                                            <span class="badge badge-warning">Tidak ada anggota</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('admin.akses-role.edit', $role->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.akses-role.destroy', $role->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-error"
                                                onclick="return confirm('Yakin hapus role ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
