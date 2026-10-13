<?php

namespace App\Providers;

use App\Support\OverridableTranslationLoader;
use App\Support\PublicNavigation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->extend('translation.loader', fn (Loader $loader): Loader => new OverridableTranslationLoader($loader));
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

        /*
         * Families in Gaza often reach the site through one shared carrier IP, so the registration form
         * gets a wider per-IP budget than the other public forms, and a friendly message instead of a 429 page.
         */
        RateLimiter::for('orphan-registration', fn (Request $request): Limit => Limit::perMinute(30)
            ->by($request->ip())
            ->response(fn (): RedirectResponse => redirect()->route('orphan-registration')
                ->withInput()
                ->withErrors(['throttle' => __('orphan_registration.too_many')])));
    }
}
