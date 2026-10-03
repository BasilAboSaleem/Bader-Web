<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Faq;
use App\Models\Setting;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardSiteContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_fixed_questions_are_carried_over_into_the_faq_table(): void
    {
        $this->assertSame(5, Faq::count());

        $this->get(route('faq'))
            ->assertOk()
            ->assertSee(__('faq.identity.question'), false);
    }

    public function test_admin_can_add_a_question_that_appears_on_the_public_faq_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('dashboard.faqs.store'), [
                'question_ar' => 'هل يمكنني التبرع باسم شخص آخر؟',
                'answer_ar' => 'نعم، عبر خيار الإهداء في صفحة التبرع.',
                'order' => 1,
                'status' => 'published',
            ])
            ->assertRedirect(route('dashboard.faqs.index'));

        $this->get(route('faq'))
            ->assertOk()
            ->assertSee('هل يمكنني التبرع باسم شخص آخر؟', false)
            ->assertSee('نعم، عبر خيار الإهداء في صفحة التبرع.', false);
    }

    public function test_draft_questions_are_hidden_from_the_public_faq_page(): void
    {
        $draft = Faq::factory()->draft()->create();

        $this->get(route('faq'))
            ->assertOk()
            ->assertDontSee($draft->question_ar, false);
    }

    public function test_admin_can_delete_a_question(): void
    {
        $faq = Faq::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('dashboard.faqs.destroy', $faq))
            ->assertRedirect(route('dashboard.faqs.index'));

        $this->assertModelMissing($faq);
    }

    public function test_guest_cannot_manage_questions_or_site_texts(): void
    {
        $this->get(route('dashboard.faqs.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard.site-texts.edit'))->assertRedirect(route('login'));
        $this->put(route('dashboard.site-texts.update'), ['texts' => ['ar' => ['footer.action_title' => 'x']]])
            ->assertRedirect(route('login'));
    }

    public function test_overridden_site_text_replaces_the_translation_on_the_public_site(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard.site-texts.edit', ['group' => 'layout']))
            ->assertOk()
            ->assertSee('texts[ar][footer.action_title]', false);

        $this->put(route('dashboard.site-texts.update'), [
            'group' => 'layout',
            'texts' => ['ar' => ['footer.action_title' => 'عنوان تذييل مخصص من اللوحة']],
        ])->assertRedirect(route('dashboard.site-texts.edit', ['group' => 'layout']));

        auth()->logout();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('عنوان تذييل مخصص من اللوحة', false);
    }

    public function test_clearing_an_overridden_site_text_restores_the_original(): void
    {
        $admin = User::factory()->create();
        $original = __('footer.action_title');

        $this->actingAs($admin)->put(route('dashboard.site-texts.update'), [
            'texts' => ['ar' => ['footer.action_title' => 'نص مؤقت']],
        ]);
        $this->actingAs($admin)->put(route('dashboard.site-texts.update'), [
            'texts' => ['ar' => ['footer.action_title' => '']],
        ]);

        $this->assertSame($original, __('footer.action_title'));
    }

    public function test_site_text_must_keep_its_placeholders(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.site-texts.update'), [
                'texts' => ['ar' => ['footer.copyright' => 'جميع الحقوق محفوظة']],
            ])
            ->assertSessionHasErrors('texts');

        $this->assertDatabaseMissing('settings', ['key' => 'site_texts_ar']);
    }

    public function test_site_texts_outside_the_editable_groups_are_ignored(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.site-texts.update'), [
                'texts' => ['ar' => ['dashboard.overview' => 'مخترق']],
            ]);

        $this->assertNotSame('مخترق', __('dashboard.overview'));
    }

    public function test_saved_social_links_appear_in_the_footer(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.settings.update'), [
                'social' => ['instagram' => 'https://instagram.com/bader'],
            ])
            ->assertRedirect(route('dashboard.settings.edit'));

        auth()->logout();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('https://instagram.com/bader', false)
            ->assertDontSee(__('footer.social.facebook'), false);
    }

    public function test_social_links_must_be_web_urls(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.settings.update'), [
                'social' => ['facebook' => 'javascript:alert(1)'],
            ])
            ->assertSessionHasErrors('social.facebook');
    }

    public function test_custom_gift_designs_replace_the_defaults(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.settings.update'), [
                'gift_designs' => [
                    ['key' => 'graduation', 'label_ar' => 'هدية التخرج', 'label_en' => 'Graduation gift', 'from' => '#112233', 'to' => '#445566', 'accent' => '#ffffff'],
                ],
            ])
            ->assertRedirect(route('dashboard.settings.edit'));

        auth()->logout();

        $this->get(route('gift'))
            ->assertOk()
            ->assertSee('هدية التخرج', false)
            ->assertSee('#112233', false)
            ->assertDontSee('هدية رمضان', false);
    }

    public function test_gift_design_colours_must_be_hex_values(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.settings.update'), [
                'gift_designs' => [
                    ['key' => 'broken', 'label_ar' => 'تصميم', 'from' => 'red;background:url(x)'],
                ],
            ])
            ->assertSessionHasErrors('gift_designs.0.from');
    }

    public function test_svg_uploads_are_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->create())
            ->post(route('dashboard.programs.store'), [
                'title_ar' => 'برنامج',
                'key' => 'svg-program',
                'status' => 'published',
                'image_file' => UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
            ])
            ->assertSessionHasErrors('image_file');

        $this->assertDatabaseMissing('programs', ['key' => 'svg-program']);
    }

    public function test_each_form_of_a_count_dependent_text_is_edited_separately(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard.site-texts.edit', ['group' => 'projects']))
            ->assertOk()
            ->assertSee('name="texts[ar][project.donors_count][1]"', false);

        $this->put(route('dashboard.site-texts.update'), [
            'group' => 'projects',
            'texts' => ['ar' => ['project.donors_count' => ['', 'متبرع وحيد', '', '', '']]],
        ])->assertSessionHasNoErrors();

        $this->assertSame('متبرع وحيد', trans_choice('project.donors_count', 1));
        $this->assertSame('متبرعان', trans_choice('project.donors_count', 2));
        $this->assertSame('15 متبرعاً', trans_choice('project.donors_count', 15, ['count' => 15]));
    }

    public function test_uploaded_logo_and_favicon_replace_the_bundled_ones_until_reset(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('dashboard.settings.update'), [
                'brand_mark_star_file' => UploadedFile::fake()->image('mark.png', 256, 256),
                'brand_favicon_file' => UploadedFile::fake()->image('icon.png', 64, 64),
            ])
            ->assertRedirect(route('dashboard.settings.edit'));

        $markPath = (string) Setting::get('brand_mark_star');
        $faviconPath = (string) Setting::get('brand_favicon');
        Storage::disk('public')->assertExists(str_replace('storage/', '', $markPath));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(asset($markPath), false)
            ->assertSee('<link rel="icon" href="'.asset($faviconPath).'">', false);

        $this->actingAs($admin)->put(route('dashboard.settings.update'), ['brand_mark_star_reset' => '1']);

        $this->get(route('home'))->assertDontSee(asset($markPath), false);
    }

    public function test_logo_upload_rejects_svg_files(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.settings.update'), [
                'brand_mark_star_file' => UploadedFile::fake()->createWithContent('mark.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
            ])
            ->assertSessionHasErrors('brand_mark_star_file');

        $this->assertNull(Setting::get('brand_mark_star'));
    }

    public function test_donation_categories_can_be_added_and_removed(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.settings.update'), [
                'donation_categories' => [
                    ['key' => 'education', 'label_ar' => 'التعليم', 'label_en' => 'Education'],
                    ['key' => '', 'label_ar' => '', 'label_en' => ''],
                ],
            ])
            ->assertRedirect(route('dashboard.settings.edit'));

        auth()->logout();

        $this->get(route('donate'))
            ->assertOk()
            ->assertSee('value="education"', false)
            ->assertSee('value="general"', false)
            ->assertDontSee('value="zakat"', false);

        $donation = ['target_type' => 'general', 'amount' => 50, 'payment_method' => 'bank_transfer', 'reference_number' => 'TRX-1'];

        $this->post(route('donate.checkout'), $donation + ['donation_category' => 'zakat'])
            ->assertSessionHasErrors('donation_category');
        $this->post(route('donate.checkout'), $donation + ['donation_category' => 'education'])
            ->assertSessionHasNoErrors();
    }

    public function test_institutional_page_cards_can_be_added_beyond_the_defaults_and_removed(): void
    {
        $admin = User::factory()->create();
        $cards = [
            ['icon' => 'heart', 'title_ar' => 'البطاقة الأولى', 'title_en' => 'First card', 'text_ar' => 'نص أول', 'text_en' => ''],
            ['icon' => 'globe', 'title_ar' => 'البطاقة الثانية', 'title_en' => '', 'text_ar' => '', 'text_en' => ''],
            ['icon' => 'users', 'title_ar' => 'البطاقة الثالثة', 'title_en' => '', 'text_ar' => '', 'text_en' => ''],
            ['icon' => 'droplet', 'title_ar' => 'البطاقة الرابعة', 'title_en' => '', 'text_ar' => '', 'text_en' => ''],
        ];

        $this->actingAs($admin)
            ->put(route('dashboard.pages.update'), ['cards' => ['about' => $cards]])
            ->assertRedirect(route('dashboard.pages.edit'));

        $this->get(route('about'))
            ->assertOk()
            ->assertSeeInOrder(['البطاقة الأولى', 'البطاقة الرابعة'], false)
            ->assertDontSee(__('page.about.about.mission.text'), false);

        $cards[1]['title_ar'] = '';
        $this->actingAs($admin)->put(route('dashboard.pages.update'), ['cards' => ['about' => $cards]]);

        $this->get(route('about'))->assertDontSee('البطاقة الثانية', false);
        $this->assertSame('First card', SiteSettings::institutionalCards('about', 'en')[0]['title']);
        $this->assertSame('البطاقة الثالثة', SiteSettings::institutionalCards('about', 'en')[1]['title']);
    }

    public function test_page_card_icons_must_come_from_the_allowed_list(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.pages.update'), [
                'cards' => ['about' => [['icon' => '"><script>', 'title_ar' => 'بطاقة']]],
            ])
            ->assertSessionHasErrors('cards.about.0.icon');
    }

    public function test_hero_opens_with_the_organisation_slide_before_featured_projects(): void
    {
        Storage::fake('public');
        $campaign = Campaign::factory()->featured()->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder([__('home.hero_title'), __('home.hero.about'), $campaign->title], false);

        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.homepage.update'), [
                'sections' => ['quick_give' => ['visible' => '1']],
                'hero_intro_enabled' => '1',
                'hero_intro_image_file' => UploadedFile::fake()->image('hero.jpg', 1920, 1080),
            ])
            ->assertRedirect(route('dashboard.homepage.edit'));

        $this->get(route('home'))->assertSee(asset((string) Setting::get('hero_intro_image')), false);

        $this->put(route('dashboard.homepage.update'), [
            'sections' => ['quick_give' => ['visible' => '1']],
            'hero_intro_enabled' => '0',
        ]);

        $this->get(route('home'))
            ->assertSee($campaign->title, false)
            ->assertDontSee(__('home.hero.about'), false);
    }

    public function test_organisation_slide_content_and_buttons_are_edited_from_the_homepage_page(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('dashboard.homepage.update'), [
                'sections' => ['quick_give' => ['visible' => '1']],
                'hero_intro_badge_ar' => 'شارة مخصصة',
                'hero_intro_title_ar' => 'عنوان الشريحة الأولى',
                'hero_intro_text_ar' => 'وصف مكتوب من لوحة التحكم',
                'hero_intro_primary_label_ar' => 'ادعم الآن',
                'hero_intro_secondary_label_ar' => 'شاهد مشاريعنا',
                'hero_intro_primary_url' => '/zakat',
                'hero_intro_secondary_url' => 'https://example.org/report',
            ])
            ->assertSessionHasNoErrors();

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['شارة مخصصة', 'عنوان', 'الشريحة', 'الأولى', 'وصف مكتوب من لوحة التحكم', url('/zakat'), 'ادعم الآن', 'https://example.org/report', 'شاهد مشاريعنا'], false);

        $this->put(route('dashboard.homepage.update'), [
            'sections' => ['quick_give' => ['visible' => '1']],
            'hero_intro_primary_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors('hero_intro_primary_url');
    }

    public function test_homepage_sections_can_be_hidden_and_reordered(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.homepage.update'), [
                'sections' => [
                    'quick_give' => ['visible' => '1'],
                    'regions' => ['visible' => '1', 'order' => '5'],
                    'trust' => ['visible' => '1', 'order' => '2'],
                    'gift' => ['visible' => '0', 'order' => '3'],
                ],
            ])
            ->assertRedirect(route('dashboard.homepage.edit'));

        auth()->logout();

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['aria-labelledby="quick-give-title"', 'aria-labelledby="trust-pillars-title"', 'aria-labelledby="regions-band-title"'], false)
            ->assertDontSee('aria-labelledby="gift-section-title"', false);
    }
}
