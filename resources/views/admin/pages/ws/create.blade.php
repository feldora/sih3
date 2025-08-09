@extends('layouts.admin')

@section('content')
<div class="mx-auto p-4 bg-white shadow-md rounded-lg">
    <h1 class="text-xl font-semibold mb-4">Tambah Wilayah Sungai</h1>
    <form action="{{ route('admin.wilayah-sungai.store') }}" method="POST" class="loadShp">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text" name="name" id="name" class="auto-mapping mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
        </div>
        <div class="mb-4">
            <label class="label">Kewenangan</label>
            <x-instansi-select name="instansi_id" :selected="old('instansi_id', $data->instansi_id ?? null)" class="w-full" />
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="description" id="description" rows="3" class="auto-mapping mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
        </div>
        <x-map-area-picker class="auto-mapping" :oldCoordinates="old('coordinates', isset($wilayahSungai) && $wilayahSungai && $wilayahSungai->geojson ? json_decode($wilayahSungai->geojson)->features[0]->geometry->coordinates : [])"/>
        
        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

@endsection
