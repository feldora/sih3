@extends('layouts.admin')

@section('title', 'Data Sungai - SIH3 Sulteng')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Data Sungai</h1>
                <p class="text-base-content/70 mt-1">Kelola data sungai dan media terkait</p>
            </div>
            <a href="{{ route('admin.sungai.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Sungai
            </a>
        </div>

        <!-- Filter & Search -->
        <div class="card bg-base-200 p-4 mb-6">
            <form method="GET" action="{{ route('admin.sungai.index') }}" class="flex flex-col lg:flex-row gap-4">
                <div class="form-control flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama sungai, ordo..." 
                           class="input input-bordered w-full">
                </div>
                <div class="form-control lg:w-64">
                    <select name="wilayah_sungai_id" class="select select-bordered">
                        <option value="">Semua Wilayah Sungai</option>
                        @foreach($wilayahSungais as $wilayah)
                            <option value="{{ $wilayah->id }}" 
                                    {{ request('wilayah_sungai_id') == $wilayah->id ? 'selected' : '' }}>
                                {{ $wilayah->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Cari
                        </button>
                        <a href="{{ route('admin.sungai.index') }}" class="btn btn-outline">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="stat bg-primary text-primary-content rounded-xl">
                <div class="stat-title text-primary-content/70">Total Sungai</div>
                <div class="stat-value">{{ $sungais->total() }}</div>
            </div>
            <div class="stat bg-secondary text-secondary-content rounded-xl">
                <div class="stat-title text-secondary-content/70">Dengan Media</div>
                <div class="stat-value">{{ $sungais->where('media_count', '>', 0)->count() }}</div>
            </div>
            <div class="stat bg-accent text-accent-content rounded-xl">
                <div class="stat-title text-accent-content/70">Total Media</div>
                <div class="stat-value">{{ $sungais->sum('media_count') }}</div>
            </div>
        </div>

        <!-- Table -->
        @if($sungais->count() > 0)
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Sungai</th>
                        <th>Wilayah Sungai</th>
                        <th class="text-center">Panjang (km)</th>
                        <th class="text-center">Luas DAS (km²)</th>
                        <th class="text-center">Ordo</th>
                        <th class="text-center">Media</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sungais as $index => $sungai)
                    <tr class="hover">
                        <td class="text-center">{{ $sungais->firstItem() + $index }}</td>
                        <td class="font-semibold">{{ $sungai->nama_sungai }}</td>
                        <td>
                            <div class="badge badge-outline">
                                {{ $sungai->wilayahSungai->nama ?? '-' }}
                            </div>
                        </td>
                        <td class="text-center">
                            {{ $sungai->panjang_sungai ? number_format($sungai->panjang_sungai, 2) : '-' }}
                        </td>
                        <td class="text-center">
                            {{ $sungai->luas_das ? number_format($sungai->luas_das, 2) : '-' }}
                        </td>
                        <td class="text-center">
                            @if($sungai->ordo)
                                <div class="badge badge-primary">{{ $sungai->ordo }}</div>
                            @else
                                <span class="text-base-content/50">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($sungai->media->count() > 0)
                                <div class="badge badge-success">{{ $sungai->media->count() }} file(s)</div>
                            @else
                                <div class="badge badge-ghost">0 file</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-ghost btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </label>
                                <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li>
                                        <a href="{{ route('admin.sungai.show', $sungai) }}" class="text-info">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.sungai.edit', $sungai) }}" class="text-warning">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                    </li>
                                    <li>
                                        <button onclick="confirmDelete('{{ $sungai->id }}', '{{ $sungai->nama_sungai }}')" class="text-error">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-6">
            {{ $sungais->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-24 w-24 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-base-content">Belum ada data sungai</h3>
            <p class="text-base-content/70 mt-2">Mulai dengan menambahkan data sungai pertama Anda.</p>
            <a href="{{ route('admin.sungai.create') }}" class="btn btn-primary mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Sungai
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<dialog id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
        <p class="py-4">Apakah Anda yakin ingin menghapus sungai <span id="sungaiName" class="font-semibold text-error"></span>?</p>
        <p class="text-sm text-warning mb-4">⚠️ Tindakan ini akan menghapus semua data dan media terkait secara permanen!</p>
        
        <div class="modal-action">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteModal()" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-error">Ya, Hapus</button>
            </form>
        </div>
    </div>
</dialog>
@endsection

@push('styles')
<style>
    .table th {
        background-color: hsl(var(--b2));
        font-weight: 600;
    }
    
    .stat {
        padding: 1.5rem;
    }
    
    .dropdown-content {
        z-index: 999;
    }
</style>
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

    function confirmDelete(id, name) {
        document.getElementById('sungaiName').textContent = name;
        document.getElementById('deleteForm').action = `/admin/sungai/${id}`;
        document.getElementById('deleteModal').showModal();
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').close();
    }
</script>
@endpush