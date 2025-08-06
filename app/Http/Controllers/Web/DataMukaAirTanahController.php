<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Models\Post;  // Gunakan model Post, bukan dataMukaAirTanah

class DataMukaAirTanahController extends Controller
{
    private $baseLabelUrl;
    private $actionUrls = [];
    private $viewPath;
    private $generalTitle;
    private $filterCategories;
    private $filterTag;

    public function __construct(array $config = [])
    {

        $this->baseLabelUrl = $config['baseLabelUrl'] ?? 'default';
        $this->viewPath = $config['viewPath'] ?? '';
        $this->generalTitle = $config['generalTitle'] ?? '';
        $this->filterCategories = $config['filterCategories'] ?? '';
        $this->filterTag = $config['filterTag'] ?? '';
        
        $this->actionUrls = [
            'index' => route($this->baseLabelUrl.'.index'),
            'create' => route($this->baseLabelUrl.'.create'),
            'store' => route($this->baseLabelUrl.'.store'),
            'show' => route($this->baseLabelUrl.'.show', ':slug'),
            'edit' => route($this->baseLabelUrl.'.edit', ':slug'),
            'update' => route($this->baseLabelUrl.'.update', ':id'),
            'destroy' => route($this->baseLabelUrl.'.destroy', ':id'),
        ];
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, PostService $service)
    {
        
        $existingCategory = Category::where('type', 'data')
            ->where('name', $this->filterCategories)
            ->first();
        if ($existingCategory) {
            $category = $existingCategory;
        } else {
            $category = new Category();
            $category->type = 'data';
            $category->name = $this->filterCategories;
            $category->slug = str_replace(' ', '-', $this->filterCategories);
            $category->save();
        }

        $existingTag = Tag::where('name', $this->filterTag)->first();
        if($existingTag) {
           $tag = $existingTag; 
        } else {
            $tag = new Tag();
            $tag->name = $this->filterTag;
            $tag->save();
        }

        $actionUrls = $this->actionUrls;
        $title = $this->generalTitle;
        $categories = $category;

        return view($this->viewPath.'index', compact('title', 'categories', 'tag', 'actionUrls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('type', 'data')->where('name', $this->filterCategories )->first();
        $tag = Tag::where('name', $this->filterTag)->first();
        $tags = [$tag ];
        $actionPost = route($this->baseLabelUrl.'.store');
        
        $title = $this->generalTitle;
        return view($this->viewPath.'create', compact('title', 'categories', 'tags', 'actionPost'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PostService $service)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $service->store($request->all());
        return redirect()->route($this->baseLabelUrl.'.index')->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug, PostService $service)  // Menggunakan Post sebagai parameter
    {
        $post = $service->getPostBySlug($slug); 
        $post->load(['category', 'tags', 'user', 'media']); 
        $title = $this->generalTitle . ' - ' . $post->title;
        return view($this->viewPath.'show', compact('title', 'post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $actionPost = route($this->baseLabelUrl.'.update', $post->id);
        
        $title = $this->generalTitle;
        $post = $post->load(['category', 'tags', 'user', 'media']);
        
        return view($this->viewPath.'edit', compact('title', 'post', 'actionPost'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Post $post, Request $request, PostService $service)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = $post->load(['category', 'tags', 'user', 'media']);

        try {
            $service->update($post, $request->all());
            return redirect()->route($this->baseLabelUrl.'.index')->with('success', 'Data updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, PostService $service)
    {
        $service->delete($post);
        return redirect()->route($this->baseLabelUrl.'.index')->with('success', 'Post deleted successfully.');
    }
}
