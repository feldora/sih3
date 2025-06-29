@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-base-content">Tambah Sungai</h1>
        <a href="{{ route('admin.sungai.index') }}" class="btn btn-ghost">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-error mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="card bg-base-100 shadow-xl">
        <div class="card-header bg-primary text-primary-content">
            <h2 class="card-title">Form Tambah Sungai</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sungai.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">
                                Nama Sungai <span class="text-error">*</span>
                            </span>
                        </label>
                        <input type="text" 
                               name="nama_sungai" 
                               value="{{ old('nama_sungai') }}" 
                               placeholder="Masukkan nama sungai" 
                               class="input input-bordered w-full @error('nama_sungai') input-error @enderror" 
                               required />
                        @error('nama_sungai')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                    
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">
                                Panjang Sungai (km) <span class="text-error">*</span>
                            </span>
                        </label>
                        <input type="number" 
                               name="panjang_sungai" 
                               value="{{ old('panjang_sungai') }}" 
                               placeholder="Masukkan panjang sungai" 
                               step="0.01" 
                               min="0" 
                               class="input input-bordered w-full @error('panjang_sungai') input-error @enderror" 
                               required />
                        @error('panjang_sungai')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">
                                Luas DAS (km²) <span class="text-error">*</span>
                            </span>
                            <span class="label-text-alt">DAS = Daerah Aliran Sungai</span>
                        </label>
                        <input type="number" 
                               name="luas_das" 
                               value="{{ old('luas_das') }}" 
                               placeholder="Masukkan luas DAS" 
                               step="0.01" 
                               min="0" 
                               class="input input-bordered w-full @error('luas_das') input-error @enderror" 
                               required />
                        @error('luas_das')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                    <!-- Dropdown Wilayah Sungai -->
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">
                                Wilayah Sungai <span class="text-error">*</span>
                            </span>
                        </label>
                        <select name="wilayah_sungai_id" class="select select-bordered w-full @error('wilayah_sungai_id') select-error @enderror" required>
                            <option value="">Pilih Wilayah Sungai</option>
                            @foreach($wilayahSungai as $wilayah)
                                <option value="{{ $wilayah->id }}" {{ old('wilayah_sungai_id') == $wilayah->id ? 'selected' : '' }}>{{ $wilayah->nama }}</option>
                            @endforeach
                        </select>
                        @error('wilayah_sungai_id')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <!-- Dropdown Status -->
                <div class="form-control w-full mt-4">
                    <label class="label">
                        <span class="label-text font-semibold">Status <span class="text-error">*</span></span>
                    </label>
                    <select name="status" class="select select-bordered w-full @error('status') select-error @enderror" required>
                        <option value="">Pilih Status</option>
                        <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Textarea GeoJSON -->
                <div class="form-control w-full mt-4">
                    <label class="label">
                        <span class="label-text font-semibold">GeoJSON</span>
                        <span class="label-text-alt">Opsional</span>
                    </label>
                    <textarea name="geojson" placeholder="Masukkan data GeoJSON (opsional)" class="textarea textarea-bordered h-24 @error('geojson') textarea-error @enderror">{{ old('geojson') }}</textarea>
                    @error('geojson')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="card-actions justify-end mt-8">
                    <a href="{{ route('admin.sungai.index') }}" class="btn btn-ghost">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
