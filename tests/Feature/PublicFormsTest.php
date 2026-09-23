<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\ImpactMetric;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_contact_form(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'محمد سعيد',
            'email' => 'mohammed@example.com',
            'phone' => '+968 91234567',
            'subject' => 'استفسار عن مشاريع الإغاثة',
            'message' => 'نود الاستفسار عن كيفية توجيه المساعدات للمناطق الشمالية.',
        ]);

        $response->assertSessionHas('success_message');
        $this->assertDatabaseHas('form_submissions', [
            'type' => 'contact',
            'name' => 'محمد سعيد',
            'email' => 'mohammed@example.com',
            'subject' => 'استفسار عن مشاريع الإغاثة',
            'status' => 'unread',
        ]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => '',
            'email' => 'not-an-email',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }

    public function test_user_can_submit_partnership_proposal(): void
    {
        $response = $this->post(route('partners.submit'), [
            'name' => 'د. خالد العماني',
            'organization' => 'مؤسسة الأمل الخيرية',
            'email' => 'contact@alamal-ngo.org',
            'phone' => '+968 99887766',
            'partnership_type' => 'تمويل برامج المياه',
            'message' => 'نرغب في عقد اتفاقية تعاون لتشغيل محطة تحلية مياه في غزة.',
        ]);

        $response->assertSessionHas('success_message');
        $this->assertDatabaseHas('form_submissions', [
            'type' => 'partnership',
            'name' => 'د. خالد العماني',
            'organization' => 'مؤسسة الأمل الخيرية',
            'status' => 'unread',
        ]);
    }

    public function test_user_can_submit_volunteer_application(): void
    {
        $response = $this->post(route('volunteer.submit'), [
            'name' => 'سارة أحمد',
            'email' => 'sara@example.com',
            'phone' => '+968 93332211',
            'location' => 'مسقط',
            'skills' => 'ترجمة وتصميم جرافيك',
            'availability' => 'عطلات نهاية الأسبوع',
            'message' => 'متحمسة للمساهمة في إعداد التقارير الإعلامية بالإنجليزية.',
        ]);

        $response->assertSessionHas('success_message');
        $this->assertDatabaseHas('form_submissions', [
            'type' => 'volunteer',
            'name' => 'سارة أحمد',
            'location' => 'مسقط',
            'status' => 'unread',
        ]);
    }

    public function test_user_can_submit_sponsorship_inquiry(): void
    {
        $response = $this->post(route('sponsorship.submit'), [
            'name' => 'أحمد الهنائي',
            'email' => 'ahmed@example.com',
            'phone' => '+968 95554433',
            'sponsorship_type' => 'orphan',
            'beneficiaries_count' => 2,
            'message' => 'أرغب في كفالة يتيمين شهرياً وتزويدي بتقارير دورية.',
        ]);

        $response->assertSessionHas('success_message');
        $this->assertDatabaseHas('form_submissions', [
            'type' => 'sponsorship',
            'name' => 'أحمد الهنائي',
            'status' => 'unread',
        ]);
    }

    public function test_user_can_submit_assistance_request(): void
    {
        $response = $this->post(route('assistance.submit'), [
            'name' => 'يوسف عبد الله',
            'national_id' => '901234567',
            'phone' => '+970 599123456',
            'location' => 'مخيم جباليا - شمال غزة',
            'family_members' => 6,
            'need_type' => 'food_shelter',
            'urgency' => 'urgent',
            'message' => 'المنزل متضرر كلياً ونحتاج خيمة وسلة غذائية عاجلة.',
        ]);

        $response->assertSessionHas('success_message');
        $this->assertDatabaseHas('form_submissions', [
            'type' => 'assistance',
            'name' => 'يوسف عبد الله',
            'location' => 'مخيم جباليا - شمال غزة',
            'status' => 'unread',
        ]);
    }

    public function test_donor_can_submit_bank_transfer_notice(): void
    {
        $campaign = Campaign::create([
            'title_ar' => 'حملة سقيا الماء',
            'title_en' => 'Water Relief Campaign',
            'key' => 'water-relief-test',
            'goal_amount' => 50000,
            'raised_amount' => 0,
            'status' => 'published',
        ]);

        $response = $this->post(route('donate.transfer.submit'), [
            'donor_name' => 'فاعل خير مسقط',
            'donor_email' => 'donor@example.com',
            'donor_phone' => '+968 98765432',
            'campaign_id' => $campaign->id,
            'amount' => 250.00,
            'reference_number' => 'BM-99882211',
            'transfer_date' => '2026-09-22',
            'notes' => 'تحويل لصالح حفر بئر ماء',
        ]);

        $response->assertSessionHas('success_message');

        // Check donation record created as pending
        $this->assertDatabaseHas('donations', [
            'donor_name' => 'فاعل خير مسقط',
            'amount' => 250.00,
            'reference_number' => 'BM-99882211',
            'status' => 'pending',
        ]);

        // Check inbox entry created
        $this->assertDatabaseHas('form_submissions', [
            'type' => 'donation_transfer',
            'name' => 'فاعل خير مسقط',
            'status' => 'unread',
        ]);
    }

    public function test_unapproved_impact_metrics_never_appear_on_public_site(): void
    {
        // 1. Create an unapproved metric
        ImpactMetric::create([
            'title_ar' => 'أسر مستفيدة غير معتمدة',
            'title_en' => 'Unapproved Beneficiary Families',
            'key' => 'unapproved-families',
            'value' => '999,999',
            'unit_ar' => 'أسرة',
            'unit_en' => 'Families',
            'is_approved' => false,
            'status' => 'draft',
        ]);

        // Check home page does NOT see unapproved value
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('999,999', false)
            ->assertDontSee('أسر مستفيدة غير معتمدة', false);

        // Check impact page does NOT see unapproved value
        $this->get(route('impact'))
            ->assertOk()
            ->assertDontSee('999,999', false)
            ->assertDontSee('أسر مستفيدة غير معتمدة', false);
    }

    public function test_approved_impact_metrics_appear_on_public_site(): void
    {
        // 1. Create an approved metric
        ImpactMetric::create([
            'title_ar' => 'وجبات ساخنة موزعة',
            'title_en' => 'Distributed Hot Meals',
            'key' => 'hot-meals-approved',
            'value' => '350,000+',
            'unit_ar' => 'وجبة ساخنة',
            'unit_en' => 'Hot Meals',
            'is_approved' => true,
            'approved_at' => now(),
            'status' => 'approved',
            'order' => 1,
        ]);

        // Check home page displays the approved metric
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('350,000+', false)
            ->assertSee('وجبات ساخنة موزعة', false);

        // Check impact page displays the approved metric
        $this->get(route('impact'))
            ->assertOk()
            ->assertSee('350,000+', false)
            ->assertSee('وجبات ساخنة موزعة', false);
    }
}
