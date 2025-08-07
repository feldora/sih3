@extends('layouts.admin')

@section('title', 'Tambah Pos Pantau')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Tambah Pos Pantau</h1>
    <form action="{{ route('admin.pos-pengamatan.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label">Nama Pos</label>
                <input type="text" name="nama_pos" class="input input-bordered w-full" value="{{ old('nama_pos') }}" required>
            </div>
            <div>
                <label class="label">Jenis Pos</label>
                <x-select-jenis-pos name="jenis_pos"
                    :selected="old('jenis_pos', $item->jenis_pos ?? '')"
                    class="w-full" />
            </div>
            <x-select-wilayah :kabupatenId="old('kabupaten_id',$item->kabupaten_id ?? null)"
                      :kecamatanId="old('kecamatan_id',$item->kecamatan_id ?? null)"
                      :desaId="old('desa_id',$item->desa_id ?? null)" />
                      
            <div>
                <label class="label">Nama Pengamat</label>
                <input value="{{ old('nama_pengamat') }}" type="text" name="nama_pengamat" class="input input-bordered w-full">
            </div>
            <div>
                <label class="label">Tahun Pembangunan</label>
                <input value="{{ old('tahun_pembangunan') }}" type="number" name="tahun_pembangunan" class="input input-bordered w-full">
            </div>
            <div>
                <label class="label">Kewenangan</label>
                <x-instansi-select name="instansi_id" :selected="old('instansi_id', $data->instansi_id ?? null)" class="w-full" />
            </div>
            <div>
                <label class="label">Status</label>
                <select name="status" class="input input-bordered w-full">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <x-map-picker
                label="Alamat Pos Pantau"
                latitude="{{ old('latitude', '') }}"
                longitude="{{ old('longitude', '') }}"
                class="w-full h-64"
            ></x-map-picker>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>
@endsection
