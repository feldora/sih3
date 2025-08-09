@extends('layouts.admin')

@section('content')
<div class="mx-auto p-4 bg-white shadow-md rounded-lg">
    <h1 class="text-xl font-semibold mb-4">Edit Wilayah Sungai</h1>

    <form action="{{ route('admin.wilayah-sungai.update', $wilayahSungai->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name', $wilayahSungai->name) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
        </div>
        <div class="mb-4">
            <label class="label" for="instansi_id">Kewenangan</label>
            <x-instansi-select 
                name="instansi_id" 
                :selected="old('instansi_id', $wilayahSungai->instansi_id)" 
                class="w-full" />
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="description" id="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('description', $wilayahSungai->description) }}</textarea>
        </div>
        
        <x-map-area-picker :oldCoordinates="old('coordinates', $wilayahSungai->feature->geojson['coordinates'][0] ?? [])"/>
        
        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection
