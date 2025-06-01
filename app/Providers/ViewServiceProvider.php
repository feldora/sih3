<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\PostAsideComposer;
use Illuminate\Support\Facades\View;


class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        view()->composer('partials.post-asside', \App\Http\View\Composers\PostAsideComposer::class);
    }
}