@extends('layouts.admin')

@section('title', "{{ $title ?? 'Data' }}")

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <div class="flex items-center mb-6 ">
            <!-- Judul di kiri -->
            <h3 class="text-2xl font-bold flex-1">{{ $title }}</h3>

            <!-- Kotak pencarian di tengah -->
            <input type="text" name="search" id="search-input" placeholder="Cari..." 
              value="{{ request('search') }}"
              class="input input-bordered input-sm w-150"
            />

            <!-- Tombol tambah di kanan -->
            <div class="flex justify-end flex-1">
                <a href="{{ route('admin.geologi.dmat.create') }}" class="btn btn-sm btn-primary"> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  <span class="hidden sm:inline">Tambah</span>
                </a>
            </div>
        </div>

        <hr class="border-gray-300 mb-6 shadow-sm">

        <!-- Loading state -->
        <div id="loading" class="flex justify-center items-center py-12" style="display: none;">
            <div class="loading loading-spinner loading-lg"></div>
        </div>

        <!-- Grid 3 kolom cards -->
        <div id="posts-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Posts will be loaded here via JavaScript -->
        </div>

        <!-- No data message -->
        <div id="no-data" class="text-center py-12" style="display: none;">
            <p class="text-gray-500">Tidak ada data yang ditemukan.</p>
        </div>

        <!-- Pagination -->
        <div id="pagination-container" class="flex justify-center mt-8 space-x-3">
            <!-- Pagination will be loaded here via JavaScript -->
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
  .clickabel {
    cursor: pointer;
  }
</style>
@endpush

@push('scripts')
<script>

    const actionUrls = JSON.parse('{!! json_encode($actionUrls) !!}');
    console.log(actionUrls);
    
    
