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

                <div class="article-hero py-16 md:py-24">
                    <div class="floating-particles">
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                    </div>

                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="article-hero-content max-w-4xl mx-auto text-center fade-in">
                            <span class="font-bold mb-6 leading-tight break-words max-w-full"
                                style="font-size:clamp(1.5rem,4vw,2.25rem);word-break:break-word;">
                                {{ $post->title }}
                            </span>

                            <div class="flex flex-wrap justify-center items-center gap-4 text-sm md:text-base">
                                <div class="meta-badge stagger-animation">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $post->user->name ?? 'Unknown' }}
                                </div>

                                <div class="meta-badge stagger-animation">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ $post->created_at->format('d M Y H:i') }}
                                </div>

                                @if ($post->category)
                                    <div class="meta-badge category-badge stagger-animation">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        {{ $post->category->name }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($post)
                <div class="grid lg:grid-cols-4 gap-8">
                    <!-- Main Content Area -->
                    <div class="lg:col-span-3">
                        <div class="mb-12">
                            <div class="relative overflow-hidden rounded-2xl shadow-xl bg-white">
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10 opacity-0">
                                </div>
                                <div class="p-8">
                                    <img src="https://picsum.photos/800/300?random={{ $post->id }}" alt="Artikel Image"
                                        class="rounded w-full h-64 object-cover mb-6">
                                    <p class="text-gray-600 leading-relaxed mb-6 text-lg">
                                        {!! $post->content !!}
                                    </p>
                                    <hr class="my-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                                                {{ substr($post->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">
                                                    {{ $post->user->name ?? 'Unknown' }}</p>
                                                <p class="text-gray-500 text-sm">
                                                    {{ $post->created_at->format('H:i') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-gray-400 text-sm">•</span>
                                            <span class="text-gray-500 text-sm">
                                                {{ $post->views }} Views
                                            </span>
                                        </div>
                                        <a href="{{ route('artikel.publicIndex') }}"
                                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-full hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                                            <svg class="w-4 h-4 ml-2 group-hover:-translate-x-1 transition-transform duration-300"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                                            </svg>
                                            Kembali
                                        </a>
                                    </div>
                                </div>
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
