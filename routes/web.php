<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/programs', [PublicPageController::class, 'programs'])->name('programs');
Route::get('/campaigns', [PublicPageController::class, 'campaigns'])->name('campaigns');
Route::get('/impact', [PublicPageController::class, 'impact'])->name('impact');
Route::get('/news', [PublicPageController::class, 'news'])->name('news');
Route::get('/sponsorship', [PublicPageController::class, 'sponsorship'])->name('sponsorship');
Route::get('/donate', [PublicPageController::class, 'donate'])->name('donate');
Route::get('/partners', [PublicPageController::class, 'partners'])->name('partners');
Route::get('/volunteer', [PublicPageController::class, 'volunteer'])->name('volunteer');
Route::get('/faq', [PublicPageController::class, 'faq'])->name('faq');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, config('bader.locales'), true), 404);

    session(['locale' => $locale]);

    return back(fallback: route('home'));
})->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
