<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\PosPantauRepositoryInterface;
use App\Repositories\PosPantauRepository;
use App\Repositories\Contracts\WilayahSungaiRepositoryInterface;
use App\Repositories\WilayahSungaiRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PosPantauRepositoryInterface::class, PosPantauRepository::class);
        $this->app->bind(WilayahSungaiRepositoryInterface::class, WilayahSungaiRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        view()->composer('components.admin.sidebar', \App\Http\View\Composers\SidebarComposer::class);
        view()->composer('partials.header', \App\Http\View\Composers\SidebarComposer::class);
        view()->composer('partials.post-asside', \App\Http\View\Composers\PostAsideComposer::class);
        view()->composer('pages.home', \App\Http\View\Composers\WilayahSungaiComposer::class);
    }
}
