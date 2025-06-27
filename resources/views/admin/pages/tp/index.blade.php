@extends('layouts.admin')

@section('content')
<div class="mx-auto p-4 bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-4">Daftar Titik Pantau</h1>
    
    
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('admin.titik-pantau.create') }}" class="btn btn-primary btn-sm">Tambah Titik Pantau</a>
        <form method="GET" action="{{ route('admin.titik-pantau.index') }}" class="flex items-center">
            <input type="text" name="search" class="form-input mr-2 p-2 border border-gray-300 rounded-lg text-sm" placeholder="Cari Titik Pantau..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border border-gray-300 p-2">Nama Titik</th>
                <th class="border border-gray-300 p-2">Alamat</th>
                <th class="border border-gray-300 p-2">Latitude</th>
                <th class="border border-gray-300 p-2">Longitude</th>
                <th class="border border-gray-300 p-2">Keterangan</th>
                <th class="border border-gray-300 p-2">Status</th>
                <th class="border border-gray-300 p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($titikPantau as $titik)
            <tr>
                <td class="border border-gray-300 p-2">{{ $titik->nama_titik }}</td>
                <td class="border border-gray-300 p-2">{{ $titik->alamat }}</td>
                <td class="border border-gray-300 p-2">{{ $titik->latitude }}</td>
                <td class="border border-gray-300 p-2">{{ $titik->longitude }}</td>
                <td class="border border-gray-300 p-2">{{ $titik->keterangan }}</td>
                <td class="border border-gray-300 p-2">
                    @if($titik->status)
                        <span class="text-green-600 font-semibold">Aktif</span>
                    @else
                        <span class="text-red-600 font-semibold">Nonaktif</span>
                    @endif
                </td>
                <td class="border border-gray-300 p-2">
                    <a href="{{ route('admin.titik-pantau.edit', $titik) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.titik-pantau.destroy', $titik) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin mau hapus titik pantau ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="border border-gray-300 p-2 text-center">Belum ada data Titik Pantau.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $titikPantau->links() }}
    </div>
</div>
@endsection
