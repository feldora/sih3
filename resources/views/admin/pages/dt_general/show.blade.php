@extends('layouts.admin')

@section('title', 'Tampil Data')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50 py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            
            @if ($post)
                <div class="">
                    <!-- Main Content Area -->
                    <div class="lg:col-span-3">
                        <div class="mb-12">
                            <article class="relative overflow-hidden rounded-2xl shadow-xl bg-white">
                                <div class="p-8">

                                    <!-- Article Header -->
                                    <Header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                                        <!-- Author Info -->
                                        <div class="flex items-center space-x-4">
                                            <div>
                                                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
                                                    {{ $post->title }}
                                                </h1>
                                                <div class="flex items-center space-x-2 text-sm text-gray-500">
                                                    <span>{{ $post->created_at->format('H:i') }}</span>
                                                    <span>•</span>
                                                    <span>{{ $post->views }} Views</span>
                                                    @if ($post->category)
                                                        <span>•</span>
                                                        <span
                                                            class="text-blue-600 font-medium">{{ $post->category->name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Back Button -->
                                        <div class="flex-shrink-0">

                                        </div>
                                    </Header>

                                    <!-- Media Display Component -->
                                    @foreach ($post->media->all() as $media)
                                        <x-media-display :media="$media" />
                                    @endforeach
                                    {{-- <x-media-display :media="$post->media->first()" /> --}}

                                    <!-- Article Content -->
                                    <div class="prose prose-lg max-w-none mb-8">
                                        <div class="text-gray-700 leading-relaxed text-lg">
                                            {!! $post->content !!}
                                        </div>
                                    </div>

                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Sidebar -->
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="max-w-md mx-auto">
                        <div
                            class="w-24 h-24 bg-gradient-to-r from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Tidak ada artikel ditemukan</h3>
                        <p class="text-gray-600 mb-6">Belum ada artikel yang dipublikasikan. Silakan cek kembali nanti.</p>
                        <a href="{{ route('/') }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-full hover:from-blue-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection