<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\FormSubmission;
use App\Models\ImpactMetric;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardInboxAndOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_phase_8_dashboard_modules(): void
    {
        $this->get(route('dashboard.inbox.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard.impact.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard.donations.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_manage_inbox_submissions(): void
    {
        $admin = User::factory()->create();

        $submission = FormSubmission::create([
            'type' => 'partnership',
            'name' => 'سعيد الكندي',
            'organization' => 'مؤسسة النور',
            'email' => 'saeed@alnoor.om',
            'phone' => '+968 91112233',
            'message' => 'مقترح رعاية مشاريع الشتاء',
            'status' => 'unread',
        ]);

        // 1. View Index
        $this->actingAs($admin)->get(route('dashboard.inbox.index'))
            ->assertOk()
            ->assertSee('سعيد الكندي', false)
            ->assertSee('مؤسسة النور', false);

        // 2. Filter by type
        $this->actingAs($admin)->get(route('dashboard.inbox.index', ['type' => 'partnership']))
            ->assertOk()
            ->assertSee('سعيد الكندي', false);

        // 3. View Show page (status transitions to in_progress)
        $this->actingAs($admin)->get(route('dashboard.inbox.show', $submission))
            ->assertOk()
            ->assertSee('مقترح رعاية مشاريع الشتاء', false);

        $this->assertDatabaseHas('form_submissions', [
            'id' => $submission->id,
            'status' => 'in_progress',
        ]);

        // 4. Update status & add internal notes
        $updateResponse = $this->actingAs($admin)->put(route('dashboard.inbox.update', $submission), [
            'status' => 'resolved',
            'notes' => 'تم التواصل هاتفياً وتحديد موعد اجتماع تنسيقي.',
        ]);

        $updateResponse->assertRedirect(route('dashboard.inbox.show', $submission));
        $this->assertDatabaseHas('form_submissions', [
            'id' => $submission->id,
            'status' => 'resolved',
            'notes' => 'تم التواصل هاتفياً وتحديد موعد اجتماع تنسيقي.',
        ]);

        // 5. Delete submission
        $deleteResponse = $this->actingAs($admin)->delete(route('dashboard.inbox.destroy', $submission));
        $deleteResponse->assertRedirect(route('dashboard.inbox.index'));
        $this->assertDatabaseMissing('form_submissions', ['id' => $submission->id]);
    }

    public function test_admin_can_manage_impact_metrics_and_toggle_approval(): void
    {
        $admin = User::factory()->create();

        // 1. View Index and Create Form
        $this->actingAs($admin)->get(route('dashboard.impact.index'))->assertOk();
        $this->actingAs($admin)->get(route('dashboard.impact.create'))->assertOk();

        // 2. Create Metric as draft
        $createResponse = $this->actingAs($admin)->post(route('dashboard.impact.store'), [
            'title_ar' => 'صهاريج مياه منقولة',
            'title_en' => 'Water Tankers Delivered',
            'key' => 'water-tankers-count',
            'value' => '1,200+',
            'unit_ar' => 'صهريج',
            'unit_en' => 'Tankers',
            'category' => 'water',
            'order' => 1,
            'is_approved' => 0,
            'status' => 'draft',
        ]);

        $createResponse->assertRedirect(route('dashboard.impact.index'));
        $this->assertDatabaseHas('impact_metrics', [
            'key' => 'water-tankers-count',
            'is_approved' => 0,
            'status' => 'draft',
        ]);

        $metric = ImpactMetric::where('key', 'water-tankers-count')->first();

        // 3. Toggle Approval to Published
        $toggleResponse = $this->actingAs($admin)->patch(route('dashboard.impact.toggle', $metric));
        $toggleResponse->assertRedirect(route('dashboard.impact.index'));

        $this->assertDatabaseHas('impact_metrics', [
            'id' => $metric->id,
            'is_approved' => 1,
            'status' => 'approved',
        ]);

        // 4. Edit Metric
        $this->actingAs($admin)->get(route('dashboard.impact.edit', $metric))->assertOk();

        $updateResponse = $this->actingAs($admin)->put(route('dashboard.impact.update', $metric), [
            'title_ar' => 'صهاريج مياه صالحة للشرب',
            'title_en' => 'Clean Water Tankers Delivered',
            'key' => 'water-tankers-count',
            'value' => '1,500+',
            'unit_ar' => 'صهريج',
            'unit_en' => 'Tankers',
            'category' => 'water',
            'order' => 2,
            'is_approved' => 1,
            'status' => 'approved',
        ]);

        $updateResponse->assertRedirect(route('dashboard.impact.index'));
        $this->assertDatabaseHas('impact_metrics', [
            'id' => $metric->id,
            'value' => '1,500+',
            'title_ar' => 'صهاريج مياه صالحة للشرب',
        ]);

        // 5. Delete Metric
        $deleteResponse = $this->actingAs($admin)->delete(route('dashboard.impact.destroy', $metric));
        $deleteResponse->assertRedirect(route('dashboard.impact.index'));
        $this->assertDatabaseMissing('impact_metrics', ['id' => $metric->id]);
    }

    public function test_admin_can_record_and_verify_offline_donations(): void
    {
        $admin = User::factory()->create();

        $campaign = Campaign::create([
            'title_ar' => 'حملة الشتاء الدافئ',
            'title_en' => 'Warm Winter Campaign',
            'key' => 'warm-winter-2026',
            'goal_amount' => 100000,
            'raised_amount' => 5000,
            'status' => 'published',
        ]);

        // 1. View Index and Create Form
        $this->actingAs($admin)->get(route('dashboard.donations.index'))->assertOk();
        $this->actingAs($admin)->get(route('dashboard.donations.create'))->assertOk();

        // 2. Store a verified donation directly -> increments campaign raised amount
        $storeResponse = $this->actingAs($admin)->post(route('dashboard.donations.store'), [
            'donor_name' => 'شركة النماء للتجارة',
            'donor_email' => 'finance@namaa.om',
            'donor_phone' => '+968 94445566',
            'campaign_id' => $campaign->id,
            'amount' => 3000.00,
            'currency_ar' => 'ريال عماني',
            'currency_en' => 'OMR',
            'payment_method' => 'bank_transfer',
            'reference_number' => 'TXN-778899',
            'transfer_date' => '2026-09-22',
            'notes' => 'تحويل بنكي مباشر لحساب المؤسسة',
            'status' => 'verified',
        ]);

        $storeResponse->assertRedirect(route('dashboard.donations.index'));
        $this->assertDatabaseHas('donations', [
            'donor_name' => 'شركة النماء للتجارة',
            'amount' => 3000.00,
            'status' => 'verified',
        ]);

        $campaign->refresh();
        $this->assertEquals(8000.00, (float) $campaign->raised_amount);

        // 3. Store a pending donation and verify via verify action
        $pendingDonation = Donation::create([
            'donor_name' => 'متبرع كريم',
            'campaign_id' => $campaign->id,
            'amount' => 1200.00,
            'currency_ar' => 'ريال عماني',
            'currency_en' => 'OMR',
            'payment_method' => 'cash',
            'reference_number' => 'RCP-0012',
            'status' => 'pending',
        ]);

        $verifyResponse = $this->actingAs($admin)->patch(route('dashboard.donations.verify', $pendingDonation));
        $verifyResponse->assertRedirect(route('dashboard.donations.index'));

        $this->assertDatabaseHas('donations', [
            'id' => $pendingDonation->id,
            'status' => 'verified',
        ]);

        $campaign->refresh();
        $this->assertEquals(9200.00, (float) $campaign->raised_amount);

        // 4. Delete Donation
        $deleteResponse = $this->actingAs($admin)->delete(route('dashboard.donations.destroy', $pendingDonation));
        $deleteResponse->assertRedirect(route('dashboard.donations.index'));
        $this->assertDatabaseMissing('donations', ['id' => $pendingDonation->id]);
    }
}
