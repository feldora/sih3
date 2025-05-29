@extends('layouts.app-lain')

@section('title', 'Beranda')

@section('content')
    <div class="container mx-auto px-4 bg-slate-400 min-h-screen pb-20">
        @php
            $articles = [
                [
                    'title' => 'Laravel 10 Dirilis: Fitur Baru dan Peningkatan',
                    'description' => 'Laravel 10 hadir dengan berbagai fitur baru yang memudahkan pengembangan aplikasi web modern.',
                    'url' => 'https://laravel.com/docs/10.x/releases',
                    'author' => 'Taylor Otwell',
                    'published' => now()->subDays(1)->toDateTimeString(),
                ],
                [
                    'title' => 'Mengenal Blade Template di Laravel',
                    'description' => 'Blade adalah template engine yang powerful dan mudah digunakan di Laravel.',
                    'url' => 'https://laravel.com/docs/10.x/blade',
                    'author' => 'Jane Doe',
                    'published' => now()->subDays(2)->toDateTimeString(),
                ],
                [
                    'title' => 'Tips Keamanan untuk Aplikasi Laravel',
                    'description' => 'Pelajari tips penting untuk menjaga keamanan aplikasi Laravel Anda.',
                    'url' => 'https://laravel.com/docs/10.x/security',
                    'author' => 'John Smith',
                    'published' => now()->subHours(10)->toDateTimeString(),
                ],
            ];
        @endphp

        <h1 class="text-2xl font-bold mb-6 pt-10">Daftar Artikel Terbaru</h1>
        @if(count($articles))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="col-span-2 flex flex-col gap-6">
                    <div class="flex flex-col gap-6">
                        @for($i = 0; $i < 2; $i++)
                            @foreach($articles as $article)
                                <div class="bg-white rounded shadow p-4 flex flex-col w-full">
                                    <div class="flex items-center mb-3">
                                        <img src="{{ $article['thumbnail'] ?? 'https://picsum.photos/150/150' }}" alt="Artikel Image" class="rounded w-12 h-12 object-cover mr-3">
                                        <div>
                                            <h2 class="text-lg font-semibold">{{ $article['title'] }}</h2>
                                            <p class="text-gray-700 mb-2">{{ Str::limit($article['description'] ?? '', 120) }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ $article['url'] }}" target="_blank" class="text-blue-600 hover:underline mt-auto">Baca Selengkapnya</a>
                                    <div class="text-xs text-gray-500 mt-2">
                                        {{ $article['author'] ?? 'Unknown' }} | {{ \Carbon\Carbon::parse($article['published'])->format('d M Y H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        @endfor
                    </div>
                    <div class="flex justify-center mt-6">
                        <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                            <a href="#" class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">Sebelumnya</a>
                            <a href="#" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-sm font-medium text-blue-600">1</a>
                            <a href="#" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">2</a>
                            <a href="#" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">3</a>
                            <a href="#" class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">Berikutnya</a>
                        </nav>
                    </div>
                </div>
                <div class="col-span-1">
                    <aside class="bg-white rounded shadow p-4 sticky top-10">
                        <h3 class="text-xl font-bold mb-4">Artikel Populer</h3>
                        <ul class="space-y-3">
                            @foreach($articles as $article)
                                <li>
                                    <a href="{{ $article['url'] }}" target="_blank" class="text-blue-700 hover:underline font-medium">
                                        {{ $article['title'] }}
                                    </a>
                                    <div class="text-xs text-gray-500">{{ $article['author'] ?? 'Unknown' }}</div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold mb-2">Kategori</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><a href="#" class="hover:text-blue-600">Laravel</a></li>
                                <li><a href="#" class="hover:text-blue-600">Blade</a></li>
                                <li><a href="#" class="hover:text-blue-600">Keamanan</a></li>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        @else
            <p>Tidak ada artikel ditemukan.</p>
        @endif
    </div>

@endsection
