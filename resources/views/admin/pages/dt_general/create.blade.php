<!-- resources/views/admin/posts/create.blade.php -->

@extends('layouts.admin')

@section('title', 'Tambah data ' . ($title ?? 'data'))

@section('content')
    <div class="container mx-auto px-4">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-header px-6 py-4 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h5 class="text-3xl font-bold mb-4">Tambah {{ $title ?? 'data' }}</h5>

                </div>
            </div>
            <hr>
            <div class="card-body p-6">
                <div class="overflow-x-auto">
                    <!-- Jika ada error validasi -->
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
                        <div class="space-y-6">
                            <x-pos-pantau-map-selector name="pos_pantau_id" />
                            <!-- Title Field -->
                            <div>
                                <label for="title" class="label">Title</label>
                                <input type="text" id="title" name="title" class="input input-bordered w-full"
                                    value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File Upload Field -->
                            <x-file-uploads id="fileUploads" name="fileUploads" label="File Uploads" accept="*"
                                multiple="{{ $multiple ?? false }}" />


                            <!-- content Field -->
                            <x-text-editor name="content" label="Deskripsi" placeholder="Write your content here..."/>
                            <input type="text" name="category_id" value="{{ $categories->name }}" hidden>
                            @foreach ($tags as $tag )                                
                                <input type="text" name="tags[]" value="{{ $tag->id }}" hidden>
                            @endforeach
                            <input type="text" name="status" value="published" hidden>
                            <input type="text" name="type" value="data" hidden>
                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button id="simpan" type="submit" class="btn btn-primary">Simpan</button>
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
        document.addEventListener('DOMContentLoaded', function() {

        });
    </script>
@endpush
