@extends('layouts.admin')

@section('content')
<div class="mx-auto p-4 bg-white shadow-md rounded-lg">
    <h1 class="text-xl font-semibold mb-4">Tambah Titik Pantau</h1>

    <form action="{{ route('admin.titik-pantau.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="nama_titik" class="block text-sm font-medium text-gray-700">Nama Titik</label>
            <input type="text" name="nama_titik" id="nama_titik" value="{{ old('nama_titik') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('nama_titik') border-red-500 @enderror" required>
            @error('nama_titik')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
            <input type="text" name="alamat" id="alamat" value="{{ old('alamat') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('alamat') border-red-500 @enderror">
            @error('alamat')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
            @error('keterangan')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <x-choices 
            name="pos_pantau_id" 
            :options="$posPantau->map(fn($pos) => ['value' => $pos->id, 'label' => $pos->nama_pos])->toArray()" 
            label="Pos Pantau" 
            :selected="old('pos_pantau_id')" 
        />
        
        <x-choices 
            name="wilayah_sungai_id" 
            :options="$wilayahSungai->map(fn($ws) => ['value' => $ws->id, 'label' => $ws->name])->toArray()" 
            label="Wilayah Sungai" 
            :selected="old('wilayah_sungai_id')" 
        />
        
        <x-choices 
            name="kategori_id" 
            :options="$kategori->map(fn($kat) => ['value' => $kat->id, 'label' => $kat->name])->toArray()" 
            label="Kategori" 
            :selected="old('kategori_id')" 
        />

        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('status') border-red-500 @enderror">
                <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <x-map-picker :latitude="old('latitude')" :longitude="old('longitude')" />
        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>
@endsection


@push('scripts')

@endpush