@extends('layouts.admin')

@section('title', 'Cards Vertikal dengan Proporsi Gambar 2/3')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <div class="flex items-center mb-6 ">
            <!-- Judul di kiri -->
            <h3 class="text-2xl font-bold flex-1">Daftar Item</h3>

            <!-- Kotak pencarian di tengah -->
            <form method="GET" action="{{ url()->current() }}" class="flex justify-center flex-1">
                <input type="text" name="search" placeholder="Cari..." 
                      value="{{ request('search') }}"
                      class="input input-bordered input-sm w-150" />
            </form>

            <!-- Tombol tambah di kanan -->
            <div class="flex justify-end flex-1">
                <a href="#" class="btn btn-sm btn-primary"> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  <span class="hidden sm:inline">Tambah</span>
                </a>
            </div>
        </div>
        <hr class="border-gray-300 mb-6 shadow-sm">
        <!-- Grid 3 kolom cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @for ($i = 1; $i <= 6; $i++)
              <div class="card bg-base-200 shadow-md rounded-lg overflow-hidden relative" style="height: 360px;">
                  <!-- Figure full tinggi -->
                  <figure class="h-full overflow-hidden">
                      <img src="https://picsum.photos/800/360?random={{ $i+1 }}" class="w-full h-full object-cover" />
                  </figure>

                  <!-- Konten overlay di bawah -->
                  <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-black bg-opacity-60 text-white p-4 flex flex-col justify-between">
                      <div>
                          <h2 class="card-title text-lg">Judul Card {{ $i }}</h2>
                          <p class="text-sm">Deskripsi singkat card ke-{{ $i }} yang bisa berisi ringkasan konten.</p>
                      </div>
                      <div class="card-actions justify-end">
                          <button class="btn btn-sm btn-primary">Detail</button>
                      </div>
                  </div>
              </div>
            @endfor
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-8 space-x-3">
            <a href="#" class="btn btn-sm btn-square btn-disabled">«</a>
            <a href="#" class="btn btn-sm btn-primary">1</a>
            <a href="#" class="btn btn-sm">2</a>
            <a href="#" class="btn btn-sm">3</a>
            <a href="#" class="btn btn-sm btn-square">»</a>
        </div>
    </div>
</div>
@endsection

@push('styles')
@endpush

@push('scripts')
  <script>
    window.addEventListener('load', function() {
      if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded yet.');
        return;
      }

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
