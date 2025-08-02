<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use App\Models\PostModel as Buletin;
use Illuminate\Http\Request;
use App\Services\PostService;

class BuletinController extends Controller
{
    // Menampilkan semua post
    public function index(Request $request) 
    {
        // Konfigurasi halaman - bisa disesuaikan per controller
        $pageConfig = [
            'title' => 'Buletin Iklim Bulanan',
            'search_placeholder' => 'Cari buletin...',
            'add_button_text' => 'Tambah Buletin',
            'add_route' => route('admin.buletin-iklim-bulanan.create'), // sesuaikan route
            'detail_route_name' => 'admin.buletin-iklim-bulanan.show', // untuk generate route di card
            'per_page' => 10,
            'no_data_message' => 'Belum ada data buletin'
        ];

        if ($request->ajax()) {
            $search = $request->get('search');
            $page = $request->get('page', 1);
            
            // Query data dengan search
            $query = Buletin::with('media')
                            ->with('category')
                            ->select('id', 'title as judul', 'content as deskripsi', 'created_at')
                            ->where('category.name', 'Buletin Iklim Bulanan');
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }
            
            $data = $query->paginate($pageConfig['per_page']);
            
            // Format data untuk frontend
            $formattedData = $data->map(function($item) use ($pageConfig) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'deskripsi' => Str::limit($item->deskripsi, 100),
                    'tanggal' => $item->created_at->format('d M Y'),
                    'gambar' => $item->media ? $item->media->getUrl() : 'https://picsum.photos/800/360?random=' . $item->id,
                    'detail_url' => route($pageConfig['detail_route_name'], $item->id)
                ];
            });
            
            return response()->json([
                'data' => $formattedData,
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'has_next' => $data->hasMorePages(),
                    'has_prev' => $data->currentPage() > 1,
                    'next_page_url' => $data->nextPageUrl(),
                    'prev_page_url' => $data->previousPageUrl()
                ]
            ]);
        }

        return view('admin.pages.buletin-iklim-bulanan.index', compact('pageConfig'));
    }

    // Menampilkan form untuk membuat post baru
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.buletin.create', compact('categories', 'tags'));
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

        $service->store($request->all());

        return redirect()->route('admin.buletin.index')->with('success', 'Post created successfully.');
    }

    // Menampilkan form untuk mengedit post
    public function edit(\App\Models\Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.buletin.edit', compact('post', 'categories', 'tags'));
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

        return redirect()->route('admin.buletin.index')->with('success', 'Post updated successfully.');
    }

    // Menghapus post
    public function destroy(\App\Models\Post $post, PostService $service)
    {
        $service->delete($post);
        return redirect()->route('admin.buletin.index')->with('success', 'Post deleted successfully.');
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
