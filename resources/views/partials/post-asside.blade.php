<aside class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-xl p-6 sticky top-10 border border-gray-100 backdrop-blur-sm">
    <!-- Popular Articles Section -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-5">
            <div class="p-2 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                Artikel Populer
            </h3>
        </div>
        
        <div class="space-y-4">
            @foreach($popularPosts as $index => $pop)
                <div class="group relative p-4 rounded-xl bg-white/70 border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                    <!-- Ranking Badge -->
                    {{-- <div class="absolute -top-2 -left-2 w-8 h-8 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-lg">
                        {{ $index + 1 }}
                    </div>
                     --}}
                    <a href="{{ route('artikel.publicShow', $pop->slug) }}" class="block">
                        <h4 class="text-blue-700 hover:text-blue-800 font-semibold text-sm leading-relaxed mb-2 group-hover:text-blue-900 transition-colors duration-200 line-clamp-2">
                            {{ $pop->title }}
                        </h4>
                        
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                                <span>{{ $pop->user->name ?? 'Unknown' }}</span>
                            </div>
                            
                            <div class="flex items-center gap-1 text-amber-600">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                                <span class="font-medium">Populer</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Categories Section -->
    <div>
        <div class="flex items-center gap-3 mb-5">
            <div class="p-2 bg-gradient-to-r from-green-500 to-teal-600 rounded-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h4 class="text-lg font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                Kategori
            </h4>
        </div>
        
        <div class="space-y-2">
            <!-- All Categories Link -->
            <a href="{{ route('artikel.publicIndex') }}" 
               class="group flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 hover:from-blue-100 hover:to-indigo-100 hover:border-blue-200 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full group-hover:scale-125 transition-transform duration-200"></div>
                    <span class="text-gray-700 font-medium group-hover:text-blue-800 transition-colors duration-200">
                        Semua Kategori
                    </span>
                </div>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            
            <!-- Individual Categories -->
            @foreach($categories as $category)
                <a href="{{ route('artikel.publicIndex', ['category' => $category->slug]) }}" 
                   class="group flex items-center justify-between p-3 rounded-xl bg-white/50 border border-gray-100 hover:bg-white hover:border-gray-200 hover:shadow-md transition-all duration-300 hover:-translate-y-0.5">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-gradient-to-r from-gray-400 to-gray-600 rounded-full group-hover:scale-125 group-hover:from-green-500 group-hover:to-teal-600 transition-all duration-200"></div>
                        <span class="text-gray-600 group-hover:text-gray-800 transition-colors duration-200 font-medium">
                            {{ $category->name }}
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        @if(isset($category->articles_count))
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full group-hover:bg-green-100 group-hover:text-green-700 transition-colors duration-200">
                                {{ $category->articles_count }}
                            </span>
                        @endif
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-green-600 group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-100/30 to-purple-100/30 rounded-full -translate-y-10 translate-x-10 blur-xl"></div>
    <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-green-100/30 to-teal-100/30 rounded-full translate-y-8 -translate-x-8 blur-xl"></div>
</aside>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar for better aesthetics */
aside::-webkit-scrollbar {
    width: 4px;
}

aside::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
}

aside::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
    border-radius: 2px;
}

aside::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #2563eb, #7c3aed);
}
</style>