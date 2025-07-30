@extends('layouts.admin')

@section('title', 'Blank Page - Snow Clone')

@section('content')
    <div class="container mx-auto px-4">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-header px-6 py-4 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h3 class="card-title text-xl font-bold">Title blank Page</h3>

                    <div class="flex justify-end items-center mb-6 gap-5">
                        <a href="#" class="btn btn-sm btn-info">
                          <i class="fas fa-file-import mr-1"></i>
                          Import Excel
                        </a>
                        <a href="#" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i>
                            Tambah Data
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-6">
                <div class="overflow-x-auto">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Veritatis officia beatae nisi laboriosam
                        sapiente esse distinctio! A minima cupiditate aperiam hic rerum, pariatur enim molestiae, repellat
                        consequatur inventore, aut architecto!</p>
                </div>
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
