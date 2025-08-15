@extends('layouts.admin')

@section('title', 'Detail Data Sungai')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Data Sungai</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap data sungai dan dokumen terkait</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.sungai.edit', $sungai->id) }}" class="btn btn-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.sungai.index') }}" class="btn btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
            <!-- Left Column - Informasi Sungai -->
            <div class="space-y-6 ">
                <!-- Info Card -->
                <div class="card h-full bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200">
                    <div class="card-body">
                        <h2 class="card-title text-blue-800 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informasi Sungai
                        </h2>
                        
                        <div class="space-y-4">
                            <!-- Nama Sungai -->
                            <div class="flex flex-col">
                                <label class="text-sm font-semibold text-gray-600 mb-1">Nama Sungai</label>
                                <div class="text-xl font-bold text-gray-800 bg-white p-3 rounded-lg border">
                                    {{ $sungai->nama_sungai }}
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Panjang Sungai -->
                                <div class="stat bg-white rounded-lg shadow-sm border">
                                    <div class="stat-figure text-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <div class="stat-title text-xs">Panjang Sungai</div>
                                    <div class="stat-value text-lg">
                                        @if($sungai->panjang_sungai)
                                            {{ number_format($sungai->panjang_sungai, 2) }}
                                            <span class="text-sm font-normal">km</span>
                                        @else
                                            <span class="text-gray-400 text-sm">Tidak tersedia</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Luas DAS -->
                                <div class="stat bg-white rounded-lg shadow-sm border">
                                    <div class="stat-figure text-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="stat-title text-xs">Luas DAS</div>
                                    <div class="stat-value text-lg">
                                        @if($sungai->luas_das)
                                            {{ number_format($sungai->luas_das, 2) }}
                                            <span class="text-sm font-normal">km²</span>
                                        @else
                                            <span class="text-gray-400 text-sm">Tidak tersedia</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Ordo Sungai -->
                            <div class="flex flex-col">
                                <label class="text-sm font-semibold text-gray-600 mb-2">Ordo Sungai</label>
                                <div class="flex items-center">
                                    @if($sungai->ordo)
                                        @php
                                            $ordoColors = [
                                                1 => 'bg-blue-900 text-white',
                                                2 => 'bg-blue-800 text-white',
                                                3 => 'bg-blue-700 text-white',
                                                4 => 'bg-blue-600 text-white',
                                                5 => 'bg-blue-500 text-white',
                                                6 => 'bg-blue-400 text-white',
                                                7 => 'bg-blue-300 text-blue-900',
                                                8 => 'bg-blue-200 text-blue-900',
                                                9 => 'bg-blue-100 text-blue-800'
                                            ];
                                        @endphp
                                        <div class="badge {{ $ordoColors[$sungai->ordo] ?? 'badge-outline' }} badge-lg">
                                            Ordo {{ $sungai->ordo }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Ordo belum ditentukan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Dokumen -->
            <div class="space-y-6 ">
                <!-- Dokumen Card -->
                <div class="card h-full bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200">
                    <div class="card-body">
                        <h2 class="card-title text-green-800 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Dokumen Terkait
                            <div class="badge badge-success badge-sm">{{ $sungai->getMedia('documents')->count() }} file</div>
                        </h2>

                        @if($sungai->getMedia('documents')->count() > 0)
                            <div class="space-y-3">
                                @foreach($sungai->getMedia('documents') as $media)
                                <div class="group flex items-center justify-between p-4 border border-green-200 rounded-lg bg-white hover:bg-green-50 transition-all duration-200 cursor-pointer hover:shadow-md"
                                     onclick="downloadFile('{{ route('admin.sungai.media.download', [$sungai->id, $media->id]) }}', '{{ $media->name }}')">
                                    <div class="flex items-center space-x-4">
                                        <div class="avatar">
                                            <div class="mask mask-squircle w-12 h-12 bg-white border-2 border-green-200 flex items-center justify-center p-2 group-hover:border-green-300 transition-colors">
                                                <x-document-icon :extension="$media->extension" size="full" />
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-800 group-hover:text-green-800 transition-colors">
                                                {{ $media->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 flex items-center space-x-4">
                                                <span>{{ number_format($media->size / 1024, 2) }} KB</span>
                                                <span>•</span>
                                                <span>{{ strtoupper($media->extension) }}</span>
                                                <span>•</span>
                                                <span>{{ $media->created_at->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="tooltip tooltip-left" data-tip="Download file">
                                            <button class="btn btn-ghost btn-sm btn-circle text-green-600 hover:bg-green-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m3 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-500 text-sm">Belum ada dokumen yang diupload</p>
                                <p class="text-gray-400 text-xs mt-1">Dokumen terkait sungai akan ditampilkan di sini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="modal">
    <div class="modal-box">
        <div class="flex flex-col items-center py-4">
            <span class="loading loading-spinner loading-lg text-primary mb-4"></span>
            <h3 class="font-bold text-lg">Memproses...</h3>
            <p class="text-gray-600" id="loadingText">Mempersiapkan download...</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .group:hover .group-hover\:border-green-300 {
        border-color: rgb(134 239 172);
    }
    
    .group:hover .group-hover\:text-green-800 {
        color: rgb(22 101 52);
    }
    
    .stat {
        padding: 1rem;
    }
    
    .stats-vertical .stat {
        border-bottom: 1px solid #e5e7eb;
    }
    
    .stats-vertical .stat:last-child {
        border-bottom: none;
    }
    
    .tooltip::before {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingModal = document.getElementById('loadingModal');
    const loadingText = document.getElementById('loadingText');

    // Toast function
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

    // Session messages
    @if (session('error'))
        showToast("{{ session('error') }}", "error");
    @endif

    @if (session('success'))
        showToast("{{ session('success') }}", "success");
    @endif

    @if (session('warning'))
        showToast("{{ session('warning') }}", "warning");
    @endif

    // Download single file function
    window.downloadFile = function(url, filename) {
        loadingText.textContent = `Mengunduh ${filename}...`;
        loadingModal.classList.add('modal-open');
        
        // Create temporary link to trigger download
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Hide loading modal after a short delay
        setTimeout(() => {
            loadingModal.classList.remove('modal-open');
            showToast(`File ${filename} berhasil diunduh`, 'success');
        }, 1000);
    };

    // Close loading modal when clicking outside
    loadingModal?.addEventListener('click', function(e) {
        if (e.target === loadingModal) {
            loadingModal.classList.remove('modal-open');
        }
    });
});
</script>
@endpush