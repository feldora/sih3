@extends('layouts.admin')

@section('title', 'Media Library')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-base-content">Media Library</h1>
            <p class="text-base-content/70 mt-1">Kelola file media Anda</p>
        </div>
        <div class="flex gap-2 mt-4 sm:mt-0">
            <button onclick="bulkDelete()" class="btn btn-error btn-sm" id="bulk-delete-btn" style="display:none;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Hapus Terpilih
            </button>
            <a href="{{ route('admin.media.create') }}" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Upload Media
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card bg-base-100 shadow-sm mb-6">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.media.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Cari File</span>
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nama file..." class="input input-bordered input-sm">
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Collection</span>
                    </label>
                    <select name="collection" class="select select-bordered select-sm">
                        <option value="">Semua Collection</option>
                        @foreach($collections as $collection)
                            <option value="{{ $collection }}" {{ request('collection') == $collection ? 'selected' : '' }}>
                                {{ $collection }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Tipe File</span>
                    </label>
                    <select name="type" class="select select-bordered select-sm">
                        <option value="">Semua Tipe</option>
                        @foreach($mimeTypes as $type => $label)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">&nbsp;</span>
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-1">Filter</button>
                        <a href="{{ route('admin.media.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Media Grid -->
    @if($media->count() > 0)
        <form id="bulk-delete-form" method="POST" action="{{ route('admin.media.bulk-delete') }}">
            @csrf
            @method('DELETE')
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-6">
                @foreach($media as $item)
                    <div class="card bg-base-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="card-body p-3">
                            <!-- Checkbox -->
                            <div class="form-control absolute top-2 left-2 z-10">
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}" 
                                           class="checkbox checkbox-primary checkbox-sm media-checkbox">
                                </label>
                            </div>
                            
                            <!-- Media Preview -->
                            <div class="aspect-square bg-base-200 rounded-lg flex items-center justify-center mb-3 relative overflow-hidden">
                                @if(str_starts_with($item->mime_type, 'image/'))
                                    <img src="{{ $item->getUrl() }}" alt="{{ $item->name }}" 
                                         class="w-full h-full object-cover rounded-lg">
                                @elseif(str_starts_with($item->mime_type, 'video/'))
                                    <div class="text-center">
                                        <svg class="w-8 h-8 text-primary mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        <span class="text-xs text-base-content/70">Video</span>
                                    </div>
                                @elseif(str_starts_with($item->mime_type, 'audio/'))
                                    <div class="text-center">
                                        <svg class="w-8 h-8 text-secondary mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                                        </svg>
                                        <span class="text-xs text-base-content/70">Audio</span>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <svg class="w-8 h-8 text-accent mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                        </svg>
                                        <span class="text-xs text-base-content/70">{{ strtoupper(pathinfo($item->file_name, PATHINFO_EXTENSION)) }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Media Info -->
                            <div class="space-y-1">
                                <h3 class="text-sm font-medium text-base-content truncate" title="{{ $item->name }}">
                                    {{ $item->name }}
                                </h3>
                                <p class="text-xs text-base-content/70">
                                    {{ formatFileSize($item->size) }}
                                </p>
                                @if($item->collection_name)
                                    <span class="badge badge-outline badge-xs">{{ $item->collection_name }}</span>
                                @endif
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex justify-between items-center mt-3 pt-2 border-t border-base-300">
                                <div class="dropdown dropdown-end">
                                    <label tabindex="0" class="btn btn-ghost btn-xs">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12,16A2,2 0 0,1 14,18A2,2 0 0,1 12,20A2,2 0 0,1 10,18A2,2 0 0,1 12,16M12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12A2,2 0 0,1 12,10M12,4A2,2 0 0,1 14,6A2,2 0 0,1 12,8A2,2 0 0,1 10,6A2,2 0 0,1 12,4Z"/>
                                        </svg>
                                    </label>
                                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-40">
                                        <li><a href="{{ route('admin.media.show', $item) }}" class="text-sm">Lihat Detail</a></li>
                                        <li><a href="{{ route('admin.media.edit', $item) }}" class="text-sm">Edit</a></li>
                                        <li><a href="{{ route('admin.media.download', $item) }}" class="text-sm">Download</a></li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.media.destroy', $item) }}" 
                                                  onsubmit="return confirm('Yakin ingin menghapus media ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-error text-sm w-full text-left">Hapus</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                
                                <button onclick="copyUrl('{{ $item->getUrl() }}')" 
                                        class="btn btn-ghost btn-xs" title="Copy URL">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
        
        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $media->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-base-content/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-base-content/70 mb-2">Tidak ada media ditemukan</h3>
            <p class="text-base-content/50 mb-4">Mulai dengan mengupload file media pertama Anda.</p>
            <a href="{{ route('admin.media.create') }}" class="btn btn-primary">
                Upload Media
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Handle checkbox selection
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.media-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.media-checkbox:checked');
            if (checkedBoxes.length > 0) {
                bulkDeleteBtn.style.display = 'inline-flex';
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
        });
    });
});

// Bulk delete function
function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.media-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Pilih media yang ingin dihapus terlebih dahulu.');
        return;
    }
    
    if (confirm(`Yakin ingin menghapus ${checkedBoxes.length} media yang dipilih?`)) {
        document.getElementById('bulk-delete-form').submit();
    }
}

// Copy URL to clipboard
function copyUrl(url) {
    navigator.clipboard.writeText(url).then(function() {
        // You can add a toast notification here
        alert('URL berhasil disalin!');
    }).catch(function(err) {
        console.error('Failed to copy URL: ', err);
    });
}
</script>
@endpush
@endsection

@php
function formatFileSize($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
@endphp