<div class="bg-white shadow-lg rounded-lg mb-4 ml-8 p-6 border border-gray-200">
    <div class="flex justify-between items-center">
        <div class="flex-1">
            <h4 class="text-lg font-semibold text-gray-800">{{ $child->title }}</h4>
            <p class="text-sm text-gray-500">{{ $child->url }}</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('admin.menus.edit', $child) }}" class="btn btn-sm btn-warning px-4 py-2 text-white bg-yellow-500 hover:bg-yellow-600 rounded-md">Edit</a>
            <form action="{{ route('admin.menus.destroy', $child) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus menu ini?')" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger px-4 py-2 text-white bg-red-500 hover:bg-red-600 rounded-md">Hapus</button>
            </form>
        </div>
    </div>

    <!-- Menampilkan Submenu jika Ada (Rekursif) -->
    @foreach ($child->children as $submenu)
        @include('admin.pages.menu.submenu', ['child' => $submenu, 'level' => $level + 1])
    @endforeach
</div>
