@extends('layouts.admin')

@section('title', 'Tambah Data Cekungan Air Tanah')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Tambah Data Cekungan Air Tanah</h1>
            <a href="{{ route('admin.cat.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <form action="{{ route('admin.cat.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Nama Cekungan Air Tanah <span class="text-red-500">*</span></span>
                    </label>
                    <input type="text" name="nama_cat" value="{{ old('nama_cat') }}" 
                           class="input input-bordered @error('nama_cat') input-error @enderror" 
                           placeholder="Masukkan nama cekungan air tanah" required>
                    @error('nama_cat')
                        <label class="label">
                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Luas CAT (ha)</span>
                    </label>
                    <input type="number" step="0.01" name="luas_cat_ha" value="{{ old('luas_cat_ha') }}" 
                           class="input input-bordered @error('luas_cat_ha') input-error @enderror" 
                           placeholder="Masukkan luas dalam hektar">
                    @error('luas_cat_ha')
                        <label class="label">
                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Potensi Air Tanah Bebas</span>
                    </label>
                    <input type="number" step="0.01" name="potensi_air_tanah_bebas" value="{{ old('potensi_air_tanah_bebas') }}" 
                           class="input input-bordered @error('potensi_air_tanah_bebas') input-error @enderror" 
                           placeholder="Masukkan potensi air tanah bebas">
                    @error('potensi_air_tanah_bebas')
                        <label class="label">
                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Potensi Air Tanah Tertekan</span>
                    </label>
                    <input type="number" step="0.01" name="potensi_air_tanah_tertekan" value="{{ old('potensi_air_tanah_tertekan') }}" 
                           class="input input-bordered @error('potensi_air_tanah_tertekan') input-error @enderror" 
                           placeholder="Masukkan potensi air tanah tertekan">
                    @error('potensi_air_tanah_tertekan')
                        <label class="label">
                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
            </div>

            <div class="divider"></div>

            <div class="flex justify-end gap-2">
                <button type="reset" class="btn btn-ghost">Reset</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> --}}
@endpush

@push('scripts')
<script>
    window.addEventListener('load', function() {
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded yet.');
            return;
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast toast-top toast-end`;
            toast.innerHTML = `
                <div class="alert alert-${type}">
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        @if (session('error'))
            showToast("{{ session('error') }}", "error");
        @endif

        @if (session('success'))
            showToast("{{ session('success') }}", "success");
        @endif

        @if (session('warning'))
            showToast("{{ session('warning') }}", "warning");
        @endif
    });
</script>
@endpush