{{-- resources/views/admin/posts/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Manage Posts')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Manage Posts</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create New Post
            </a>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters and Search --}}
    <div class="bg-base-200 p-4 rounded-lg mb-6">
        <form method="GET" action="{{ route('admin.posts.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="form-control">
                <input type="text" 
                       name="search" 
                       placeholder="Search posts..." 
                       class="input input-bordered w-full" 
                       value="{{ request('search') }}">
            </div>

            {{-- Category Filter --}}
            <div class="form-control">
                <select name="category" class="select select-bordered w-full">
                    <option value="">All Categories</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" 
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="form-control">
                <select name="status" class="select select-bordered w-full">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            {{-- Filter Actions --}}
            <div class="form-control flex flex-row space-x-2">
                <button type="submit" class="btn btn-primary flex-1">Filter</button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-ghost">Reset</a>
            </div>
        </form>
    </div>

    {{-- Bulk Actions Form - Wrapper for entire table --}}
    <form id="bulk-action-form" method="POST" action="{{ route('admin.posts.bulk-action') }}">
        @csrf
        <div class="mb-4">
            <div class="flex items-center space-x-4">
                <input type="checkbox" id="select-all" class="checkbox">
                <label for="select-all" class="text-sm">Select All</label>
                
                <select name="bulk_action" class="select select-bordered select-sm">
                    <option value="">Bulk Actions</option>
                    <option value="publish">Publish Selected</option>
                    <option value="draft">Set as Draft</option>
                    <option value="delete">Delete Selected</option>
                </select>
                
                <button type="submit" class="btn btn-sm btn-secondary" id="bulk-submit" disabled>
                    Apply
                </button>
            </div>
        </div>

    {{-- Posts Count and Pagination Info --}}
    <div class="flex justify-between items-center mb-4">
        <div class="text-sm text-gray-600">
            @if($posts->count() > 0)
                Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} posts
            @else
                No posts found
            @endif
        </div>
        
        {{-- Per Page Selector --}}
        <div class="flex items-center space-x-2">
            <span class="text-sm">Show:</span>
            <select onchange="window.location.href=updateUrlParameter(window.location.href, 'per_page', this.value)" 
                    class="select select-bordered select-sm">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    </div>

    {{-- Posts Table --}}
    @if($posts->count() > 0)
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" class="checkbox" id="master-checkbox">
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                               class="flex items-center hover:text-primary">
                                Title
                                @if(request('sort') === 'title')
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        @if(request('direction') === 'asc')
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        @else
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>Category</th>
                        <th>Tags</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                               class="flex items-center hover:text-primary">
                                Status
                                @if(request('sort') === 'status')
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        @if(request('direction') === 'asc')
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        @else
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                               class="flex items-center hover:text-primary">
                                Created
                                @if(request('sort') === 'created_at')
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        @if(request('direction') === 'asc')
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        @else
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                    <tr class="hover">
                        <td>
                            <input type="checkbox" name="selected_posts[]" value="{{ $post->id }}" class="checkbox post-checkbox">
                        </td>
                        <td>
                            <div class="flex flex-col">
                                <span class="font-medium">{{ Str::limit($post->title, 50) }}</span>
                                <span class="text-sm text-gray-500">ID: {{ $post->id }}</span>
                            </div>
                        </td>
                        <td>
                            @if($post->category)
                                <span class="badge badge-outline">{{ $post->category->name }}</span>
                            @else
                                <span class="text-gray-400">No Category</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @forelse($post->tags as $tag)
                                    <span class="badge badge-info badge-sm">{{ $tag->name }}</span>
                                @empty
                                    <span class="text-gray-400 text-sm">No tags</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            @php
                                $statusClasses = [
                                    'published' => 'badge-success',
                                    'draft' => 'badge-warning',
                                    'archived' => 'badge-error'
                                ];
                                $statusClass = $statusClasses[$post->status] ?? 'badge-neutral';
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-sm text-gray-500">
                                {{ $post->created_at->format('M d, Y') }}
                            </span>
                        </td>
                        <td>
                            <div class="flex space-x-1">
                                {{-- View Button --}}
                                @if($post->status === 'published')
                                    <a href="{{ route('artikel.publicShow', $post->slug ?? $post->id) }}" 
                                       target="_blank"
                                       class="btn btn-xs btn-ghost" 
                                       title="View Post">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                @endif

                                {{-- Edit Button --}}
                                <a href="{{ route('admin.posts.edit', $post) }}" 
                                   class="btn btn-xs btn-info" 
                                   title="Edit Post">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>

                                {{-- Delete Button --}}
                                <button type="button" 
                                        class="btn btn-xs btn-error delete-single-btn" 
                                        title="Delete Post"
                                        data-post-id="{{ $post->id }}"
                                        data-post-title="{{ $post->title }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $posts->appends(request()->query())->links() }}
        </div>
    </form>
    @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No posts found</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'category', 'status']))
                    Try adjusting your search or filter criteria.
                @else
                    Get started by creating a new post.
                @endif
            </p>
            <div class="mt-6">
                @if(request()->hasAny(['search', 'category', 'status']))
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-primary">Clear Filters</a>
                @else
                    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">Create New Post</a>
                @endif
            </div>
        </div>
    @endif
