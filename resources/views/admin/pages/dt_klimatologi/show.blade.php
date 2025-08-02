@extends('layouts.admin')

@section('title', 'Detail Data Klimatologi')

@section('content')
<div class="container mx-auto px-4">
    <div class="breadcrumbs text-sm mb-6">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a></li>
            <li><a href="{{ route('admin.hidrologi.klimatologi.index') }}" class="text-primary">Data Klimatologi</a></li>
            <li>Detail Data</li>
        </ul>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-header px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="card-title text-xl font-bold">Detail Data Klimatologi</h3>
                <a href="{{ route('admin.hidrologi.klimatologi.index') }}" class="btn btn-ghost btn-sm">
                    <x-icons.back /> Kembali
                </a>
            </div>
        </div>

        <div class="card-body p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-readonly-input label="Pos Pantau" :value="$dataKlimatologi->posPantau->nama ?? '-'" />
                <x-readonly-input label="Tanggal" :value="$dataKlimatologi->tanggal" />
                <x-readonly-input label="Jam" :value="$dataKlimatologi->jam" />
                <x-readonly-input label="Kecepatan Angin" :value="$dataKlimatologi->kecepatan_angin . ' m/s'" />
                <x-readonly-input label="Arah Angin" :value="$dataKlimatologi->arah_angin" />
                <x-readonly-input label="Kelembapan" :value="$dataKlimatologi->kelembapan . ' %'" />
                <x-readonly-input label="Suhu" :value="$dataKlimatologi->suhu . ' °C'" />
                <x-readonly-input label="Curah Hujan" :value="$dataKlimatologi->curah_hujan . ' mm'" />
            </div>

            @if($dataKlimatologi->keterangan)
                <div class="mt-6">
                    <h4 class="font-semibold mb-2">Keterangan:</h4>
                    <div class="prose max-w-none">
                        {!! $dataKlimatologi->keterangan !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
