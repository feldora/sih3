@extends('layouts.admin')

@section('title', 'Edit ' . ($title ?? 'data'))

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-header px-6 py-4 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="text-3xl font-bold mb-4">Edit {{ $title ?? 'data' }}</h3>
            </div>
        </div>
        <hr>
        <div class="card-body p-6">
            <div class="overflow-x-auto">

                @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ $actionPost }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Title Field -->
                        <div>
                            <label for="title" class="label">Title</label>
                            <input type="text" id="title" name="title" class="input input-bordered w-full"
                                value="{{ old('title', $post->title) }}" required>
                            @error('title')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload Field -->
                        <x-file-uploads
                            id="fileUploads"
                            name="fileUploads"
                            label="File Uploads"
                            accept="*"
                            :multiple="true"
                            :value="old('fileUploads', $post->media->map(fn($m) => $m->original_url)->toArray())"
                        />
                        
                        <!-- Content Field -->
                        <x-text-editor
                            name="content"
                            label="Deskripsi"
                            value="{!! old('content', $post->content) !!}"
                            placeholder="Write your content here...">{{
                                old('content', $post->content)
                            }}
                        </x-text-editor>

                        <!-- Hidden Fields -->
                        <input type="hidden" name="category_id" value="{{ old('category_id', $post->category->name) }}">
                        <input type="hidden" name="status" value="{{ old('status', $post->status ?? 'published') }}">
                        {{-- Jika tags array kosong, bisa sesuaikan --}}
                        <input type="hidden" name="tags[]" value="">

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button id="simpan" type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

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
