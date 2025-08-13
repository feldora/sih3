@extends('layouts.admin')

@section('title', 'Detail Data Cekungan Air Tanah')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Detail Data Cekungan Air Tanah</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.cat.edit', $cekunganAirTanah->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.cat.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informasi Dasar -->
            <div class="card bg-base-200 shadow-md">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Informasi Dasar
                    </h2>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Nama Cekungan Air Tanah</label>
                            <p class="text-lg font-semibold">{{ $cekunganAirTanah->nama_cat }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-600">Luas CAT</label>
                            <p class="text-lg">{{ number_format($cekunganAirTanah->luas_cat_ha ?? 0, 2) }} ha</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Potensi -->
            <div class="card bg-base-200 shadow-md">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-tint text-blue-500"></i>
                        Data Potensi Air Tanah
                    </h2>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Potensi Air Tanah Bebas</label>
                            <p class="text-lg">{{ number_format($cekunganAirTanah->potensi_air_tanah_bebas ?? 0, 2) }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-600">Potensi Air Tanah Tertekan</label>
                            <p class="text-lg">{{ number_format($cekunganAirTanah->potensi_air_tanah_tertekan ?? 0, 2) }}</p>
                        </div>

                        <div class="divider my-2"></div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-600">Total Potensi Air Tanah</label>
                            <p class="text-xl font-bold text-primary">
                                {{ number_format(($cekunganAirTanah->potensi_air_tanah_bebas ?? 0) + ($cekunganAirTanah->potensi_air_tanah_tertekan ?? 0), 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Sistem -->
            <div class="card bg-base-200 shadow-md md:col-span-2">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-clock text-green-500"></i>
                        Informasi Sistem
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">ID</label>
                            <p class="text-lg">{{ $cekunganAirTanah->id }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tanggal Dibuat</label>
                            <p class="text-lg">{{ $cekunganAirTanah->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-600">Terakhir Diperbarui</label>
                            <p class="text-lg">{{ $cekunganAirTanah->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Visual -->
        <div class="card bg-base-200 shadow-md mt-6">
            <div class="card-body">
                <h2 class="card-title text-lg mb-4">
                    <i class="fas fa-chart-bar text-purple-500"></i>
                    Visualisasi Data
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="stat bg-base-100 rounded-lg shadow">
                        <div class="stat-figure text-primary">
                            <i class="fas fa-water text-2xl"></i>
                        </div>
                        <div class="stat-title">Air Tanah Bebas</div>
                        <div class="stat-value text-primary">{{ number_format($cekunganAirTanah->potensi_air_tanah_bebas ?? 0, 0) }}</div>
                        <div class="stat-desc">
                            @php
                                $total = ($cekunganAirTanah->potensi_air_tanah_bebas ?? 0) + ($cekunganAirTanah->potensi_air_tanah_tertekan ?? 0);
                                $persentase = $total > 0 ? (($cekunganAirTanah->potensi_air_tanah_bebas ?? 0) / $total) * 100 : 0;
                            @endphp
                            {{ number_format($persentase, 1) }}% dari total potensi
                        </div>
                    </div>
                    
                    <div class="stat bg-base-100 rounded-lg shadow">
                        <div class="stat-figure text-secondary">
                            <i class="fas fa-compress-alt text-2xl"></i>
                        </div>
                        <div class="stat-title">Air Tanah Tertekan</div>
                        <div class="stat-value text-secondary">{{ number_format($cekunganAirTanah->potensi_air_tanah_tertekan ?? 0, 0) }}</div>
                        <div class="stat-desc">
                            @php
                                $persentase2 = $total > 0 ? (($cekunganAirTanah->potensi_air_tanah_tertekan ?? 0) / $total) * 100 : 0;
                            @endphp
                            {{ number_format($persentase2, 1) }}% dari total potensi
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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