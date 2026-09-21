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
            'contact_phone' => '+968 1234 5678',
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
                'hq_location_ar' => 'سلطنة عمان، مسقط - المقر الرئيسي المعتمد',
                'hq_location_en' => 'Sultanate of Oman, Muscat - Main HQ',
                'field_location_ar' => 'فلسطين، قطاع غزة - المكتب الميداني',
                'field_location_en' => 'Palestine, Gaza Strip - Field Office',
                'contact_email' => 'contact@baderhumanitarian.com',
                'contact_phone' => '+968 9988 7766',
                'contact_address_ar' => 'مسقط، سلطنة عمان',
                'contact_address_en' => 'Muscat, Sultanate of Oman',
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
            'value' => '+968 9988 7766',
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
                'hq_location_ar' => 'سلطنة عمان، مسقط - المقر الرئيسي الجديد',
                'field_location_ar' => 'فلسطين، قطاع غزة - المركز الميداني المعتمد',
                'contact_phone' => '+968 9988 7766',
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
            ->assertSee('سلطنة عمان، مسقط - المقر الرئيسي الجديد', false)
            ->assertSee('فلسطين، قطاع غزة - المركز الميداني المعتمد', false);

        // 2. Check Contact page displays the updated phone and email
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('+968 9988 7766', false)
            ->assertSee('director@baderhumanitarian.com', false);
    }

    public function test_admin_can_view_institutional_pages_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.pages.edit'))
            ->assertOk()
            ->assertSee(__('dashboard.pages_title'), false)
            ->assertSee(__('dashboard.section_about_title'), false)
            ->assertSee(__('dashboard.section_policies_title'), false)
            ->assertSee(__('dashboard.section_faq_title'), false);
    }

    public function test_admin_can_update_institutional_pages_and_reflected_on_public_site(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->put(route('dashboard.pages.update'), [
                'inst_about_intro_ar' => 'مؤسسة بادر الإنسانية - مقدمة محدثة ومخصصة للتعريف بالجمعية.',
                'inst_about_mission_title_ar' => 'رسالتنا الإنسانية المحدثة',
                'inst_about_mission_text_ar' => 'نص الرسالة المحدث من لوحة التحكم.',
                'faq_identity_q_ar' => 'سؤال مخصص ومحدث عن هوية مؤسسة بادر؟',
                'faq_identity_a_ar' => 'إجابة مفصلة ومحدثة تم تحريرها من لوحة التحكم.',
            ]);

        $response->assertRedirect(route('dashboard.pages.edit'));
        $response->assertSessionHas('status', __('dashboard.pages_saved'));

        // Check Public About page
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('مؤسسة بادر الإنسانية - مقدمة محدثة ومخصصة للتعريف بالجمعية.', false)
            ->assertSee('رسالتنا الإنسانية المحدثة', false)
            ->assertSee('نص الرسالة المحدث من لوحة التحكم.', false);

        // Check Public FAQ page
        $this->get(route('faq'))
            ->assertOk()
            ->assertSee('سؤال مخصص ومحدث عن هوية مؤسسة بادر؟', false)
            ->assertSee('إجابة مفصلة ومحدثة تم تحريرها من لوحة التحكم.', false);
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
