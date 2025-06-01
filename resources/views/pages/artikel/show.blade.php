@extends('layouts.app-lain')

@section('title', $post->title ?? 'Artikel Tidak Ditemukan')

@section('content')
    <div class="container mx-auto px-4 bg-slate-400 min-h-screen pb-20 pt-5">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-10">
            <div class="col-span-2 flex flex-col gap-6">
                @if($post)
                    <div class="bg-white rounded shadow p-8">
                        <h1 class="text-3xl font-bold mb-2">{{ $post->title }}</h1>
                        <div class="text-sm text-gray-500 mb-4">
                            {{ $post->user->name ?? 'Unknown' }} | {{ $post->created_at->format('d M Y H:i') }}
                            @if($post->category)
                                | Kategori: <span class="font-semibold">{{ $post->category->name }}</span>
                            @endif
                        </div>
                        <img src="https://picsum.photos/800/300?random={{ $post->id }}" alt="Artikel Image" class="rounded w-full h-64 object-cover mb-6">
                        <div class="prose max-w-none mb-6">
                            {!! $post->content !!}
                        </div>
                        <div class="flex flex-wrap gap-2 mt-4">
                            @foreach($post->tags as $tag)
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded shadow p-8">
                        <h2 class="text-xl font-bold mb-4">Artikel tidak ditemukan.</h2>
                        <p>Maaf, artikel yang Anda cari tidak tersedia.</p>
                    </div>
                @endif
            </div>
            <div class="col-span-1">
                @include('partials.post-asside')
            </div>
        </div>
    </div>
@endsection