</div>

@push('scripts')
{{-- Hidden form for single post deletion --}}
<form id="delete-single-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
// URL parameter helper function
function updateUrlParameter(url, param, paramVal) {
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (var i = 0; i < tempArray.length; i++) {
            if (tempArray[i].split('=')[0] != param) {
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }
    var rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}

// Bulk actions functionality
document.addEventListener('DOMContentLoaded', function() {
    const masterCheckbox = document.getElementById('master-checkbox');
    const postCheckboxes = document.querySelectorAll('.post-checkbox');
    const bulkSubmit = document.getElementById('bulk-submit');
    const bulkActionForm = document.getElementById('bulk-action-form');

    // Master checkbox functionality
    if (masterCheckbox) {
        masterCheckbox.addEventListener('change', function() {
            postCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkSubmitState();
        });
    }

    // Individual checkbox functionality
    postCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateMasterCheckboxState();
            updateBulkSubmitState();
        });
    });

    function updateMasterCheckboxState() {
        const checkedCount = document.querySelectorAll('.post-checkbox:checked').length;
        const totalCount = postCheckboxes.length;
        
        if (masterCheckbox) {
            masterCheckbox.checked = checkedCount === totalCount;
            masterCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        }
    }

    function updateBulkSubmitState() {
        const hasChecked = document.querySelectorAll('.post-checkbox:checked').length > 0;
        if (bulkSubmit) {
            bulkSubmit.disabled = !hasChecked;
        }
    }

    // Bulk action form submission
    if (bulkActionForm) {
        bulkActionForm.addEventListener('submit', function(e) {
            const selectedPosts = document.querySelectorAll('.post-checkbox:checked');
            const bulkAction = document.querySelector('select[name="bulk_action"]').value;
            
            if (selectedPosts.length === 0) {
                e.preventDefault();
                alert('Please select at least one post.');
                return false;
            }
            
            if (!bulkAction) {
                e.preventDefault();
                alert('Please select a bulk action.');
                return false;
            }
            
            if (bulkAction === 'delete') {
                if (!confirm(`Are you sure you want to delete ${selectedPosts.length} selected posts?`)) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    }

    // Single post deletion
    document.querySelectorAll('.delete-single-btn').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const postTitle = this.getAttribute('data-post-title');
            
            if (confirm(`Are you sure you want to delete "${postTitle}"?`)) {
                const deleteForm = document.getElementById('delete-single-form');
                deleteForm.action = `{{ route('admin.posts.index') }}/${postId}`;
                deleteForm.submit();
            }
        });
    });
});
</script>
@endpush
@endsection