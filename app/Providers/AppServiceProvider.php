<?php

namespace App\Providers;

use App\Support\PublicNavigation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['partials.site-header', 'partials.site-footer'], function ($view): void {
            $view->with('navMenus', PublicNavigation::menus());
        });

        foreach (PublicNavigation::MENU_SOURCES as $model) {
            $model::saved(fn () => PublicNavigation::flushMenus());
            $model::deleted(fn () => PublicNavigation::flushMenus());
        }
    }
}
