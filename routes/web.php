<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\CampaignController;
use App\Http\Controllers\Dashboard\DonationController;
use App\Http\Controllers\Dashboard\FacilityController;
use App\Http\Controllers\Dashboard\ImpactMetricController;
use App\Http\Controllers\Dashboard\InboxController;
use App\Http\Controllers\Dashboard\InstitutionalPageController;
use App\Http\Controllers\Dashboard\MediaAssetController;
use App\Http\Controllers\Dashboard\ProgramController;
use App\Http\Controllers\Dashboard\SiteSettingController;
use App\Http\Controllers\Dashboard\StoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// SEO: Sitemap & robots.txt (no caching — always fresh)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Public pages with HTTP caching for guests
Route::middleware('cache.public')->group(function () {
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
});

// Public Form Submissions
Route::post('/contact', [PublicFormController::class, 'submitContact'])->name('contact.submit');
Route::post('/partners', [PublicFormController::class, 'submitPartnership'])->name('partners.submit');
Route::post('/volunteer', [PublicFormController::class, 'submitVolunteer'])->name('volunteer.submit');
Route::post('/sponsorship', [PublicFormController::class, 'submitSponsorship'])->name('sponsorship.submit');
Route::post('/assistance', [PublicFormController::class, 'submitAssistance'])->name('assistance.submit');
Route::post('/donate/transfer', [PublicFormController::class, 'notifyTransfer'])->name('donate.transfer.submit');

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
    Route::get('/dashboard/settings', [SiteSettingController::class, 'edit'])->name('dashboard.settings.edit');
    Route::put('/dashboard/settings', [SiteSettingController::class, 'update'])->name('dashboard.settings.update');
    Route::get('/dashboard/pages', [InstitutionalPageController::class, 'edit'])->name('dashboard.pages.edit');
    Route::put('/dashboard/pages', [InstitutionalPageController::class, 'update'])->name('dashboard.pages.update');

    Route::resource('dashboard/programs', ProgramController::class)->names('dashboard.programs')->except(['show']);
    Route::resource('dashboard/facilities', FacilityController::class)->names('dashboard.facilities')->except(['show']);
    Route::resource('dashboard/campaigns', CampaignController::class)->names('dashboard.campaigns')->except(['show']);
    Route::resource('dashboard/stories', StoryController::class)->names('dashboard.stories')->except(['show']);
    Route::resource('dashboard/media', MediaAssetController::class)->names('dashboard.media')->except(['show']);

    Route::resource('dashboard/inbox', InboxController::class)->names('dashboard.inbox')->only(['index', 'show', 'update', 'destroy']);
    Route::resource('dashboard/impact', ImpactMetricController::class)->names('dashboard.impact')->except(['show']);
    Route::patch('dashboard/impact/{impact}/toggle', [ImpactMetricController::class, 'toggleApproval'])->name('dashboard.impact.toggle');
    Route::resource('dashboard/donations', DonationController::class)->names('dashboard.donations')->except(['show']);
    Route::patch('dashboard/donations/{donation}/verify', [DonationController::class, 'verify'])->name('dashboard.donations.verify');

    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
