@extends('layouts.admin')

@section('title', 'Edit Pos Pantau')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Edit Pos Pantau</h1>
    <form action="{{ route('admin.pos-pengamatan.update', $posPantau) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label" for="nama_pos">Nama Pos</label>
                <input type="text" id="nama_pos" name="nama_pos" class="input input-bordered w-full" 
                       value="{{ old('nama_pos', $posPantau->nama_pos) }}" required>
            </div>
            <div>
                <label class="label" for="jenis_pos">Jenis Pos</label>
                {{-- Pakai komponen select jenis pos agar konsisten --}}
                <x-select-jenis-pos 
                    name="jenis_pos" 
                    :selected="old('jenis_pos', $posPantau->jenis_pos)" 
                    class="w-full" />
            </div>
            
            {{-- Pakai komponen select wilayah yang sudah kamu buat --}}
            <x-select-wilayah 
                :kabupatenId="old('kabupaten_id', $posPantau->kabupaten_id)" 
                :kecamatanId="old('kecamatan_id', $posPantau->kecamatan_id)" 
                :desaId="old('desa_id', $posPantau->desa_id)" />
            
            <div>
                <label class="label" for="nama_pengamat">Nama Pengamat</label>
                <input type="text" id="nama_pengamat" name="nama_pengamat" class="input input-bordered w-full" 
                       value="{{ old('nama_pengamat', $posPantau->nama_pengamat) }}">
            </div>
            <div>
                <label class="label" for="tahun_pembangunan">Tahun Pembangunan</label>
                <input type="number" id="tahun_pembangunan" name="tahun_pembangunan" class="input input-bordered w-full" 
                       value="{{ old('tahun_pembangunan', $posPantau->tahun_pembangunan) }}" min="1900" max="2100" step="1">
            </div>
            <div>
                <label class="label" for="instansi_id">Kewenangan</label>
                {{-- Pakai komponen instansi select agar konsisten --}}
                <x-instansi-select 
                    name="instansi_id" 
                    :selected="old('instansi_id', $posPantau->instansi_id)" 
                    class="w-full" />
            </div>
            <div>
                <label class="label" for="status">Status</label>
                <select id="status" name="status" class="input input-bordered w-full">
                    <option value="aktif" @selected(old('status', $posPantau->status) == 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(old('status', $posPantau->status) == 'nonaktif')>Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <x-map-picker
                label="Alamat Pos Pantau"
                latitude="{{ old('latitude', $posPantau->latitude) }}"
                longitude="{{ old('longitude', $posPantau->longitude) }}"
                class="w-full h-64"
            ></x-map-picker>
            {{-- Tidak perlu input hidden latitude & longitude karena sudah di-handle oleh map-picker --}}
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection
