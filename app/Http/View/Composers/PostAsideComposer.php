<?php
namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;


class PostAsideComposer
{
    public function compose(View $view)
    {
        // Ambil 5 artikel populer (atau terbaru)
        // $view->with([
        //     'recentPosts' => Post::where('status', 'published')
        //         ->with('user', 'category')
        //         ->latest()
        //         ->take(5)
        //         ->get(),
        // ]);

        $view->with([
            'categories' => \App\Models\Category::all(),
            'popularPosts' => Post::where('status', 'published')
                ->with('user', 'category')
                ->orderBy('views', 'desc')
                ->latest()
                ->take(5)
                ->get()
        ]);
    }
}