window.addEventListener('load', function() {
  if (typeof $ === 'undefined') {
    console.error('jQuery is not loaded yet.');
    return;
  }

  let searchTimeout = null;

  document.getElementById('search-input').addEventListener('input', function(e) {
    const query = e.target.value;

    // Bersihkan timeout sebelumnya
    clearTimeout(searchTimeout);

    // Tunggu 500ms setelah user berhenti mengetik
    searchTimeout = setTimeout(() => {
      if (query.length >= 1 || query.length === 0) {
        loadPosts({ search: query });
      }
    }, 500);
  });


  // Toast function
  function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-top toast-end`;
    toast.innerHTML = `
      <div class="alert alert-${type}">
        <span>${message}</span>
      </div>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
  }

  // Load posts from API
  function loadPosts(extraParams = { 'per_page' : 9, 'category': "{{ $categories->id ?? '' }}" })  {
    const loading = document.getElementById('loading');
    const container = document.getElementById('posts-container');
    const noData = document.getElementById('no-data');
    const paginationContainer = document.getElementById('pagination-container');

    // Show loading
    loading.style.display = 'flex';
    container.style.display = 'none';
    noData.style.display = 'none';

    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    // Tambahkan atau timpa parameter dengan yang dari extraParams
    for (const [key, value] of Object.entries(extraParams)) {
      urlParams.set(key, value);
    }
    const queryString = urlParams.toString();

    // Fetch data from API
    fetch(`/api/posts?${queryString}`)
      .then(response => response.json())
      .then(data => {
        loading.style.display = 'none';
        
        if (data.data && data.data.length > 0) {
          container.style.display = 'grid';
          renderPosts(data.data);
          renderPagination(data);
        } else {
          noData.style.display = 'block';
          paginationContainer.innerHTML = '';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        loading.style.display = 'none';
        noData.style.display = 'block';
        showToast('Terjadi kesalahan saat memuat data', 'error');
      });
  }

  // Render posts
function renderPosts(posts) {
  const container = document.getElementById('posts-container');
  container.innerHTML = '';

  posts.forEach((post, index) => {
    const title = post.title || 'Tidak ada judul';
    const excerpt = post.content ? post.content.substring(0, 120) + '...' : 'Tidak ada deskripsi tersedia.';
    const categoryName = post.category ? post.category.name : 'Uncategorized';
    const authorName = post.user ? post.user.name : 'Unknown';
    const statusBadge = post.status === 'published' 
      ? '<span class="badge badge-success badge-xs">Published</span>' 
      : '<span class="badge badge-warning badge-xs">Draft</span>';
    const views = post.views || 0;
    const contentUrl = actionUrls.show.replace(':slug',  post.slug);
    // Format tanggal
    const createdDate = new Date(post.created_at).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    });

    // Generate preview isi media
    let mediaPreviewHtml = `<img src="/images/sih3.png" class="w-full h-full object-cover" alt="${title}" />`;

    if (post.media && post.media.length > 0) {
      const media = post.media[0];
      const mime = media.mime_type;
      const url = media.original_url;

      if (mime.startsWith('image/')) {
        mediaPreviewHtml = `<img src="${media.preview_url || url}" class="w-full h-full object-cover" alt="${title}" />`;
      } else if (mime === 'application/pdf') {
        mediaPreviewHtml = `
          <iframe src="${url}#toolbar=0" type="application/pdf" class="w-full h-full" style="border: none;"></iframe>
        `;
      } else {
        // const contentUrl = actionUrls.show.replace(':slug', post.slug);
        mediaPreviewHtml = `
          <div class="flex flex-col items-center justify-center h-full text-white p-4 text-center">
            <p class="text-sm">Preview tidak tersedia untuk file ini</p>
            <a href="${contentUrl}" target="_blank" class="text-blue-400 underline mt-2">Buka file</a>
          </div>
        `;
      }
    }

    // Template kartu
    const cardHtml = `
      <div class="card bg-base-200 shadow-md rounded-lg overflow-hidden relative" style="height: 360px;">

        <!-- Tombol Edit & Hapus -->
        <div class="absolute top-2 right-2 flex gap-2 z-10">
          <button class="btn btn-sm shadow-xl/30 btn-circle btn-ghost hover:bg-base-300 bg-warning" title="Edit" onclick="editPost('${post.id}')">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.536-6.536a2 2 0 012.828 0l1.172 1.172a2 2 0 010 2.828L13 15H9v-4z" />
            </svg>
          </button>
          <button class="btn btn-sm shadow-xl/30 btn-circle btn-ghost hover:bg-base-300 bg-error" title="Hapus" onclick="deletePost('${post.id}')">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 012 2H5a2 2 0 012-2h10z" />
            </svg>
          </button>
        </div>

        <figure class="h-full overflow-hidden">
          ${mediaPreviewHtml}
        </figure>
        
        <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-black bg-opacity-60 text-white p-4 flex flex-col justify-between clickable" onclick="viewDetail('${contentUrl}')">
          <div>
            <h2 class="card-title text-lg line-clamp-2 clickable">${title}</h2>
            <p class="text-sm opacity-90 line-clamp-2 clickable">${excerpt}</p>
          </div>
          <div class="card-actions justify-end mt-2">
            ${statusBadge}
          </div>
        </div>
      </div>
    `;

    container.innerHTML += cardHtml;
  });
}


  // Render pagination
  function renderPagination(data) {
    const container = document.getElementById('pagination-container');
    
    if (!data.last_page || data.last_page <= 1) {
      container.innerHTML = '';
      return;
    }

    let paginationHtml = '';
    const currentPage = data.current_page;
    const lastPage = data.last_page;

    // Previous button
    if (currentPage > 1) {
      paginationHtml += `<a href="#" onclick="goToPage(${currentPage - 1})" class="btn btn-sm btn-square">«</a>`;
    } else {
      paginationHtml += `<a href="#" class="btn btn-sm btn-square btn-disabled">«</a>`;
    }

    // Page numbers
    for (let i = Math.max(1, currentPage - 2); i <= Math.min(lastPage, currentPage + 2); i++) {
      if (i === currentPage) {
        paginationHtml += `<a href="#" class="btn btn-sm btn-primary">${i}</a>`;
      } else {
        paginationHtml += `<a href="#" onclick="goToPage(${i})" class="btn btn-sm">${i}</a>`;
      }
    }

    // Next button
    if (currentPage < lastPage) {
      paginationHtml += `<a href="#" onclick="goToPage(${currentPage + 1})" class="btn btn-sm btn-square">»</a>`;
    } else {
      paginationHtml += `<a href="#" class="btn btn-sm btn-square btn-disabled">»</a>`;
    }

    container.innerHTML = paginationHtml;
  }

  // Go to specific page
  window.goToPage = function(page) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('page', page);
    window.location.search = urlParams.toString();
  };

  // View detail
  window.viewDetail = function(contentUrl) {
    window.location.href = contentUrl;
  };

  // Apply filter
  window.applyFilter = function() {
    const form = document.querySelector('form');
    const selects = document.querySelectorAll('select[name]');
    
    selects.forEach(select => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = select.name;
      input.value = select.value;
      form.appendChild(input);
    });
    
    form.submit();
  };

  window.editPost = function(slug) {
    window.location.href = `/admin/posts/${slug}/edit`; // Sesuaikan dengan route edit-mu
  };

  window.deletePost = function(id) {
    if (confirm('Yakin ingin menghapus item ini?')) {
      fetch(actionUrls.destroy.replace(':id', id), {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => {
        if (response.ok) {
          showToast('Berhasil dihapus', 'success');
          loadPosts();
        } else {
          showToast('Gagal menghapus item', 'error');
        }
      })
      .catch(error => {
        console.error(error);
        showToast('Terjadi kesalahan', 'error');
      });
    }
  };

  // Initial load
  loadPosts();

  // Session messages
  @if (session('error'))
    showToast("{{ session('error') }}", "error");
  @endif

  @if (session('success'))
    showToast("{{ session('success') }}", "success");
  @endif

  @if (session('warning'))
    showToast("{{ session('warning') }}", "warning");
  @endif
});
</script>
@endpush