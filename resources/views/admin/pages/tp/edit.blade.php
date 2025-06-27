@extends('layouts.admin')

@section('content')
<div class="mx-auto p-4 bg-white shadow-md rounded-lg">
    <h1 class="text-xl font-semibold mb-4">Edit Titik Pantau</h1>

    <form action="{{ route('admin.titik-pantau.update', $titikPantau->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="nama_titik" class="block text-sm font-medium text-gray-700">Nama Titik</label>
            <input type="text" name="nama_titik" id="nama_titik" value="{{ old('nama_titik', $titikPantau->nama_titik) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('nama_titik') border-red-500 @enderror" required>
            @error('nama_titik')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
            <input type="text" name="alamat" id="alamat" value="{{ old('alamat', $titikPantau->alamat) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('alamat') border-red-500 @enderror">
            @error('alamat')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $titikPantau->keterangan) }}</textarea>
            @error('keterangan')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="pos_pantau_id" class="block text-sm font-medium text-gray-700">Pos Pantau</label>
            <select name="pos_pantau_id" id="pos_pantau_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('pos_pantau_id') border-red-500 @enderror">
                <option value="" selected disabled>Pilih Pos Pantau</option>
                @foreach($posPantau as $pos)
                <option value="{{ $pos->id }}" {{ old('pos_pantau_id', $titikPantau->pos_pantau_id) == $pos->id ? 'selected' : '' }}>{{ $pos->nama }}</option>
                @endforeach
            </select>
            @error('pos_pantau_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="wilayah_sungai_id" class="block text-sm font-medium text-gray-700">Wilayah Sungai</label>
            <select name="wilayah_sungai_id" id="wilayah_sungai_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('wilayah_sungai_id') border-red-500 @enderror">
                <option value="" selected disabled>Pilih Wilayah Sungai</option>
                @foreach($wilayahSungai as $wilayah)
                <option value="{{ $wilayah->id }}" {{ old('wilayah_sungai_id', $titikPantau->wilayah_sungai_id) == $wilayah->id ? 'selected' : '' }}>{{ $wilayah->name }}</option>
                @endforeach
            </select>
            @error('wilayah_sungai_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="kategori_id" class="block text-sm font-medium text-gray-700">Kategori</label>
            <select name="kategori_id" id="kategori_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('kategori_id') border-red-500 @enderror">
                <option value="" selected disabled>Pilih Kategori</option>
                @foreach($kategori as $kat)
                <option value="{{ $kat->id }}" {{ old('kategori_id', $titikPantau->kategori_id) == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                @endforeach
            </select>
            @error('kategori_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('status') border-red-500 @enderror">
                <option value="aktif" {{ old('status', $titikPantau->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ old('status', $titikPantau->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <x-map-picker :latitude="old('latitude', $titikPantau->latitude)" :longitude="old('longitude', $titikPantau->longitude)" />
        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection
