<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class InfoH3Controller extends Controller
{
    public function index(){
        $categories = Category::where('type', 'post')->get();
        // pd($categories);
        return view('pages.info_h3', compact('categories'));
    }
}
