<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Facility;
use App\Models\MediaAsset;
use App\Models\Program;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_content_modules(): void
    {
        $this->get(route('dashboard.programs.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard.facilities.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard.campaigns.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard.stories.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard.media.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_manage_programs(): void
    {
        $admin = User::factory()->create();

        // 1. View Index and Create Form
        $this->actingAs($admin)->get(route('dashboard.programs.index'))->assertOk();
        $this->actingAs($admin)->get(route('dashboard.programs.create'))->assertOk();

        // 2. Store Program
        $response = $this->actingAs($admin)->post(route('dashboard.programs.store'), [
            'title_ar' => 'برنامج الإغاثة الطارئة المتقدم',
            'title_en' => 'Advanced Emergency Relief Program',
            'key' => 'emergency-relief-adv',
            'description_ar' => 'وصف تفصيلي للبرنامج المتقدم',
            'description_en' => 'Detailed description of advanced program',
            'order' => 1,
            'status' => 'published',
        ]);

        $response->assertRedirect(route('dashboard.programs.index'));
        $this->assertDatabaseHas('programs', [
            'key' => 'emergency-relief-adv',
            'title_ar' => 'برنامج الإغاثة الطارئة المتقدم',
            'status' => 'published',
        ]);

        $program = Program::where('key', 'emergency-relief-adv')->first();

        // 3. Edit Program
        $this->actingAs($admin)->get(route('dashboard.programs.edit', $program))->assertOk();

        $updateResponse = $this->actingAs($admin)->put(route('dashboard.programs.update', $program), [
            'title_ar' => 'برنامج الإغاثة الطارئة المحدث',
            'title_en' => 'Updated Emergency Relief Program',
            'key' => 'emergency-relief-adv',
            'description_ar' => 'وصف محدث للبرنامج',
            'description_en' => 'Updated description',
            'order' => 2,
            'status' => 'under_review',
        ]);

        $updateResponse->assertRedirect(route('dashboard.programs.index'));
        $this->assertDatabaseHas('programs', [
            'key' => 'emergency-relief-adv',
            'title_ar' => 'برنامج الإغاثة الطارئة المحدث',
            'status' => 'under_review',
        ]);

        // 4. Delete Program
        $deleteResponse = $this->actingAs($admin)->delete(route('dashboard.programs.destroy', $program));
        $deleteResponse->assertRedirect(route('dashboard.programs.index'));
        $this->assertDatabaseMissing('programs', ['id' => $program->id]);
    }

    public function test_admin_can_manage_facilities(): void
    {
        $admin = User::factory()->create();

        // 1. View Index and Create Form
        $this->actingAs($admin)->get(route('dashboard.facilities.index'))->assertOk();
        $this->actingAs($admin)->get(route('dashboard.facilities.create'))->assertOk();

        // 2. Store Facility
        $response = $this->actingAs($admin)->post(route('dashboard.facilities.store'), [
            'name_ar' => 'مطبخ بادر الميداني المركزي',
            'name_en' => 'Bader Central Field Kitchen',
            'key' => 'central-kitchen',
            'description_ar' => 'يقدم وجبات ساخنة يومية للأسر المتضررة',
            'description_en' => 'Provides daily hot meals to affected families',
            'location_ar' => 'شمال غزة',
            'location_en' => 'North Gaza',
            'order' => 1,
            'status' => 'published',
        ]);

        $response->assertRedirect(route('dashboard.facilities.index'));
        $this->assertDatabaseHas('facilities', [
            'key' => 'central-kitchen',
            'name_ar' => 'مطبخ بادر الميداني المركزي',
            'status' => 'published',
        ]);

        $facility = Facility::where('key', 'central-kitchen')->first();

        // 3. Edit Facility
        $this->actingAs($admin)->get(route('dashboard.facilities.edit', $facility))->assertOk();

        $updateResponse = $this->actingAs($admin)->put(route('dashboard.facilities.update', $facility), [
            'name_ar' => 'مطبخ بادر الميداني المركزي - المحدث',
            'name_en' => 'Bader Central Field Kitchen - Updated',
            'key' => 'central-kitchen',
            'description_ar' => 'وصف محدث',
            'description_en' => 'Updated description',
            'location_ar' => 'غزة - الوسطى',
            'location_en' => 'Gaza - Central',
            'order' => 3,
            'status' => 'draft',
        ]);

        $updateResponse->assertRedirect(route('dashboard.facilities.index'));
        $this->assertDatabaseHas('facilities', [
            'id' => $facility->id,
            'name_ar' => 'مطبخ بادر الميداني المركزي - المحدث',
            'status' => 'draft',
        ]);

        // 4. Delete Facility
        $deleteResponse = $this->actingAs($admin)->delete(route('dashboard.facilities.destroy', $facility));
        $deleteResponse->assertRedirect(route('dashboard.facilities.index'));
        $this->assertDatabaseMissing('facilities', ['id' => $facility->id]);
    }

    public function test_admin_can_manage_campaigns(): void
    {
        $admin = User::factory()->create();

        // 1. View Index and Create Form
        $this->actingAs($admin)->get(route('dashboard.campaigns.index'))->assertOk();
        $this->actingAs($admin)->get(route('dashboard.campaigns.create'))->assertOk();

        // 2. Store Campaign
        $response = $this->actingAs($admin)->post(route('dashboard.campaigns.store'), [
            'title_ar' => 'حملة سقيا الماء العاجلة 2026',
            'title_en' => 'Urgent Water Relief Campaign 2026',
            'key' => 'water-relief-2026',
            'description_ar' => 'توفير صهاريج مياه صالحة للشرب',
            'description_en' => 'Providing clean drinking water tanks',
            'goal_amount' => 50000,
            'raised_amount' => 12500,
            'currency_ar' => 'ريال عماني',
            'currency_en' => 'OMR',
            'is_featured' => true,
            'status' => 'published',
        ]);

        $response->assertRedirect(route('dashboard.campaigns.index'));
        $this->assertDatabaseHas('campaigns', [
            'key' => 'water-relief-2026',
            'title_ar' => 'حملة سقيا الماء العاجلة 2026',
            'goal_amount' => 50000,
            'is_featured' => 1,
            'status' => 'published',
        ]);

        $campaign = Campaign::where('key', 'water-relief-2026')->first();

        // 3. Edit Campaign
        $this->actingAs($admin)->get(route('dashboard.campaigns.edit', $campaign))->assertOk();

        $updateResponse = $this->actingAs($admin)->put(route('dashboard.campaigns.update', $campaign), [
            'title_ar' => 'حملة سقيا الماء العاجلة المحدثة',
            'title_en' => 'Updated Urgent Water Relief Campaign',
            'key' => 'water-relief-2026',
            'description_ar' => 'وصف محدث',
            'description_en' => 'Updated description',
            'goal_amount' => 60000,
            'raised_amount' => 30000,
            'currency_ar' => 'ريال عماني',
            'currency_en' => 'OMR',
            'is_featured' => false,
            'status' => 'under_review',
        ]);

        $updateResponse->assertRedirect(route('dashboard.campaigns.index'));
        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'title_ar' => 'حملة سقيا الماء العاجلة المحدثة',
            'goal_amount' => 60000,
            'is_featured' => 0,
            'status' => 'under_review',
        ]);

        // 4. Delete Campaign
        $deleteResponse = $this->actingAs($admin)->delete(route('dashboard.campaigns.destroy', $campaign));
        $deleteResponse->assertRedirect(route('dashboard.campaigns.index'));
        $this->assertDatabaseMissing('campaigns', ['id' => $campaign->id]);
    }

    public function test_admin_can_manage_stories(): void
    {
        $admin = User::factory()->create();

        // 1. View Index and Create Form
        $this->actingAs($admin)->get(route('dashboard.stories.index'))->assertOk();
        $this->actingAs($admin)->get(route('dashboard.stories.create'))->assertOk();

        // 2. Store Story
        $response = $this->actingAs($admin)->post(route('dashboard.stories.store'), [
            'title_ar' => 'قصة أمل من مخيم النزوح',
            'title_en' => 'Story of Hope from Displacement Camp',
            'key' => 'story-hope-camp',
            'excerpt_ar' => 'موجز القصة الإنسانية الملهمة',
            'excerpt_en' => 'Inspiring humanitarian story summary',
            'content_ar' => 'تفاصيل القصة الميدانية الكاملة',
            'content_en' => 'Full field story details',
            'category_ar' => 'قصص ميدانية',
            'category_en' => 'Field Stories',
            'published_at' => '2026-09-20',
            'is_featured' => true,
            'status' => 'published',
        ]);

        $response->assertRedirect(route('dashboard.stories.index'));
        $this->assertDatabaseHas('stories', [
            'key' => 'story-hope-camp',
            'title_ar' => 'قصة أمل من مخيم النزوح',
            'is_featured' => 1,
            'status' => 'published',
        ]);

        $story = Story::where('key', 'story-hope-camp')->first();

        // 3. Edit Story
        $this->actingAs($admin)->get(route('dashboard.stories.edit', $story))->assertOk();

        $updateResponse = $this->actingAs($admin)->put(route('dashboard.stories.update', $story), [
            'title_ar' => 'قصة أمل من مخيم النزوح - محدثة',
            'title_en' => 'Story of Hope from Displacement Camp - Updated',
            'key' => 'story-hope-camp',
            'excerpt_ar' => 'موجز محدث',
            'excerpt_en' => 'Updated excerpt',
            'content_ar' => 'محتوى محدث',
            'content_en' => 'Updated content',
            'category_ar' => 'ميداني',
            'category_en' => 'Field',
            'published_at' => '2026-09-21',
            'is_featured' => false,
            'status' => 'draft',
        ]);

        $updateResponse->assertRedirect(route('dashboard.stories.index'));
        $this->assertDatabaseHas('stories', [
            'id' => $story->id,
            'title_ar' => 'قصة أمل من مخيم النزوح - محدثة',
            'is_featured' => 0,
            'status' => 'draft',
        ]);

        // 4. Delete Story
        $deleteResponse = $this->actingAs($admin)->delete(route('dashboard.stories.destroy', $story));
        $deleteResponse->assertRedirect(route('dashboard.stories.index'));
        $this->assertDatabaseMissing('stories', ['id' => $story->id]);
    }

    public function test_admin_can_manage_media_assets(): void
    {
        $admin = User::factory()->create();

        // 1. View Index and Create Form
        $this->actingAs($admin)->get(route('dashboard.media.index'))->assertOk();
        $this->actingAs($admin)->get(route('dashboard.media.create'))->assertOk();

        // 2. Store Media Asset
        $response = $this->actingAs($admin)->post(route('dashboard.media.store'), [
            'title_ar' => 'صورة توزيع السلال الغذائية - خان يونس',
            'title_en' => 'Food Basket Distribution Photo - Khan Younis',
            'file_path' => 'media/food-dist-01.jpg',
            'category' => 'food_relief',
            'has_usage_consent' => 1,
            'consent_notes' => 'المستفيدون وافقوا خطياً وفق سياسة الحماية',
        ]);

        $response->assertRedirect(route('dashboard.media.index'));
        $this->assertDatabaseHas('media_assets', [
            'title_ar' => 'صورة توزيع السلال الغذائية - خان يونس',
            'file_path' => 'media/food-dist-01.jpg',
            'has_usage_consent' => 1,
        ]);

        $media = MediaAsset::where('file_path', 'media/food-dist-01.jpg')->first();

        // 3. Edit Media Asset
        $this->actingAs($admin)->get(route('dashboard.media.edit', $media))->assertOk();

        $updateResponse = $this->actingAs($admin)->put(route('dashboard.media.update', $media), [
            'title_ar' => 'صورة توزيع السلال الغذائية المحدثة',
            'title_en' => 'Updated Food Basket Distribution Photo',
            'file_path' => 'media/food-dist-01-updated.jpg',
            'category' => 'food_relief_updated',
            'has_usage_consent' => 1,
            'consent_notes' => 'ملاحظات موافقة محدثة',
        ]);

        $updateResponse->assertRedirect(route('dashboard.media.index'));
        $this->assertDatabaseHas('media_assets', [
            'id' => $media->id,
            'title_ar' => 'صورة توزيع السلال الغذائية المحدثة',
            'file_path' => 'media/food-dist-01-updated.jpg',
        ]);

        // 4. Delete Media Asset
        $deleteResponse = $this->actingAs($admin)->delete(route('dashboard.media.destroy', $media));
        $deleteResponse->assertRedirect(route('dashboard.media.index'));
        $this->assertDatabaseMissing('media_assets', ['id' => $media->id]);
    }
}
