<!-- resources/views/admin/posts/index.blade.php -->

@extends('layouts.admin')

@section('title', 'Manage Posts')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-4">Manage Posts</h1>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">Create New Post</a>
    </div>

    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Tags</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $post)
                <tr>
                    <td>{{ $post->title }}</td>
                    <td>{{ $post->category->name }}</td>
                    <td>
                        @foreach($post->tags as $tag)
                            <span class="badge badge-info">{{ $tag->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <span class="badge {{ $post->status === 'published' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($post->status) }}
                        </span>
                    </td>
                    <td class="space-x-2">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-info">Edit</a>
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-error">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $posts->links() }}
    </div>
</div>
@endsection
