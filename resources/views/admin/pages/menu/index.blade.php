@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6 bg-gray-100 shadow-lg rounded-lg">
    <!-- Tombol Tambah Menu -->
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary px-4 py-2 rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-200">Tambah Menu</a>

        <!-- Form Pencarian dan Filter -->
        <div class="flex items-center space-x-4">
            <input type="text" id="search" class="form-input px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Cari menu..." />
            <select id="filterParent" class="form-select px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Parent</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-6 p-4 bg-green-100 text-green-800 rounded-md">{{ session('success') }}</div>
    @endif

    <!-- Daftar Menu dengan Struktur Nested -->
    <div id="menu-list">
        @foreach ($menus as $menu)
            @if (!$menu->parent)
                <div class="bg-white shadow-xl rounded-lg mb-6 p-6 border border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800">{{ $menu->title }}</h3>
                            <p class="text-sm text-gray-500">{{ $menu->url }}</p>
                        </div>
                        <div class="space-x-2">
                            <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-sm btn-warning px-4 py-2 text-white bg-yellow-500 hover:bg-yellow-600 rounded-md">Edit</a>
                            <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus menu ini?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger px-4 py-2 text-white bg-red-500 hover:bg-red-600 rounded-md">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <!-- Menampilkan Submenu jika Ada -->
                    @foreach ($menu->children as $child)
                        @include('admin.pages.menu.submenu', ['child' => $child, 'level' => 1])
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>

    <!-- Pagination -->
    @if(method_exists($menus, 'links'))
        <div class="mt-6">
            {{ $menus->links() }}
        </div>
    @endif
</div>

@endsection


@section('scripts')
<script>
    // Filter dan Pencarian
    document.getElementById('search').addEventListener('input', function() {
        let searchQuery = this.value.toLowerCase();
        let menus = document.querySelectorAll('#menu-list > div');
        menus.forEach(function(menu) {
            let title = menu.querySelector('h3').innerText.toLowerCase();
            if (title.includes(searchQuery)) {
                menu.style.display = '';
            } else {
                menu.style.display = 'none';
            }
        });
    });

    document.getElementById('filterParent').addEventListener('change', function() {
        let filterValue = this.value;
        let menus = document.querySelectorAll('#menu-list > div');
        menus.forEach(function(menu) {
            let parent = menu.querySelector('.text-xs').innerText;
            if (filterValue && parent.indexOf(filterValue) === -1) {
                menu.style.display = 'none';
            } else {
                menu.style.display = '';
            }
        });
    });
</script>
@endsection
