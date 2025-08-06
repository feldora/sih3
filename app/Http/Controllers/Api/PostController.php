<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PostService;

class PostController extends Controller
{
    // Mendapatkan daftar post (GET /api/posts)
    public function index(Request $request, PostService $service)
    {
        $posts = $service->getPosts([
            'search' => $request->input('search'),
            'category' => $request->input('category'),
            'tags'     => $request->input('tags'),
            'status' => $request->input('status'),
            'sort' => $request->input('sort', 'created_at'),
            'direction' => $request->input('direction', 'desc'),
            'per_page' => $request->input('per_page', 10),
        ]);
        return response()->json($posts);
    }

    // Mendapatkan detail post berdasarkan slug (GET /api/posts/{slug})
    public function show($slug, PostService $service)
    {
        [$post, $popularPosts] = $service->getPublicPostDetail($slug);
        return response()->json([
            'post' => $post,
            'popular_posts' => $popularPosts
        ]);
    }
}
