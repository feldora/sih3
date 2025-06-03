{{-- filepath: /mnt/data/laragon/www/sih3-sulteng/resources/views/admin/posts/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Post')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">Edit Post</h1>

    @if ($errors->any())
        <div class="alert alert-error mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <!-- Type Field -->
            <div>
                <label for="type" class="label">Type</label>
                <select id="type" name="type" class="select select-bordered w-full">
                    <option value="article" {{ old('type', $post->type) == 'article' ? 'selected' : '' }}>Article</option>
                    <option value="news" {{ old('type', $post->type) == 'news' ? 'selected' : '' }}>News</option>
                </select>
                @error('type')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Title Field -->
            <div>
                <label for="title" class="label">Title</label>
                <input type="text" id="title" name="title" class="input input-bordered w-full" value="{{ old('title', $post->title) }}" required>
                @error('title')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- File Upload Field -->
            <x-file-input 
                id="fileUpload" 
                name="featured_image" 
                label="Featured Image"
                :value="old('featured_image', $post->getFirstMediaUrl('featured_image'))"
            />

            <!-- content Field -->
            <x-text-editor 
                name="content"
                label="Post Content"
                placeholder="Write your post content here..."
                :value="old('content', $post->content)"
                required
            />

            <!-- Category Field -->
            <x-input-popup 
                name="category_id" 
                label="Category" 
                :items="$categories" 
                itemLabel="name" 
                :value="old('category_id', $post->category->name ?? '')"
                placeholder="Select a category"
            />
            
            <x-choices-multiple
                name="tags"
                label="Tags"
                :options="$tags->map(function($tag) {
                    return ['value' => $tag->id, 'label' => $tag->name];
                })->toArray()"
                :selected="old('tags', $post->tags->pluck('id')->toArray())"
                placeholder="Select tags..."
            />

            <!-- Status Field -->
            <div>
                <label for="status" class="label">Status</label>
                <select id="status" name="status" class="select select-bordered w-full">
                    <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                </select>
                @error('status')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button id="simpan" type="submit" class="btn btn-primary">Update Post</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
</script>
@endpush
