@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-base-content">Data Sungai</h1>
        <a href="{{ route('admin.sungai.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Sungai
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="card bg-base-100 shadow-xl">
        <div class="card-header bg-primary text-primary-content">
            <h2 class="card-title">Daftar Sungai</h2>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Sungai</th>
                            <th>Panjang (km)</th>
                            <th>Luas DAS (km²)</th>
                            <th>Hasil Uji Kualitas Air</th>
                            <th>Wilayah Sungai</th>
                            <th>Status</th>
                            <th>GeoJSON</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sungai as $index => $item)
                            <tr class="hover">
                                <td>{{ $sungai->firstItem() + $index }}</td>
                                <td class="font-semibold">{{ $item->nama_sungai }}</td>
                                <td>
                                    <span class="badge badge-outline">
                                        {{ number_format($item->panjang_sungai, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-outline">
                                        {{ number_format($item->luas_das, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->hasil_uji_kualitas_air)
                                        <div class="tooltip" data-tip="{{ $item->hasil_uji_kualitas_air }}">
                                            <span class="text-sm truncate max-w-xs">
                                                {{ Str::limit($item->hasil_uji_kualitas_air, 50) }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>{{ $item->wilayahSungai->nama ?? '-' }}</td>
                                <td>
                                    @if($item->status == 'aktif')
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->geojson)
                                        <span class="text-xs truncate d-block" title="{{ $item->geojson }}">{{ Str::limit($item->geojson, 50) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="join">
                                        <a href="{{ route('admin.sungai.show', $item->id) }}" 
                                           class="btn btn-info btn-sm join-item" 
                                           data-tip="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.sungai.edit', $item->id) }}" 
                                           class="btn btn-warning btn-sm join-item" 
                                           data-tip="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.sungai.destroy', $item->id) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-error btn-sm join-item" 
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')" 
                                                    data-tip="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <span class="text-gray-500">Tidak ada data sungai</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($sungai->hasPages())
                <div class="flex justify-center mt-6">
                    <div class="join">
                        {{-- Previous Page Link --}}
                        @if ($sungai->onFirstPage())
                            <button class="join-item btn btn-disabled">«</button>
                        @else
                            <a href="{{ $sungai->previousPageUrl() }}" class="join-item btn">«</a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($sungai->getUrlRange(1, $sungai->lastPage()) as $page => $url)
                            @if ($page == $sungai->currentPage())
                                <button class="join-item btn btn-active">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}" class="join-item btn">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($sungai->hasMorePages())
                            <a href="{{ $sungai->nextPageUrl() }}" class="join-item btn">»</a>
                        @else
                            <button class="join-item btn btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
