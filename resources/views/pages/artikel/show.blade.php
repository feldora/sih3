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
                                    <x-media-display :media="$post->media->first()" />

                                    <!-- Article Content -->
                                    <div class="prose prose-lg max-w-none mb-8">
                                        <div class="text-gray-700 leading-relaxed text-lg">
                                            {!! $post->content !!}
                                        </div>
                                    </div>

                                    <hr class="my-8 border-gray-200">

                                    <!-- Article Footer -->
                                    <footer class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                        <!-- Author Info -->
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                                {{ substr($post->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900 text-lg">
                                                    {{ $post->user->name ?? 'Unknown' }}
                                                </h3>
                                            </div>
                                        </div>

                                        <!-- Back Button -->
                                        <div class="flex-shrink-0">
                                            <a href="{{ route('artikel.publicIndex') }}"
                                                class="inline-flex items-center
                                                px-3 py-1.5
                                                bg-gradient-to-r from-blue-500 to-purple-600
                                                text-white text-sm font-medium
                                                rounded-full
                                                hover:from-blue-600 hover:to-purple-700
                                                transition-colors duration-200
                                                shadow-sm
                                                focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50
                                                group">
                                                <svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                                                </svg>
                                                Kembali ke Artikel
                                            </a>
                                        </div>

                                    </footer>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-8 space-y-6">
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

    @push('_styles')
        <style>
            /* Prose styling for article content */
            .prose {
                max-width: none;
            }

            .prose h1,
            .prose h2,
            .prose h3,
            .prose h4,
            .prose h5,
            .prose h6 {
                color: #1f2937;
                font-weight: 600;
                margin-top: 2rem;
                margin-bottom: 1rem;
            }

            .prose p {
                margin-bottom: 1.5rem;
                line-height: 1.75;
            }

            .prose ul,
            .prose ol {
                margin: 1.5rem 0;
                padding-left: 2rem;
            }

            .prose li {
                margin-bottom: 0.5rem;
            }

            .prose blockquote {
                border-left: 4px solid #3b82f6;
                padding-left: 1.5rem;
                margin: 2rem 0;
                font-style: italic;
                color: #4b5563;
                background-color: #f8fafc;
                padding: 1.5rem;
                border-radius: 0.5rem;
            }

            .prose code {
                background-color: #f1f5f9;
                color: #dc2626;
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                font-size: 0.875em;
            }

            .prose pre {
                background-color: #1e293b;
                color: #e2e8f0;
                padding: 1.5rem;
                border-radius: 0.5rem;
                overflow-x: auto;
                margin: 1.5rem 0;
            }

            .prose img {
                border-radius: 0.5rem;
                margin: 2rem auto;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }

            .prose a {
                color: #3b82f6;
                text-decoration: none;
                font-weight: 500;
                border-bottom: 1px solid transparent;
                transition: border-color 0.2s ease;
            }

            .prose a:hover {
                border-bottom-color: #3b82f6;
            }

            /* Meta badges styling */
            .meta-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.5rem 1rem;
                background: rgba(255, 255, 255, 0.9);
                border-radius: 9999px;
                font-weight: 500;
                color: #374151;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .category-badge {
                background: rgba(59, 130, 246, 0.1);
                color: #2563eb;
                border-color: rgba(59, 130, 246, 0.2);
            }

            /* Floating particles animation */
            .floating-particles {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                pointer-events: none;
            }

            .particle {
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(59, 130, 246, 0.3);
                border-radius: 50%;
                animation: float 6s ease-in-out infinite;
            }

            .particle:nth-child(1) {
                left: 20%;
                animation-delay: 0s;
            }

            .particle:nth-child(2) {
                left: 40%;
                animation-delay: 2s;
            }

            .particle:nth-child(3) {
                left: 60%;
                animation-delay: 4s;
            }

            .particle:nth-child(4) {
                left: 80%;
                animation-delay: 6s;
            }

            @keyframes float {
                0%, 100% {
                    transform: translateY(0px) rotate(0deg);
                    opacity: 0;
                }
                10%, 90% {
                    opacity: 1;
                }
                50% {
                    transform: translateY(-20px) rotate(180deg);
                }
            }

            /* Stagger animation for meta badges */
            .stagger-animation {
                animation: slideInUp 0.6s ease-out forwards;
                opacity: 0;
                transform: translateY(20px);
            }

            .stagger-animation:nth-child(1) {
                animation-delay: 0.1s;
            }

            .stagger-animation:nth-child(2) {
                animation-delay: 0.2s;
            }

            .stagger-animation:nth-child(3) {
                animation-delay: 0.3s;
            }

            @keyframes slideInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Fade in animation */
            .fade-in {
                animation: fadeIn 1s ease-out;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush

    @push('_scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Image zoom functionality for prose images
                document.querySelectorAll('.prose img').forEach(img => {
                    img.style.cursor = 'zoom-in';
                    img.addEventListener('click', function() {
                        const modal = document.createElement('div');
                        modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
                        modal.innerHTML = `
                            <div class="relative max-w-4xl max-h-full">
                                <img src="${this.src}" alt="${this.alt}" class="max-w-full max-h-full object-contain rounded-lg">
                                <button class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        `;

                        document.body.appendChild(modal);
                        document.body.style.overflow = 'hidden';

                        // Close modal
                        modal.addEventListener('click', function(e) {
                            if (e.target === modal || e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                                document.body.removeChild(modal);
                                document.body.style.overflow = 'auto';
                            }
                        });

                        // Close with ESC key
                        const escHandler = function(e) {
                            if (e.key === 'Escape') {
                                if (document.body.contains(modal)) {
                                    document.body.removeChild(modal);
                                    document.body.style.overflow = 'auto';
                                }
                                document.removeEventListener('keydown', escHandler);
                            }
                        };
                        document.addEventListener('keydown', escHandler);
                    });
                });
            });
        </script>
    @endpush
@endsection