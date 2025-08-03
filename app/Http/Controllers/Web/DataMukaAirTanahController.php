<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Models\Post as dataMukaAirTanah;

class DataMukaAirTanahController extends Controller
{
    private $baseLabelUrl = 'admin.geologi.dmat';
    private $actionUrls = [];
    private $viewPath = 'admin.pages.dt_general.';
    private $generalTitle = 'Data Muka Air Tanah';
    private $filterCategories = 'data muka air tanah';

    public function __construct()
    {
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
        $posts = $service->getPosts([
            'search' => $request->input('search'),
            'category' => $request->input('category'),
            'status' => $request->input('status'),
            'sort' => $request->input('sort', 'created_at'),
            'direction' => $request->input('direction', 'desc'),
            'per_page' => $request->input('per_page', 10),
        ]);

        $categories = Category::where('type', 'data')->where('name', $this->filterCategories )->first();
        $actionUrls = $this->actionUrls;
        $title = $this->generalTitle;
        return view($this->viewPath.'index', compact('title', 'posts', 'categories', 'actionUrls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('type', 'data')->where('name', $this->filterCategories )->first();
        $tags = [];
        $actionPost = route($this->baseLabelUrl.'.store');
        
        $title = $this->generalTitle;
        return view($this->viewPath.'create', compact('title', 'categories', 'tags', 'actionPost'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PostService $service)
    {
        // echo "Data muka air tanah berhasil disimpan.";
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
    public function show(string $slug, PostService $service)
    {
        [$post] = $service->getPublicPostDetail($slug);
        
        $title = $this->generalTitle . ' - ' . $post->title;
        return view($this->viewPath.'show', compact('title', 'post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(dataMukaAirTanah $dataMukaAirTanah)
    {
        
        $actionPost = route($this->baseLabelUrl.'.update', $dataMukaAirTanah->id);
        
        $title = $this->generalTitle;
        $post = $dataMukaAirTanah->load(['category', 'tags', 'user', 'media']);
        
        return view($this->viewPath.'edit', compact('title', 'post', 'actionPost'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(dataMukaAirTanah $dataMukaAirTanah, Request $request, PostService $service)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $post = $dataMukaAirTanah->load(['category', 'tags', 'user', 'media']);

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
    public function destroy(dataMukaAirTanah $dataMukaAirTanah, PostService $service)
    {
        $service->delete($dataMukaAirTanah);
        // return redirect()->route($this->baseLabelUrl.'.index')->with('success', 'Post deleted successfully.');
    }
}
