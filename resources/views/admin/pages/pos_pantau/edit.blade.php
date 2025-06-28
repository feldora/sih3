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
                <label class="label">Nama Pos</label>
                <input type="text" name="nama_pos" class="input input-bordered w-full" value="{{ $posPantau->nama_pos }}" required>
            </div>
            <div>
                <label class="label">Jenis Pos</label>
                <input type="text" name="jenis_pos" class="input input-bordered w-full" value="{{ $posPantau->jenis_pos }}" required>
            </div>
            <div>
                <label class="label">Alamat</label>
                <input type="text" name="alamat" class="input input-bordered w-full" value="{{ $posPantau->alamat }}">
            </div>
            <div>
                <label class="label">Kabupaten</label>
                <input type="text" name="kabupaten" class="input input-bordered w-full" value="{{ $posPantau->kabupaten }}">
            </div>
            <div>
                <label class="label">Kecamatan</label>
                <input type="text" name="kecamatan" class="input input-bordered w-full" value="{{ $posPantau->kecamatan }}">
            </div>
            <div>
                <label class="label">Desa</label>
                <input type="text" name="desa" class="input input-bordered w-full" value="{{ $posPantau->desa }}">
            </div>
            <div>
                <label class="label">Nama Pengamat</label>
                <input type="text" name="nama_pengamat" class="input input-bordered w-full" value="{{ $posPantau->nama_pengamat }}">
            </div>
            <div>
                <label class="label">Tahun Pembangunan</label>
                <input type="number" name="tahun_pembangunan" class="input input-bordered w-full" value="{{ $posPantau->tahun_pembangunan }}" min="1900" max="2100" step="1">
            </div>
            <div>
                <label class="label">Kewenangan</label>
                <input type="text" name="kewenangan" class="input input-bordered w-full" value="{{ $posPantau->kewenangan }}">
            </div>
            <div>
                <label class="label">Status</label>
                <select name="status" class="input input-bordered w-full">
                    <option value="aktif" @if($posPantau->status=='aktif') selected @endif>Aktif</option>
                    <option value="nonaktif" @if($posPantau->status=='nonaktif') selected @endif>Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="">
            <x-map-picker
                latitude="{{ $posPantau->latitude }}"
                longitude="{{ $posPantau->longitude }}"
                class="w-full h-64"
            ></x-map-picker>
            {{-- input hidden latitude & longitude dihapus karena sudah di-handle oleh map-picker --}}
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection
