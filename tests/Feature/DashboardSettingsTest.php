<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_site_settings(): void
    {
        $this->get(route('dashboard.settings.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_site_settings(): void
    {
        $this->put(route('dashboard.settings.update'), [
            'contact_phone' => '+970 59 123 4567',
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_institutional_pages(): void
    {
        $this->get(route('dashboard.pages.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_institutional_pages(): void
    {
        $this->put(route('dashboard.pages.update'), [
            'inst_about_intro_ar' => 'Test intro',
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_view_site_settings_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.settings.edit'))
            ->assertOk()
            ->assertSee(__('dashboard.settings_title'), false)
            ->assertSee(__('dashboard.section_identity_title'), false)
            ->assertSee(__('dashboard.section_contact_title'), false)
            ->assertSee(__('dashboard.section_urgent_title'), false)
            ->assertSee('dir="rtl"', false);
    }

    public function test_admin_can_update_site_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->put(route('dashboard.settings.update'), [
                'founded_year' => '2021',
                'hq_location_ar' => 'فلسطين، قطاع غزة - المقر الرئيسي المعتمد',
                'hq_location_en' => 'Gaza Strip, Palestine - Main HQ',
                'field_location_ar' => 'فلسطين، قطاع غزة - المكتب الميداني',
                'field_location_en' => 'Palestine, Gaza Strip - Field Office',
                'contact_email' => 'contact@baderhumanitarian.com',
                'contact_phone' => '+970 59988 7766',
                'contact_address_ar' => 'غزة، فلسطين',
                'contact_address_en' => 'Gaza, Palestine',
                'urgent_enabled' => '1',
                'urgent_text_ar' => 'نداء إغاثة عاجل لشمال غزة',
                'urgent_text_en' => 'Emergency Appeal for North Gaza',
                'urgent_url' => 'https://baderhumanitarian.com/donate',
            ]);

        $response->assertRedirect(route('dashboard.settings.edit'));
        $response->assertSessionHas('status', __('dashboard.settings_saved'));

        $this->assertDatabaseHas('settings', [
            'key' => 'founded_year',
            'value' => '2021',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'contact_phone',
            'value' => '+970 59988 7766',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'urgent_text_ar',
            'value' => 'نداء إغاثة عاجل لشمال غزة',
        ]);
    }

    public function test_updated_settings_immediately_reflected_on_public_site(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('dashboard.settings.update'), [
                'hq_location_ar' => 'فلسطين، قطاع غزة - المقر الرئيسي الجديد',
                'field_location_ar' => 'فلسطين، قطاع غزة - المركز الميداني المعتمد',
                'contact_phone' => '+970 59988 7766',
                'contact_email' => 'director@baderhumanitarian.com',
                'urgent_enabled' => '1',
                'urgent_text_ar' => 'نداء إغاثة عاجل وفوري لأهلنا في قطاع غزة',
                'urgent_url' => 'https://baderhumanitarian.com/donate',
            ]);

        // 1. Check Home page shows the new HQ in footer and new urgent bar
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('نداء إغاثة عاجل وفوري لأهلنا في قطاع غزة', false)
            ->assertSee('https://baderhumanitarian.com/donate', false)
            ->assertSee('فلسطين، قطاع غزة - المقر الرئيسي الجديد', false)
            ->assertSee('فلسطين، قطاع غزة - المركز الميداني المعتمد', false);

        // 2. Check Contact page displays the updated phone and email
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('+970 59988 7766', false)
            ->assertSee('director@baderhumanitarian.com', false);
    }

    public function test_admin_can_view_institutional_pages_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.pages.edit'))
            ->assertOk()
            ->assertSee(__('dashboard.pages_title'), false)
            ->assertSee(__('page.partners.title'), false)
            ->assertSee(__('page.volunteer.title'), false)
            ->assertSee('name="inst_about_president_speech_text_ar"', false)
            ->assertSee('name="cards[about][3][title_ar]"', false)
            ->assertSee('name="inst_contact_intro_ar"', false);
    }

    public function test_admin_can_update_institutional_pages_and_reflected_on_public_site(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->put(route('dashboard.pages.update'), [
                'inst_about_intro_ar' => 'مؤسسة بادر الإنسانية - مقدمة محدثة ومخصصة للتعريف بالمؤسسة.',
                'inst_about_president_speech_text_ar' => 'كلمة رئيس المؤسسة المكتوبة من لوحة التحكم.',
                'inst_about_vision_text_ar' => 'رؤية بادر المكتوبة من لوحة التحكم.',
                'inst_contact_intro_ar' => 'مقدمة صفحة التواصل من لوحة التحكم.',
                'cards' => [
                    'about' => [['icon' => 'heart', 'title_ar' => 'رسالتنا الإنسانية المحدثة', 'text_ar' => 'نص الرسالة المحدث من لوحة التحكم.']],
                    'partners' => [['icon' => 'globe', 'title_ar' => 'شراكات محلية محدثة']],
                ],
            ]);

        $response->assertRedirect(route('dashboard.pages.edit'));
        $response->assertSessionHas('status', __('dashboard.pages_saved'));

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('مؤسسة بادر الإنسانية - مقدمة محدثة ومخصصة للتعريف بالمؤسسة.', false)
            ->assertSee('رسالتنا الإنسانية المحدثة', false)
            ->assertSee('نص الرسالة المحدث من لوحة التحكم.', false)
            ->assertSee('كلمة رئيس المؤسسة المكتوبة من لوحة التحكم.', false)
            ->assertSee('رؤية بادر المكتوبة من لوحة التحكم.', false);

        $this->get(route('partners'))->assertOk()->assertSee('شراكات محلية محدثة', false);
        $this->get(route('contact'))->assertOk()->assertSee('مقدمة صفحة التواصل من لوحة التحكم.', false);
    }

    public function test_founding_year_from_settings_is_used_by_every_text_that_mentions_it(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('>2023</dd>', false)
            ->assertSee('تأسست عام 2023', false);

        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.settings.update'), ['founded_year' => '2022']);

        $this->get(route('about'))
            ->assertSee('>2022</dd>', false)
            ->assertSee('تأسست عام 2022', false)
            ->assertDontSee(':founded_year', false);
        $this->get(route('faq'))
            ->assertSee('تأسست عام 2022', false)
            ->assertDontSee(':founded_year', false);
        $this->get(route('dashboard.pages.edit'))
            ->assertSee('تأسست عام :founded_year', false);
    }

    public function test_about_page_hides_president_speech_and_vision_until_they_are_filled_in(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertDontSee(__('about_page.president_kicker'), false);
    }

    public function test_institutional_pages_ignore_fields_outside_the_editable_pages(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.pages.update'), [
                'inst_about_intro_ar' => 'مقدمة',
                'inst_unknown_page_intro_ar' => 'لا يجب حفظه',
            ])
            ->assertRedirect(route('dashboard.pages.edit'));

        $this->assertDatabaseMissing('settings', ['key' => 'inst_unknown_page_intro_ar']);
    }

    public function test_validation_catches_invalid_urls_or_emails(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->put(route('dashboard.settings.update'), [
                'contact_email' => 'invalid-email-format',
                'urgent_url' => 'not-a-valid-url',
            ]);

        $response->assertSessionHasErrors(['contact_email', 'urgent_url']);
    }
}
