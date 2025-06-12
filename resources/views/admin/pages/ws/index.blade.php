@extends('layouts.admin')

@section('content')
<div class="mx-auto p-4 bg-white shadow-md rounded-lg">
    <a href="{{ route('admin.wilayah-sungai.create') }}" class="btn btn-primary mb-4">Tambah Wilayah Sungai</a>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border border-gray-300 p-2">Nama</th>
                <th class="border border-gray-300 p-2">Deskripsi</th>
                <th class="border border-gray-300 p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($wilayahSungais as $wilayahSungai)
            <tr>
                <td class="border border-gray-300 p-2">{{ $wilayahSungai->name }}</td>
                <td class="border border-gray-300 p-2">{{ $wilayahSungai->description }}</td>
                <td class="border border-gray-300 p-2">
                    <a href="{{ route('admin.wilayah-sungai.edit', $wilayahSungai) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.wilayah-sungai.destroy', $wilayahSungai) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin mau hapus?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $wilayahSungais->links() }}
    </div>

@endsection