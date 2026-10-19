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
use App\Http\Controllers\Dashboard\OrphanFamilyController;
use App\Http\Controllers\Dashboard\ProgramController;
use App\Http\Controllers\Dashboard\RegionController;
use App\Http\Controllers\Dashboard\SiteSettingController;
use App\Http\Controllers\Dashboard\SiteTextController;
use App\Http\Controllers\Dashboard\SponsorController;
use App\Http\Controllers\Dashboard\SponsorRequestController;
use App\Http\Controllers\Dashboard\SponsorshipCaseController;
use App\Http\Controllers\Dashboard\SponsorshipDisbursementController;
use App\Http\Controllers\Dashboard\SponsorshipReportController;
use App\Http\Controllers\Dashboard\StoryController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationPaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageVariantController;
use App\Http\Controllers\OrphanCaseUpdateController;
use App\Http\Controllers\OrphanRegistrationController;
use App\Http\Controllers\PasswordSetupController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SponsorPortalController;
use App\Http\Controllers\SponsorshipPageController;
use App\Http\Middleware\WarmUploadedImages;
use App\Models\User;
use App\Support\ResponsiveImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// SEO: Sitemap & robots.txt (no caching — always fresh)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Resized WebP photos, written to public/variants on first request and served statically afterwards
Route::get('/variants/{width}/{path}', ImageVariantController::class)
    ->whereIn('width', array_map('strval', ResponsiveImage::WIDTHS))
    ->where('path', '.+\.('.implode('|', array_keys(ResponsiveImage::FORMATS)).')')
    ->name('image-variant');

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
    Route::get('/regions/{key}', [PublicPageController::class, 'regionOnHomeMap'])->name('regions.show');
    Route::get('/impact-map', [PublicPageController::class, 'impactMap'])->name('impact-map');
    Route::get('/impact', [PublicPageController::class, 'impact'])->name('impact');
    Route::get('/news', [PublicPageController::class, 'news'])->name('news');
    Route::get('/news/{key}', [PublicPageController::class, 'newsShow'])->name('news.show');
    Route::get('/facilities/{key}', [PublicPageController::class, 'facilityShow'])->name('facilities.show');
    Route::get('/sponsorship', [SponsorshipPageController::class, 'index'])->name('sponsorship');
    Route::get('/sponsorship/register', [OrphanRegistrationController::class, 'create'])->name('orphan-registration');
    Route::get('/sponsorship/update', [OrphanCaseUpdateController::class, 'create'])->name('orphan-case-update');
    Route::get('/sponsorship/update/case', [OrphanCaseUpdateController::class, 'edit'])->name('orphan-case-update.edit');
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

Route::post('/sponsorship/register', [OrphanRegistrationController::class, 'store'])
    ->middleware('throttle:orphan-registration')
    ->name('orphan-registration.store');

/*
 * Case self-update POSTs sit outside the public GET cache group. Lookup opens a short session;
 * throttles mirror registration so busy shared networks get a friendly message, not a bare 429.
 */
Route::post('/sponsorship/update/lookup', [OrphanCaseUpdateController::class, 'lookup'])
    ->middleware('throttle:orphan-case-update')
    ->name('orphan-case-update.lookup');
Route::post('/sponsorship/update/case', [OrphanCaseUpdateController::class, 'update'])
    ->middleware('throttle:orphan-case-update')
    ->name('orphan-case-update.update');
Route::post('/sponsorship/update/exit', [OrphanCaseUpdateController::class, 'exit'])->name('orphan-case-update.exit');

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
    Route::get('/reset-password/{token}', [PasswordSetupController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [PasswordSetupController::class, 'store'])->middleware('throttle:5,1')->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'role:'.User::ROLE_SPONSOR])->group(function () {
    Route::get('/my-sponsorships', [SponsorPortalController::class, 'index'])->name('sponsor.home');
    Route::get('/my-sponsorships/{sponsorship}', [SponsorPortalController::class, 'show'])->name('sponsor.show');
    Route::post('/my-sponsorships/{sponsorship}/requests', [SponsorPortalController::class, 'storeRequest'])->name('sponsor.requests.store');
    Route::get('/my-sponsorships/{sponsorship}/print', [SponsorPortalController::class, 'print'])->name('sponsor.print');
    Route::get('/my-sponsorships/{sponsorship}/finance', [SponsorPortalController::class, 'finance'])->name('sponsor.finance');
    Route::get('/my-sponsorships/{sponsorship}/finance/print', [SponsorPortalController::class, 'financePrint'])->name('sponsor.finance.print');
});

Route::middleware(['auth', 'role:'.implode(',', User::STAFF_ROLES), WarmUploadedImages::class])->group(function () {
    Route::middleware('role:'.implode(',', User::DASHBOARD_MANAGE_ROLES))->group(function () {
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
    });

    Route::middleware('role:'.implode(',', User::CONTENT_ROLES))->group(function () {
        Route::get('dashboard/orphans/import', [OrphanFamilyController::class, 'importForm'])->name('dashboard.orphans.import');
        Route::get('dashboard/orphans/import/template', [OrphanFamilyController::class, 'importTemplate'])->name('dashboard.orphans.import.template');
        Route::post('dashboard/orphans/import', [OrphanFamilyController::class, 'import'])->name('dashboard.orphans.import.store');
        Route::get('dashboard/orphans/create', [OrphanFamilyController::class, 'create'])->name('dashboard.orphans.create');
        Route::post('dashboard/orphans', [OrphanFamilyController::class, 'store'])->name('dashboard.orphans.store');
    });

    Route::middleware('role:'.implode(',', User::ORPHAN_VIEW_ROLES))->group(function () {
        Route::get('dashboard/orphans', [OrphanFamilyController::class, 'index'])->name('dashboard.orphans.index');
        Route::get('dashboard/orphans/{orphanFamily}', [OrphanFamilyController::class, 'show'])->name('dashboard.orphans.show');
        Route::get('dashboard/orphans/{orphanFamily}/print', [SponsorshipReportController::class, 'casePrint'])->name('dashboard.orphans.print');
        Route::get('dashboard/orphans/{orphanFamily}/finance', [SponsorshipReportController::class, 'caseFinance'])->name('dashboard.orphans.finance');
        Route::get('dashboard/orphans/{orphanFamily}/finance/print', [SponsorshipReportController::class, 'caseFinancePrint'])->name('dashboard.orphans.finance.print');
        Route::get('dashboard/orphans/{orphanFamily}/documents/{document}', [OrphanFamilyController::class, 'document'])->name('dashboard.orphans.document');
    });

    Route::middleware('role:'.implode(',', User::CONTENT_ROLES))->group(function () {
        Route::get('dashboard/orphans/{orphanFamily}/edit', [OrphanFamilyController::class, 'edit'])->name('dashboard.orphans.edit');
        Route::put('dashboard/orphans/{orphanFamily}', [OrphanFamilyController::class, 'update'])->name('dashboard.orphans.update');
        Route::delete('dashboard/orphans/{orphanFamily}', [OrphanFamilyController::class, 'destroy'])->name('dashboard.orphans.destroy');
        Route::patch('dashboard/orphans/{orphanFamily}/review', [OrphanFamilyController::class, 'review'])->name('dashboard.orphans.review');
        Route::patch('dashboard/orphans/{orphanFamily}/sponsorship', [OrphanFamilyController::class, 'updateSponsorship'])
            ->middleware('role:'.implode(',', User::FINANCE_ROLES))
            ->name('dashboard.orphans.sponsorship');
    });

    Route::resource('dashboard/users', UserController::class)
        ->names('dashboard.users')
        ->only(['index', 'create', 'store', 'destroy'])
        ->middleware('role:'.User::ROLE_SUPER_ADMIN);

    Route::middleware('role:'.implode(',', User::FINANCE_REPORT_ROLES))->group(function () {
        Route::get('dashboard/reports/finance', [SponsorshipReportController::class, 'period'])->name('dashboard.reports.finance');
        Route::get('dashboard/reports/finance/print', [SponsorshipReportController::class, 'periodPrint'])->name('dashboard.reports.finance.print');
    });

    Route::middleware('role:'.implode(',', User::FINANCE_ROLES))->group(function () {
        Route::resource('dashboard/sponsors', SponsorController::class)->names('dashboard.sponsors');
        Route::post('dashboard/sponsors/{sponsor}/invite', [SponsorController::class, 'invite'])->name('dashboard.sponsors.invite');
        Route::get('dashboard/sponsor-requests', [SponsorRequestController::class, 'index'])->name('dashboard.sponsor-requests.index');
        Route::patch('dashboard/sponsor-requests/{sponsorRequest}', [SponsorRequestController::class, 'update'])->name('dashboard.sponsor-requests.update');
        Route::get('dashboard/disbursements', [SponsorshipDisbursementController::class, 'index'])->name('dashboard.disbursements.index');
        Route::get('dashboard/disbursements/create', [SponsorshipDisbursementController::class, 'create'])->name('dashboard.disbursements.create');
        Route::post('dashboard/disbursements', [SponsorshipDisbursementController::class, 'store'])->name('dashboard.disbursements.store');
        Route::delete('dashboard/disbursements/{disbursement}', [SponsorshipDisbursementController::class, 'destroy'])->name('dashboard.disbursements.destroy');
    });
});
