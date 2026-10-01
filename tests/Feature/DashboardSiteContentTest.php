<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
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
}
