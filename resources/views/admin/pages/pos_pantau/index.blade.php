@extends('layouts.admin')

@section('title', 'Daftar Pos Pantau')

@section('content')
<div class="contmx-auto p-4 bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-4">Daftar Pos Pantau</h1>
    <div class="flex justify-end items-center mb-6 gap-5">
        <a href="{{ route('admin.loadshp.index') }}?type=Pos Pantau" class="btn btn-sm mb-4 btn-info">Tambah Dari SHP</a>
        <a href="{{ route('admin.pos-pengamatan.import-excel') }}" class="btn btn-sm mb-4 btn-success">Tambah Dari Excel</a>
        <a href="{{ route('admin.pos-pengamatan.create') }}" class="btn btn-sm mb-4 btn-primary">+ Tambah Pos Pantau</a>
    </div>
    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Pos</th>
                    <th>Jenis Pos</th>
                    <th>Alamat</th>
                    <th>Kabupaten</th>
                    <th>Kewenangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posPantau as $pos)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pos->nama_pos }}</td>
                    <td>{{ $pos->jenis_pos }}</td>
                    <td>{{ $pos->alamat }}</td>
                    <td>{{ $pos->kabupaten->nama }}</td>
                    <td>{{ $pos->kewenangan }}</td>
                    <td>{{ $pos->status }}</td>
                    <td>
                        <a href="{{ route('admin.pos-pengamatan.edit', $pos) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.pos-pengamatan.destroy', $pos->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-error" onclick="return confirm('Yakin hapus pos ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection