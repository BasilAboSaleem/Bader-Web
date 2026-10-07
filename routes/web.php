<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\CampaignController;
use App\Http\Controllers\Dashboard\CompletedProjectController;
use App\Http\Controllers\Dashboard\DonationController;
use App\Http\Controllers\Dashboard\FacilityController;
use App\Http\Controllers\Dashboard\FaqController;
use App\Http\Controllers\Dashboard\HomepageController;
use App\Http\Controllers\Dashboard\ImpactMetricController;
use App\Http\Controllers\Dashboard\InboxController;
use App\Http\Controllers\Dashboard\InstitutionalPageController;
use App\Http\Controllers\Dashboard\MediaAssetController;
use App\Http\Controllers\Dashboard\ProgramController;
use App\Http\Controllers\Dashboard\RegionController;
use App\Http\Controllers\Dashboard\SiteSettingController;
use App\Http\Controllers\Dashboard\SiteTextController;
use App\Http\Controllers\Dashboard\SponsorshipCaseController;
use App\Http\Controllers\Dashboard\StoryController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationPaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SponsorshipPageController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// SEO: Sitemap & robots.txt (no caching — always fresh)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Public pages with HTTP caching for guests
Route::middleware('cache.public')->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::get('/about', [PublicPageController::class, 'about'])->name('about');
    Route::get('/programs', [PublicPageController::class, 'programs'])->name('programs');
    Route::get('/programs/{key}', [PublicPageController::class, 'programShow'])->name('programs.show');
    Route::get('/campaigns', [PublicPageController::class, 'campaigns'])->name('campaigns');
    Route::get('/campaigns/{key}', [PublicPageController::class, 'campaignShow'])->name('campaigns.show');
    Route::get('/projects', [PublicPageController::class, 'completedProjects'])->name('projects');
    Route::get('/projects/{key}', [PublicPageController::class, 'completedProjectShow'])->name('projects.show');
    Route::get('/regions/{key}', [PublicPageController::class, 'regionShow'])->name('regions.show');
    Route::get('/impact-map', [PublicPageController::class, 'impactMap'])->name('impact-map');
    Route::get('/impact', [PublicPageController::class, 'impact'])->name('impact');
    Route::get('/news', [PublicPageController::class, 'news'])->name('news');
    Route::get('/news/{key}', [PublicPageController::class, 'newsShow'])->name('news.show');
    Route::get('/facilities/{key}', [PublicPageController::class, 'facilityShow'])->name('facilities.show');
    Route::get('/sponsorship', [SponsorshipPageController::class, 'index'])->name('sponsorship');
    Route::get('/sponsorship/{code}', [SponsorshipPageController::class, 'show'])->name('sponsorship.show');
    Route::get('/gift', [PublicPageController::class, 'gift'])->name('gift');
    Route::get('/zakat-calculator', [PublicPageController::class, 'zakat'])->name('zakat');
    Route::get('/partners', [PublicPageController::class, 'partners'])->name('partners');
    Route::get('/volunteer', [PublicPageController::class, 'volunteer'])->name('volunteer');
    Route::get('/faq', [PublicPageController::class, 'faq'])->name('faq');
    Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');
});

// Old completed-projects links (shared before projects were unified) keep working
Route::get('/completed-projects', fn (Request $request) => redirect()->route('projects', $request->query(), 301));
Route::permanentRedirect('/completed-projects/{key}', '/projects/{key}');

// Public Form Submissions
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/contact', [PublicFormController::class, 'submitContact'])->name('contact.submit');
    Route::post('/partners', [PublicFormController::class, 'submitPartnership'])->name('partners.submit');
    Route::post('/volunteer', [PublicFormController::class, 'submitVolunteer'])->name('volunteer.submit');
    Route::post('/sponsorship', [PublicFormController::class, 'submitSponsorship'])->name('sponsorship.submit');
    Route::post('/assistance', [PublicFormController::class, 'submitAssistance'])->name('assistance.submit');
    Route::post('/donate/transfer', [PublicFormController::class, 'notifyTransfer'])->name('donate.transfer.submit');
});

// Online donations (the donate form carries a CSRF token, so it is never publicly cached)
Route::get('/donate', [PublicPageController::class, 'donate'])->name('donate');
Route::post('/donate/checkout', [DonationPaymentController::class, 'checkout'])->middleware('throttle:20,1')->name('donate.checkout');
Route::get('/donate/success', [DonationPaymentController::class, 'success'])->name('donate.success');
Route::get('/donate/cancel', [DonationPaymentController::class, 'cancel'])->name('donate.cancel');
Route::post('/donate/webhook', [DonationPaymentController::class, 'webhook'])->name('donate.webhook');

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, config('bader.locales'), true), 404);

    session(['locale' => $locale]);

    return back(fallback: route('home'));
})->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/dashboard/settings', [SiteSettingController::class, 'edit'])->name('dashboard.settings.edit');
    Route::put('/dashboard/settings', [SiteSettingController::class, 'update'])->name('dashboard.settings.update');
    Route::get('/dashboard/homepage', [HomepageController::class, 'edit'])->name('dashboard.homepage.edit');
    Route::put('/dashboard/homepage', [HomepageController::class, 'update'])->name('dashboard.homepage.update');
    Route::get('/dashboard/pages', [InstitutionalPageController::class, 'edit'])->name('dashboard.pages.edit');
    Route::put('/dashboard/pages', [InstitutionalPageController::class, 'update'])->name('dashboard.pages.update');
    Route::get('/dashboard/site-texts', [SiteTextController::class, 'edit'])->name('dashboard.site-texts.edit');
    Route::put('/dashboard/site-texts', [SiteTextController::class, 'update'])->name('dashboard.site-texts.update');
    Route::resource('dashboard/faqs', FaqController::class)->names('dashboard.faqs')->except(['show']);

    Route::resource('dashboard/programs', ProgramController::class)->names('dashboard.programs')->except(['show']);
    Route::resource('dashboard/facilities', FacilityController::class)->names('dashboard.facilities')->except(['show']);
    Route::resource('dashboard/regions', RegionController::class)->names('dashboard.regions')->except(['show']);
    Route::resource('dashboard/campaigns', CampaignController::class)->names('dashboard.campaigns')->except(['show']);
    Route::resource('dashboard/completed-projects', CompletedProjectController::class)
        ->names('dashboard.completed-projects')
        ->parameters(['completed-projects' => 'completedProject'])
        ->except(['show']);
    Route::resource('dashboard/sponsorship-cases', SponsorshipCaseController::class)->names('dashboard.sponsorship-cases')->except(['show']);
    Route::resource('dashboard/stories', StoryController::class)->names('dashboard.stories')->except(['show']);
    Route::resource('dashboard/media', MediaAssetController::class)->names('dashboard.media')->except(['show']);

    Route::resource('dashboard/inbox', InboxController::class)->names('dashboard.inbox')->only(['index', 'show', 'update', 'destroy']);
    Route::resource('dashboard/impact', ImpactMetricController::class)->names('dashboard.impact')->except(['show']);
    Route::patch('dashboard/impact/{impact}/toggle', [ImpactMetricController::class, 'toggleApproval'])->name('dashboard.impact.toggle');
    Route::resource('dashboard/donations', DonationController::class)->names('dashboard.donations')->except(['show']);
    Route::patch('dashboard/donations/{donation}/verify', [DonationController::class, 'verify'])->name('dashboard.donations.verify');

    Route::resource('dashboard/users', UserController::class)
        ->names('dashboard.users')
        ->only(['index', 'create', 'store', 'destroy'])
        ->middleware('role:'.User::ROLE_SUPER_ADMIN);

    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
