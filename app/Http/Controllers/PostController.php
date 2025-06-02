<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\PostService;

class PostController extends Controller
{
    // Menampilkan semua post
    public function index(Request $request)
    {

    $query = Post::with(['user', 'category', 'tags']);

    // Search
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }
    
    // Filter by category
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }
    
    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    // Sorting
    $sortField = $request->get('sort', 'created_at');
    $sortDirection = $request->get('direction', 'desc');
    $query->orderBy($sortField, $sortDirection);
    
    // Pagination
    $perPage = $request->get('per_page', 10);
    $posts = $query->paginate($perPage);
    
    $categories = Category::all(); // For filter dropdown
    

        // $posts = Post::with('user', 'category', 'tags')->latest()->paginate(10);
        return view('admin.posts.index', compact('posts','categories'));
    }

    // Menampilkan form untuk membuat post baru
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    // Menyimpan post baru
    public function store(Request $request, PostService $service)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,name',
            'status' => 'required|in:draft,published',
            'tags' => 'array|exists:tags,id',
        ]);

        $service->store($request->all());

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
    }

    // Menampilkan form untuk mengedit post
    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    // Mengupdate post yang ada
    public function update(Request $request, Post $post, PostService $service)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,name',
            'status' => 'required|in:draft,published',
            'tags' => 'array|exists:tags,id',
        ]);

        $service->update($post, $request->all());

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    // Menghapus post
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $selectedPosts = $request->selected_posts;
        $action = $request->bulk_action;
        
        if (empty($selectedPosts) || empty($action)) {
            return back()->with('error', 'Please select posts and action.');
        }
        
        switch ($action) {
            case 'publish':
                Post::whereIn('id', $selectedPosts)->update(['status' => 'published']);
                return back()->with('success', 'Posts published successfully.');
                
            case 'draft':
                Post::whereIn('id', $selectedPosts)->update(['status' => 'draft']);
                return back()->with('success', 'Posts set as draft successfully.');
                
            case 'delete':
                Post::whereIn('id', $selectedPosts)->delete();
                return back()->with('success', 'Posts deleted successfully.');
        }
    }



    public function publicIndex()
    {
        $query = Post::where('status', 'published')->with('user', 'category', 'tags');

        if ($category = request('category')) {
            $query->whereHas('category', function ($q) use ($category) {
            $q->where('name', $category);
            });
        }

        $posts = $query->latest()->paginate(10);
        return view('pages.artikel.list', compact('posts'));
    }
    
    public function publicShow($slug)
    {
        $post = Post::where('slug', $slug)->where('status', 'published')->with('user', 'category', 'tags')->firstOrFail();
        $popularPosts = Post::where('status', 'published')->with('user', 'category', 'tags')->latest()->take(5)->get();
        $post->increment('views');
        return view('pages.artikel.show', compact('post', 'popularPosts'));
    }
}
