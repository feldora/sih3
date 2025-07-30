@extends('layouts.admin')

@section('title', 'Tambah Data Curah Hujan')

@section('content')
<div class="container mx-auto px-4">
    <!-- Breadcrumb -->
    <div class="breadcrumbs text-sm mb-6">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a></li>
            <li><a href="{{ route('admin.meteorologi.curah-hujan.index') }}" class="text-primary">Data Curah Hujan</a></li>
            <li>Tambah Data</li>
        </ul>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-header px-6 py-4 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="card-title text-xl font-bold">Tambah Data Curah Hujan</h3>
                <a href="{{ route('admin.meteorologi.curah-hujan.index') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

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
            @if(session('error'))
                <div class="alert alert-error mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.meteorologi.curah-hujan.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                    <!-- Pos Pantau -->
                    <div class="form-control w-full">
                        <label class="label" for="pos_pantau_id">
                            <span class="label-text font-medium">Pos Pantau <span class="text-error">*</span></span>
                        </label>
                        <select name="pos_pantau_id" id="pos_pantau_id" class="select select-bordered w-full @error('pos_pantau_id') select-error @enderror">
                            <option value="">Pilih Pos Pantau</option>
                            @foreach($pos_pantau as $pos)
                                <option value="{{ $pos->id }}" {{ old('pos_pantau_id') == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->nama_pos }}
                                </option>
                            @endforeach
                        </select>
                        @error('pos_pantau_id')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

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

                    <!-- Curah Hujan -->
                    <div class="form-control w-full">
                        <label class="label" for="curah_hujan">
                            <span class="label-text font-medium">Curah Hujan (mm) <span class="text-error">*</span></span>
                        </label>
                        <div class="relative">
                            <input type="number" name="curah_hujan" id="curah_hujan" 
                                   value="{{ old('curah_hujan') }}" 
                                   step="0.1" min="0" max="1000"
                                   placeholder="0.0"
                                   class="input input-bordered w-full pr-12 @error('curah_hujan') input-error @enderror">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-base-content/70">
                                mm
                            </span>
                        </div>
                        @error('curah_hujan')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="form-control w-full">
                    <x-text-editor
                        name="keterangan"
                        label="Keterangan"
                        :value="old('keterangan')"
                        placeholder="Tambahkan keterangan jika diperlukan..." 
                    />

                </div>

                <!-- Submit Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-base-300">
                    <button type="submit" class="btn btn-primary flex-1 sm:flex-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Data
                    </button>
                    
                    <a href="{{ route('admin.meteorologi.curah-hujan.index') }}" class="btn btn-outline flex-1 sm:flex-none">
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

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const curahHujanInput = document.getElementById('curah_hujan');
    const kategoriBadge = document.getElementById('kategori-badge');
    const kategoriInfo = document.getElementById('kategori-info');

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 300);
        }, 5000);
    });
});

function showToast(message, type = 'success') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast toast-top toast-end';
        document.body.appendChild(toastContainer);
    }
    
    const toast = document.createElement('div');
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-error' : 
                      type === 'info' ? 'alert-info' : 'alert-warning';
    
    toast.className = `alert ${alertClass} mb-2`;
    toast.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            ${type === 'success' 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                : type === 'error'
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
            }
        </svg>
        <span>${message}</span>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto remove toast after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
    }, 3000);
}
</script>
@endpush