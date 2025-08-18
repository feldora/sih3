<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Services\PostService;

class PostController extends Controller
{
    // Menampilkan semua post
    public function index(Request $request, PostService $service)
    {
        $posts = $service->getPosts([
            'search' => $request->input('search'),
            'category' => $request->input('category'),
            'status' => $request->input('status'),
            'tags'     => $request->input('tags'),
            'sort' => $request->input('sort', 'created_at'),
            'direction' => $request->input('direction', 'desc'),
            'per_page' => $request->input('per_page', 10),
            'selected_categories_type' => 'post',
        ]);
        $categories = Category::where('type', 'post')->get();
        return view('admin.posts.index', compact('posts', 'categories'));
    }

    // Menampilkan form untuk membuat post baru
    public function create()
    {
        $categories = Category::where('type', 'post')->get();
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
            // 'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Maksimal 2MB
        ]);

        $data = $request->only([
            'title', 'content', 'status', 'instansi_id', 'views', 'tags', 'category_id'
        ]);

        $data['featured_image'] = $request->file('featured_image');
        $data['fileUploads'] = $request->file('fileUploads'); // bisa array

        $service->store($data);

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
    }

    // Menampilkan form untuk mengedit post
    public function edit(\App\Models\Post $post)
    {
        $categories = Category::where('type', 'post')->get();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    // Mengupdate post yang ada
    public function update(Request $request, \App\Models\Post $post, PostService $service)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,name',
            'status' => 'required|in:draft,published',
            'tags' => 'array|exists:tags,id',
            // 'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Maksimal 2MB
        ]);

        $service->update($post, $request->all());

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    // Menghapus post
    public function destroy(\App\Models\Post $post, PostService $service)
    {
        $service->delete($post);
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }

    public function bulkAction(Request $request, PostService $service)
    {
        $result = $service->bulkAction($request->selected_posts, $request->bulk_action);
        return back()->with($result['status'], $result['message']);
    }



    public function publicIndex(PostService $service)
    {
        $posts = $service->getPublicPosts(request('category'));
        return view('pages.artikel.list', compact('posts'));
    }

    public function publicShow($slug, PostService $service)
    {
        [$post, $popularPosts] = $service->getPublicPostDetail($slug);
        return view('pages.artikel.show', compact('post', 'popularPosts'));
    }
}
