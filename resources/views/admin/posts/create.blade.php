<!-- resources/views/admin/posts/create.blade.php -->

@extends('layouts.admin')

@section('title', 'Create Post')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">Create New Post</h1>

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

    <!-- Form untuk membuat post -->
    {{-- <form action="{{ route('admin.posts.store') }}" method="POST"> --}}
        <form action="/admin/posts" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-6">
            <!-- Type Field -->
            <div>
                <label for="type" class="label">Type</label>
                <select id="type" name="type" class="select select-bordered w-full">
                    <option value="article" {{ old('type') == 'article' ? 'selected' : '' }}>Article</option>
                    <option value="news" {{ old('type') == 'news' ? 'selected' : '' }}>News</option>
                </select>
                @error('type')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Title Field -->
            <div>
                <label for="title" class="label">Title</label>
                <input type="text" id="title" name="title" class="input input-bordered w-full" value="{{ old('title') }}" required>
                @error('title')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- File Upload Field -->
            <x-file-input 
                id="fileUpload" 
                name="featured_image" 
                label="Featured Image"
            />

            
            <!-- content Field -->
            <x-text-editor 
                name="content"
                label="Post Content"
                placeholder="Write your post content here..."
                required
            />

            <!-- Category Field -->
            <x-input-popup 
                name="category_id" 
                label="Category" 
                :items="$categories" 
                itemLabel="name" 
                placeholder="Select a category"
            />
            
            <x-choices-multiple
                name="tags"
                label="Tags"
                :options="$tags->map(function($tag) {
                    return ['value' => $tag->id, 'label' => $tag->name];
                })->toArray()"
                :selected="old('tags', [])"
                placeholder="Select tags..."
            />
            
            <div>
                <label for="tag" class="label">New Tag</label>
                <small class="text-gray-500">Enter a new tag, separated by commas</small>
                <input type="text" id="tag" name="tag" class="input input-bordered w-full" value="{{ old('tag') }}" list="tags-datalist">
                <datalist id="tags-datalist">
                    @foreach($tags as $tag)
                        <option value="{{ $tag->name }}">
                    @endforeach
                </datalist>
                <input list="tags-datalist" style="display:none;">
                <div class="flex flex-wrap gap-2 mt-2" id="tag-list">
                </div>
                @error('tag')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>


            <!-- Status Field -->
            <div>
                <label for="status" class="label">Status</label>
                <select id="status" name="status" class="select select-bordered w-full">
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                </select>
                @error('status')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button id="simpan" type="submit" class="btn btn-primary">Save Post</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tagInput = document.getElementById('tag');
        const tagList = document.getElementById('tag-list');

        tagInput.addEventListener('input', function () {
            const tags = tagInput.value.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
            tagList.innerHTML = '';
            tags.forEach(tag => {
                const span = document.createElement('span');
                span.className = 'inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-medium mr-2 mb-2';
                span.textContent = tag;
                tagList.appendChild(span);
            });
        });
    });
</script>

@endpush