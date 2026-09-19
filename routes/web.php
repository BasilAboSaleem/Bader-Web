<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::view('/about', 'pages.stub', ['titleKey' => 'nav.about'])->name('about');
Route::view('/programs', 'pages.stub', ['titleKey' => 'nav.programs'])->name('programs');
Route::view('/campaigns', 'pages.stub', ['titleKey' => 'nav.campaigns'])->name('campaigns');
Route::view('/impact', 'pages.stub', ['titleKey' => 'nav.impact'])->name('impact');
Route::view('/news', 'pages.stub', ['titleKey' => 'nav.news'])->name('news');
Route::view('/sponsorship', 'pages.stub', ['titleKey' => 'nav.sponsorship'])->name('sponsorship');
Route::view('/donate', 'pages.stub', ['titleKey' => 'nav.donate'])->name('donate');
Route::view('/partners', 'pages.stub', ['titleKey' => 'nav.partners'])->name('partners');
Route::view('/volunteer', 'pages.stub', ['titleKey' => 'nav.volunteer'])->name('volunteer');
Route::view('/faq', 'pages.stub', ['titleKey' => 'nav.faq'])->name('faq');
Route::view('/contact', 'pages.stub', ['titleKey' => 'nav.contact'])->name('contact');

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, config('bader.locales'), true), 404);

    session(['locale' => $locale]);

    return back(fallback: route('home'));
})->name('locale.switch');
