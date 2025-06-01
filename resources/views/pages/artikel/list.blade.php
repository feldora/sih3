@extends('layouts.app-lain')

@section('title', 'Beranda')

@section('content')
    <div class="container mx-auto px-4 bg-slate-400 min-h-screen pb-20">
        <h1 class="text-2xl font-bold mb-6 pt-10">Daftar Artikel Terbaru</h1>
        @if($posts->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="col-span-2 flex flex-col gap-6">
                    <div class="flex flex-col gap-6">
                        @foreach($posts as $post)
                            <div class="bg-white rounded shadow p-4 flex flex-col w-full">
                                <div class="flex items-center mb-3">
                                    <img src="https://picsum.photos/150/150?random={{ $post->id }}" alt="Artikel Image" class="rounded w-12 h-12 object-cover mr-3">
                                    <div>
                                        <h2 class="text-lg font-semibold">{{ $post->title }}</h2>
                                        <p class="text-gray-700 mb-2">{{ Str::limit(strip_tags($post->content), 120) }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('artikel.publicShow', $post->slug) }}" class="text-blue-600 hover:underline mt-auto">Baca Selengkapnya</a>
                                <div class="text-xs text-gray-500 mt-2">
                                    {{ $post->user->name ?? 'Unknown' }} | {{ $post->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-center mt-6">
                        {{ $posts->links('pagination::tailwind') }}
                    </div>
                </div>
                <div class="col-span-1">
                    @include('partials.post-asside')
                </div>
            </div>
        @else
            <p>Tidak ada artikel ditemukan.</p>
        @endif
    </div>
@endsection
