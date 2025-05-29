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
    <form action="{{ route('admin.posts.store') }}" method="POST">
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

            <!-- Content Field -->
            <div>
                <label for="content" class="label">Content</label>
                <textarea id="content" name="content" class="textarea textarea-bordered w-full" rows="6" required>{{ old('content') }}</textarea>
                @error('content')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <x-map-picker 
                name="location" 
                label="Location" 
                :defaultLocation="old('location', ['lat' => -6.1751, 'lng' => 106.8650])"
                placeholder="Click to select location on map"
            />

            <!-- Category Field -->
            <x-input-popup 
                name="category_id" 
                label="Category" 
                :items="$categories" 
                itemLabel="name" 
                placeholder="Select a category"
            />

            <!-- Tags Field -->
            <x-select-multiple
                name="tags[]"
                label="Tags"
                :items="$tags"
                itemLabel="name"
                :selectedItems="old('tags', [])"
                placeholder="Select tags..."
            />

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
                <button type="submit" class="btn btn-primary">Save Post</button>
            </div>
        </div>
    </form>
</div>
@endsection
