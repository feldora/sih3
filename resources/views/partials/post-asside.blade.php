<aside class="bg-white rounded shadow p-4 sticky top-10">
    <h3 class="text-xl font-bold mb-4">Artikel Populer</h3>
    <ul class="space-y-3">
        @foreach($popularPosts as $pop)
            <li>
                <a href="{{ route('artikel.publicShow', $pop->slug) }}" class="text-blue-700 hover:underline font-medium">
                    {{ $pop->title }}
                </a>
                <div class="text-xs text-gray-500">{{ $pop->user->name ?? 'Unknown' }}</div>
            </li>
        @endforeach
    </ul>
    <div class="mt-8">
        <h4 class="text-lg font-semibold mb-2">Kategori</h4>
        <ul class="text-sm text-gray-600 space-y-1">
            <li>
                <a href="{{ route('artikel.publicIndex') }}" class="hover:text-blue-600">
                    Semua Kategori
                </a>
            </li>
            @foreach($categories as $category)
                <li>
                    <a href="{{ route('artikel.publicIndex', ['category' => $category->slug]) }}" class="hover:text-blue-600">
                        {{ $category->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</aside>