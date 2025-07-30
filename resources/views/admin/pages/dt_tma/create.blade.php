@extends('layouts.admin')

@section('title', 'Tambah Data Tinggi Muka Air')

@section('content')
<div class="container mx-auto px-4">
    <!-- Breadcrumb -->
    <div class="breadcrumbs text-sm mb-6">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a></li>
            <li><a href="{{ route('admin.hidrologi.tma.index') }}" class="text-primary">Data TMA</a></li>
            <li>Tambah Data</li>
        </ul>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <!-- Card Header -->
        <div class="card-header px-6 py-4 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="card-title text-xl font-bold">Tambah Data Tinggi Muka Air</h3>
                <a href="{{ route('admin.hidrologi.tma.index') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-6">
            <!-- Success Alert -->
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error Alert -->
            @if($errors->any())
                <div class="alert alert-error mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">Terjadi kesalahan validasi:</h3>
                        <ul class="list-disc list-inside mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.hidrologi.tma.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pos Pantau -->
                    <x-dropdown-tom-select
                        name="pos_pantau_id" 
                        label="Pos Pantau"
                        ajaxUrl="/api/pos-pantau"
                        SearchFlter="jenis_pos=Pos TMA"
                        :multiple="false"
                        :selected="[]"
                    />

                    <!-- Tanggal -->
                    <div class="form-control w-full">
                        <label class="label" for="tanggal">
                            <span class="label-text font-medium">Tanggal <span class="text-error">*</span></span>
                        </label>
                        <input type="date" name="tanggal" id="tanggal" 
                               value="{{ old('tanggal', date('Y-m-d')) }}"
                               class="input input-bordered w-full @error('tanggal') input-error @enderror">
                        @error('tanggal')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Jam -->
                    <div class="form-control w-full">
                        <label class="label" for="jam">
                            <span class="label-text font-medium">Jam <span class="text-error">*</span></span>
                        </label>
                        <input type="time" name="jam" id="jam"
                               value="{{ old('jam', date('H:i')) }}"
                               class="input input-bordered w-full @error('jam') input-error @enderror">
                        @error('jam')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Tinggi Muka Air -->
                    <div class="form-control w-full">
                        <label class="label" for="tinggi_muka_air">
                            <span class="label-text font-medium">Tinggi Muka Air (cm) <span class="text-error">*</span></span>
                        </label>
                        <div class="relative">
                            <input type="number" name="tinggi_muka_air" id="tinggi_muka_air"
                                   value="{{ old('tinggi_muka_air') }}" min="0" step="1"
                                   class="input input-bordered w-full pr-12 @error('tinggi_muka_air') input-error @enderror"
                                   placeholder="Masukkan angka">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-base-content/70">cm</span>
                        </div>
                        @error('tinggi_muka_air')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="form-control w-full">
                    <label class="label" for="keterangan">
                        <span class="label-text font-medium">Keterangan</span>
                    </label>
                    <textarea name="keterangan" id="keterangan"
                              class="textarea textarea-bordered w-full @error('keterangan') textarea-error @enderror"
                              rows="4" placeholder="Masukkan keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-base-300">
                    <button type="submit" class="btn btn-primary flex-1 sm:flex-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Data
                    </button>

                    <a href="{{ route('admin.hidrologi.tma.index') }}" class="btn btn-outline flex-1 sm:flex-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
</script>
@endpush
