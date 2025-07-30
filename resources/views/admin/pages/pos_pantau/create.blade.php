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
                <input list="jenis_pos_list" type="text" name="jenis_pos" class="input input-bordered w-full" value="{{ old('jenis_pos') }}" required>
                <datalist id="jenis_pos_list">
                    <option value="Pos Curah Hujan"></option>
                    <option value="Pos Duga Air"></option>
                    <option value="Pos Klimatologi"></option>
                </datalist>
            </div>
            <div>
                <label class="label">Alamat</label>
                <input type="text" name="alamat" class="input input-bordered w-full" value="{{ old('alamat') }}">
            </div>
            <div>
                <label class="label">Kabupaten</label>
                <input type="text" name="kabupaten" class="input input-bordered w-full" value="{{ old('kabupaten') }}">
            </div>
            <div>
                <label class="label">Kecamatan</label>
                <input type="text" name="kecamatan" class="input input-bordered w-full" value="{{ old('kecamatan') }}">
            </div>
            <div>
                <label class="label">Desa</label>
                <input value="{{ old('desa') }}" type="text" name="desa" class="input input-bordered w-full">
            </div>
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
                <input value="{{ old('kewenangan') }}" type="text" name="kewenangan" class="input input-bordered w-full">
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
