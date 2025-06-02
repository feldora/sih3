@extends('layouts.app-lain')

@section('title', 'Beranda')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50 py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="relative text-center mb-12 h-64">
                <div class="absolute inset-0">
                    <img src="{{ asset('/images/paisupok.webp') }}" alt="Background"
                        class="w-full h-full object-cover object-center opacity-30" />
                </div>
                <div class="relative z-10 py-12 flex flex-col justify-end h-full">
                    <h1
                        class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-4">
                        Daftar Artikel Terbaru
                    </h1>
                    <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto rounded-full"></div>
                </div>
            </div>

            @if ($posts->count())
                <div class="grid lg:grid-cols-4 gap-8">
                    <!-- Main Content Area -->
                    <div class="lg:col-span-3">
                        <!-- Featured Article (First Post) -->
                        @if ($posts->first())
                            <div class="mb-12">
                                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                                    <span
                                        class="w-2 h-8 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full mr-3"></span>
                                    Artikel Unggulan
                                </h2>
                                <div
                                    class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] bg-white">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                                    </div>
                                    <div class="p-8">
                                        <div class="flex items-center space-x-2 mb-4">
                                            <span
                                                class="px-3 py-1 bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 text-sm font-semibold rounded-full">
                                                Featured
                                            </span>
                                            <span class="text-gray-400 text-sm">•</span>
                                            <span
                                                class="text-gray-500 text-sm">{{ $posts->first()->created_at->format('d M Y') }}</span>
                                        </div>
                                        <h3
                                            class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 group-hover:text-blue-600 transition-colors duration-300">
                                            {{ $posts->first()->title }}
                                        </h3>
                                        <p class="text-gray-600 leading-relaxed mb-6 text-lg">
                                            {{ Str::limit(strip_tags($posts->first()->content), 200) }}
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                                                    {{ substr($posts->first()->user->name ?? 'U', 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-800">
                                                        {{ $posts->first()->user->name ?? 'Unknown' }}</p>
                                                    <p class="text-gray-500 text-sm">
                                                        {{ $posts->first()->created_at->format('H:i') }}</p>
                                                </div>
                                            </div>
                                            <a href="{{ route('artikel.publicShow', $posts->first()->slug) }}"
                                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-full hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                                                Baca Selengkapnya
                                                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Article Grid -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                                <span class="w-2 h-8 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full mr-3"></span>
                                Artikel Lainnya
                            </h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach ($posts->skip(1) as $post)
                                    <article
                                        class="group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-200">
                                        <div class="p-6">
                                            <div class="flex items-center space-x-2 mb-3">
                                                <span
                                                    class="text-gray-400 text-sm">{{ $post->created_at->format('d M Y') }}</span>
                                                <span class="text-gray-300">•</span>
                                                <span
                                                    class="text-gray-400 text-sm">{{ $post->created_at->format('H:i') }}</span>
                                            </div>

                                            <h3
                                                class="text-xl font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition-colors duration-300 line-clamp-2">
                                                {{ $post->title }}
                                            </h3>

                                            <p class="text-gray-600 mb-4 line-clamp-3 leading-relaxed">
                                                {{ Str::limit(strip_tags($post->content), 120) }}
                                            </p>

                                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                                <div class="flex items-center space-x-2">
                                                    <div
                                                        class="w-8 h-8 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                                        {{ substr($post->user->name ?? 'U', 0, 1) }}
                                                    </div>
                                                    <span
                                                        class="text-gray-700 font-medium text-sm">{{ $post->user->name ?? 'Unknown' }}</span>
                                                </div>

                                                <a href="{{ route('artikel.publicShow', $post->slug) }}"
                                                    class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold text-sm group-hover:translate-x-1 transition-all duration-300">
                                                    Baca
                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="flex justify-center mt-12">
                            <div class="bg-white rounded-xl shadow-lg p-4">
                                {{ $posts->links('pagination::tailwind') }}
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-8">
                            @include('partials.post-asside')
                        </div>
                    </div>
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
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .line-clamp-3 {
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            /* Custom scrollbar untuk sidebar */
            .sticky::-webkit-scrollbar {
                width: 4px;
            }

            .sticky::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            .sticky::-webkit-scrollbar-thumb {
                background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
                border-radius: 10px;
            }

            /* Hover animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .group:hover .group-hover\:animate-fadeInUp {
                animation: fadeInUp 0.3s ease-out;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Animate articles on scroll
                const articles = document.querySelectorAll('article');
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                articles.forEach(article => {
                    article.style.opacity = '0';
                    article.style.transform = 'translateY(20px)';
                    article.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
                    observer.observe(article);
                });

                // Add reading time estimation
                document.querySelectorAll('article').forEach(article => {
                    const content = article.querySelector('p').textContent;
                    const wordCount = content.split(' ').length;
                    const readingTime = Math.ceil(wordCount / 200); // 200 words per minute

                    const timeElement = document.createElement('span');
                    timeElement.className = 'text-gray-400 text-sm';
                    timeElement.textContent = `${readingTime} min read`;

                    const timeContainer = article.querySelector('.flex.items-center.space-x-2');
                    if (timeContainer) {
                        timeContainer.appendChild(document.createTextNode(' • '));
                        timeContainer.appendChild(timeElement);
                    }
                });
            });
        </script>
    @endpush
@endsection
