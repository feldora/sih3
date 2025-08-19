@extends('layouts.app-lain')
@section('title', 'Beranda')
@section('content')
    <div class="min-h-screen mb-20 pt-20">

        <!-- Category Filter Section -->
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-wrap gap-4 justify-center mb-8">
                <button class="btn btn-outline category-btn active" data-category="all">Semua</button>
                @foreach($categories as $category)
                    <button class="btn btn-outline category-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
                @endforeach
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loading" class="flex justify-center py-8 hidden">
            <span class="loading loading-spinner loading-lg text-primary"></span>
        </div>

        <!-- Content Grid -->
        <div class="container mx-auto px-4">
            <div id="posts-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Posts will be loaded here -->
            </div>
        </div>

        <!-- Pagination -->
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-center">
                <div class="join" id="pagination">
                    <!-- Pagination will be loaded here -->
                </div>
            </div>
        </div>


    </div>

    <script>
        let currentPage = 1;
        let currentCategory = 'all';
        let allCategoriesData = [];
        const categories = @json($categories);

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadPosts();
            setupCategoryButtons();
        });

        // Setup category button event listeners
        function setupCategoryButtons() {
            const categoryButtons = document.querySelectorAll('.category-btn');
            categoryButtons.forEach(button => {
                // Remove existing event listeners to prevent duplicates
                button.replaceWith(button.cloneNode(true));
            });
            
            // Re-select buttons after cloning and add fresh event listeners
            const freshButtons = document.querySelectorAll('.category-btn');
            freshButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    freshButtons.forEach(btn => btn.classList.remove('btn-primary', 'active'));
                    freshButtons.forEach(btn => btn.classList.add('btn-outline'));
                    
                    // Add active class to clicked button
                    this.classList.remove('btn-outline');
                    this.classList.add('btn-primary', 'active');
                    
                    // Set current category and reload posts
                    currentCategory = this.dataset.category;
                    currentPage = 1;
                    loadPosts();
                });
            });
        }

        // Load posts from API
        async function loadPosts() {
            showLoading();
            
            try {
                if (currentCategory === 'all') {
                    // Load 3 posts from each category
                    await loadAllCategoriesPosts();
                } else {
                    // Load 9 posts from specific category with pagination
                    let url = `/api/posts?per_page=9&page=${currentPage}&category=${currentCategory}`;
                    
                    const response = await fetch(url);
                    const data = await response.json();
                    
                    displayPosts(data.data);
                    updatePagination(data);
                }
                
            } catch (error) {
                console.error('Error loading posts:', error);
                showError('Gagal memuat data. Silakan coba lagi.');
            } finally {
                hideLoading();
            }
        }

        // Load 3 posts from each category
        async function loadAllCategoriesPosts() {
            try {
                allCategoriesData = [];
                
                // Fetch 3 posts from each category
                for (const category of categories) {
                    const response = await fetch(`/api/posts?per_page=3&category=${category.id}`);
                    const data = await response.json();
                    
                    if (data.data && data.data.length > 0) {
                        allCategoriesData.push({
                            category: category,
                            posts: data.data
                        });
                    }
                }
                
                displayAllCategoriesPosts();
                // Hide pagination for "all" view
                document.getElementById('pagination').innerHTML = '';
                
            } catch (error) {
                console.error('Error loading all categories posts:', error);
                throw error;
            }
        }

        // Display posts grouped by category (for "all" view)
        function displayAllCategoriesPosts() {
            const container = document.getElementById('posts-container');
            
            if (!allCategoriesData || allCategoriesData.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-16">
                        <div class="text-gray-400 text-6xl mb-4">📄</div>
                        <h3 class="text-xl font-semibold mb-2">Tidak ada data</h3>
                        <p class="text-gray-600">Belum ada informasi tersedia.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            
            allCategoriesData.forEach(categoryData => {
                html += `
                    <div class="col-span-full mb-12">
                        <div class="flex items-center justify-between mb-6 bg-cyan-500 p-3 rounded-t-lg">
                            <h2 class="text-2xl font-bold text-gray-800">${categoryData.category.name}</h2>
                            <button class="btn btn-outline btn-sm category-btn" data-category="${categoryData.category.id}">
                                Lihat Semua
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            ${categoryData.posts.map(post => createPostCard(post)).join('')}
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            
            // Setup event listeners only for the new "Lihat Semua" buttons
            const lihatSemuaButtons = container.querySelectorAll('.category-btn');
            lihatSemuaButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from top category buttons
                    const topButtons = document.querySelectorAll('.category-btn');
                    topButtons.forEach(btn => btn.classList.remove('btn-primary', 'active'));
                    topButtons.forEach(btn => btn.classList.add('btn-outline'));
                    
                    // Find and activate the corresponding top button
                    const categoryId = this.dataset.category;
                    const correspondingTopButton = document.querySelector(`.category-btn[data-category="${categoryId}"]`);
                    if (correspondingTopButton && correspondingTopButton !== this) {
                        correspondingTopButton.classList.remove('btn-outline');
                        correspondingTopButton.classList.add('btn-primary', 'active');
                    }
                    
                    // Set current category and reload posts
                    currentCategory = categoryId;
                    currentPage = 1;
                    loadPosts();
                });
            });
        }

        // Create post card HTML
        // function createPostCard(post) {
        //     return `
        //         <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300">
        //             ${post.image ? `
        //                 <figure class="h-48 overflow-hidden">
        //                     <img src="${post.image}" alt="${post.title}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
        //                 </figure>
        //             ` : ''}
        //             <div class="card-body">
        //                 <h2 class="card-title text-lg line-clamp-2">
        //                     ${post.title}
        //                     ${post.is_featured ? '<div class="badge badge-primary">Featured</div>' : ''}
        //                 </h2>
        //                 <p class="text-sm text-gray-600 line-clamp-3">${post.excerpt || post.content?.substring(0, 100) + '...' || ''}</p>
        //                 <div class="flex items-center justify-between mt-4">
        //                     <div class="text-xs text-gray-500">
        //                         <span class="badge badge-outline badge-sm">${post.category?.name || 'Umum'}</span>
        //                     </div>
        //                     <div class="text-xs text-gray-500">
        //                         ${formatDate(post.created_at)}
        //                     </div>
        //                 </div>
        //                 <div class="card-actions justify-end mt-4">
        //                     <button class="btn btn-primary btn-sm" onclick="viewPost(${post.id})">
        //                         Baca Selengkapnya
        //                     </button>
        //                 </div>
        //             </div>
        //         </div>
        //     `;
        // }
// Create post card HTML with media support
function createPostCard(post) {
    // Get first media if available
    const firstMedia = post.media && post.media.length > 0 ? post.media[0] : null;
    
    return `
        <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300">
            ${firstMedia ? createMediaDisplay(firstMedia) : ''}
            <div class="card-body">
                <h2 class="card-title text-lg line-clamp-2">
                    ${post.title}
                    ${post.is_featured ? '<div class="badge badge-primary">Featured</div>' : ''}
                </h2>
                <p class="text-sm text-gray-600 line-clamp-3">${post.excerpt || post.content?.substring(0, 100) + '...' || ''}</p>
                <div class="flex items-center justify-between mt-4">
                    <div class="text-xs text-gray-500">
                        <span class="badge badge-outline badge-sm">${post.category?.name || 'Umum'}</span>
                    </div>
                    <div class="text-xs text-gray-500">
                        ${formatDate(post.created_at)}
                    </div>
                </div>
                <div class="card-actions justify-end mt-4">
                    <button class="btn btn-primary btn-sm" onclick="viewPost(${post.id})">
                        Baca Selengkapnya
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Create media display based on media type
function createMediaDisplay(media) {
    const mimeType = media.mime_type;
    const originalUrl = media.original_url;
    const previewUrl = media.preview_url || originalUrl;
    const fileName = media.name || media.file_name;
    
    // Image types
    if (mimeType.startsWith('image/')) {
        return `
            <figure class="h-48 overflow-hidden">
                <img src="${previewUrl}" alt="${fileName}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
            </figure>
        `;
    }
    
    // Video types
    if (mimeType.startsWith('video/')) {
        return `
            <figure class="h-48 overflow-hidden bg-black flex items-center justify-center relative">
                <video class="w-full h-full object-cover" controls preload="metadata">
                    <source src="${originalUrl}" type="${mimeType}">
                    Your browser does not support the video tag.
                </video>
                <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                    📹 Video
                </div>
            </figure>
        `;
    }
    
    // PDF files
    if (mimeType === 'application/pdf') {
        return `
            <figure class="h-48 overflow-hidden relative border-2 border-red-200">
                <iframe 
                    src="${originalUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                    class="w-full h-full pointer-events-none"
                    frameborder="0">
                </iframe>
                <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-xs">
                    PDF
                </div>
                <div class="absolute inset-0 bg-transparent cursor-pointer" onclick="window.open('${originalUrl}', '_blank')" title="Buka PDF"></div>
            </figure>
        `;
    }

    
    // Excel files (.xlsx, .xls, .csv)
    if (mimeType.includes('spreadsheet') || 
        mimeType.includes('excel') || 
        mimeType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
        mimeType === 'application/vnd.ms-excel' ||
        mimeType === 'text/csv') {
        return `
            <figure class="h-48 overflow-hidden bg-green-50 flex flex-col items-center justify-center relative border-2 border-green-200">
                <div class="text-green-500 text-4xl mb-2">📊</div>
                <div class="text-sm text-green-700 font-medium text-center px-4">${fileName}</div>
                <div class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded text-xs">
                    Excel
                </div>
                <div class="absolute inset-0 bg-transparent cursor-pointer" onclick="window.open('${originalUrl}', '_blank')" title="Download Excel"></div>
            </figure>
        `;
    }
    
    // Word documents
    if (mimeType.includes('document') || 
        mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
        mimeType === 'application/msword') {
        return `
            <figure class="h-48 overflow-hidden bg-blue-50 flex flex-col items-center justify-center relative border-2 border-blue-200">
                <div class="text-blue-500 text-4xl mb-2">📝</div>
                <div class="text-sm text-blue-700 font-medium text-center px-4">${fileName}</div>
                <div class="absolute top-2 right-2 bg-blue-500 text-white px-2 py-1 rounded text-xs">
                    Word
                </div>
                <div class="absolute inset-0 bg-transparent cursor-pointer" onclick="window.open('${originalUrl}', '_blank')" title="Download Word"></div>
            </figure>
        `;
    }
    
    // PowerPoint files
    if (mimeType.includes('presentation') || 
        mimeType === 'application/vnd.openxmlformats-officedocument.presentationml.presentation' ||
        mimeType === 'application/vnd.ms-powerpoint') {
        return `
            <figure class="h-48 overflow-hidden bg-orange-50 flex flex-col items-center justify-center relative border-2 border-orange-200">
                <div class="text-orange-500 text-4xl mb-2">📊</div>
                <div class="text-sm text-orange-700 font-medium text-center px-4">${fileName}</div>
                <div class="absolute top-2 right-2 bg-orange-500 text-white px-2 py-1 rounded text-xs">
                    PPT
                </div>
                <div class="absolute inset-0 bg-transparent cursor-pointer" onclick="window.open('${originalUrl}', '_blank')" title="Download PowerPoint"></div>
            </figure>
        `;
    }
    
    // Audio files
    if (mimeType.startsWith('audio/')) {
        return `
            <figure class="h-48 overflow-hidden bg-purple-50 flex flex-col items-center justify-center relative border-2 border-purple-200">
                <div class="text-purple-500 text-4xl mb-2">🎵</div>
                <div class="text-sm text-purple-700 font-medium text-center px-4 mb-4">${fileName}</div>
                <audio controls class="w-full max-w-xs">
                    <source src="${originalUrl}" type="${mimeType}">
                    Your browser does not support the audio element.
                </audio>
                <div class="absolute top-2 right-2 bg-purple-500 text-white px-2 py-1 rounded text-xs">
                    Audio
                </div>
            </figure>
        `;
    }
    
    // Archive files (.zip, .rar, etc.)
    if (mimeType.includes('zip') || mimeType.includes('rar') || mimeType.includes('archive')) {
        return `
            <figure class="h-48 overflow-hidden bg-gray-50 flex flex-col items-center justify-center relative border-2 border-gray-200">
                <div class="text-gray-500 text-4xl mb-2">🗜️</div>
                <div class="text-sm text-gray-700 font-medium text-center px-4">${fileName}</div>
                <div class="absolute top-2 right-2 bg-gray-500 text-white px-2 py-1 rounded text-xs">
                    Archive
                </div>
                <div class="absolute inset-0 bg-transparent cursor-pointer" onclick="window.open('${originalUrl}', '_blank')" title="Download Archive"></div>
            </figure>
        `;
    }
    
    // Default for other file types
    return `
        <figure class="h-48 overflow-hidden bg-gray-50 flex flex-col items-center justify-center relative border-2 border-gray-200">
            <div class="text-gray-500 text-4xl mb-2">📁</div>
            <div class="text-sm text-gray-700 font-medium text-center px-4">${fileName}</div>
            <div class="text-xs text-gray-500 mt-1">${mimeType}</div>
            <div class="absolute top-2 right-2 bg-gray-500 text-white px-2 py-1 rounded text-xs">
                File
            </div>
            <div class="absolute inset-0 bg-transparent cursor-pointer" onclick="window.open('${originalUrl}', '_blank')" title="Download File"></div>
        </figure>
    `;
}
        // Update pagination
        function updatePagination(data) {
            const pagination = document.getElementById('pagination');
            const totalPages = data.last_page || 1;
            
            if (totalPages <= 1) {
                pagination.innerHTML = '';
                return;
            }

            let paginationHTML = '';
            
            // Previous button
            if (data.prev_page_url) {
                paginationHTML += `<button class="join-item btn" onclick="changePage(${currentPage - 1})">«</button>`;
            }
            
            // Page numbers
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);
            
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentPage ? 'btn-active' : '';
                paginationHTML += `<button class="join-item btn ${activeClass}" onclick="changePage(${i})">${i}</button>`;
            }
            
            // Next button
            if (data.next_page_url) {
                paginationHTML += `<button class="join-item btn" onclick="changePage(${currentPage + 1})">»</button>`;
            }
            
            pagination.innerHTML = paginationHTML;
        }

        // Display posts in grid (for specific category view)
        function displayPosts(posts) {
            const container = document.getElementById('posts-container');
            
            if (!posts || posts.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-16">
                        <div class="text-gray-400 text-6xl mb-4">📄</div>
                        <h3 class="text-xl font-semibold mb-2">Tidak ada data</h3>
                        <p class="text-gray-600">Belum ada informasi untuk kategori ini.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = posts.map(post => createPostCard(post)).join('');
        }

        // Change page
        function changePage(page) {
            currentPage = page;
            loadPosts();
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Utility functions
        function showLoading() {
            document.getElementById('loading').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loading').classList.add('hidden');
        }

        function showError(message) {
            const container = document.getElementById('posts-container');
            container.innerHTML = `
                <div class="col-span-full">
                    <div class="alert alert-error">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>${message}</span>
                    </div>
                </div>
            `;
        }

        function formatDate(dateString) {
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                timeZone: 'Asia/Jakarta'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        // Add line-clamp utility classes to your CSS if not already available
        const style = document.createElement('style');
        style.textContent = `
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
        `;
        document.head.appendChild(style);
    </script>
@endsection