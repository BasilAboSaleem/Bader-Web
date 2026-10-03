<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Program;
use App\Models\Region;
use App\Models\Setting;
use App\Models\SponsorshipCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DashboardGivingContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_regions_or_sponsorship_cases(): void
    {
        $this->get(route('dashboard.regions.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard.sponsorship-cases.index'))->assertRedirect(route('login'));
        $this->post(route('dashboard.sponsorship-cases.store'))->assertRedirect(route('login'));
    }

    public function test_admin_can_manage_regions_and_the_public_menu_refreshes(): void
    {
        $admin = User::factory()->create();

        $this->get(route('contact'))->assertDontSee('منطقة المواصي');

        $this->actingAs($admin)->get(route('dashboard.regions.create'))->assertOk();
        $this->actingAs($admin)->post(route('dashboard.regions.store'), [
            'name_ar' => 'منطقة المواصي',
            'name_en' => 'Al-Mawasi',
            'key' => 'mawasi',
            'map_x' => 30,
            'map_y' => 75,
            'map_area' => 'khan_younis',
            'status' => 'published',
        ])->assertRedirect(route('dashboard.regions.index'));

        $region = Region::sole();
        $this->assertSame(0, $region->order);
        $this->assertSame('khan_younis', $region->map_area);
        $this->get(route('contact'))->assertSee('منطقة المواصي');

        $this->actingAs($admin)->put(route('dashboard.regions.update', $region), [
            'name_ar' => 'منطقة المواصي',
            'key' => 'mawasi',
            'map_x' => 120,
            'map_y' => 75,
            'map_area' => 'unknown_area',
            'status' => 'draft',
        ])->assertSessionHasErrors(['map_x', 'map_area']);

        $this->actingAs($admin)->put(route('dashboard.regions.update', $region), [
            'name_ar' => 'منطقة المواصي',
            'key' => 'mawasi',
            'map_x' => 30,
            'map_y' => 75,
            'status' => 'published',
            'impact_metrics' => [
                ['value' => ' 3,200 ', 'icon' => 'droplet', 'label_ar' => 'لتر مياه يوميا', 'label_en' => 'litres of water a day'],
                ['value' => '', 'icon' => 'users', 'label_ar' => '', 'label_en' => ''],
            ],
        ])->assertRedirect(route('dashboard.regions.index'));
        $this->assertSame(
            [['value' => '3,200', 'icon' => 'droplet', 'label_ar' => 'لتر مياه يوميا', 'label_en' => 'litres of water a day']],
            $region->fresh()->impact_metrics,
        );
        $this->actingAs($admin)->get(route('dashboard.regions.edit', $region))
            ->assertOk()
            ->assertSee('value="لتر مياه يوميا"', false);

        $this->actingAs($admin)->put(route('dashboard.regions.update', $region), [
            'name_ar' => 'منطقة المواصي',
            'key' => 'mawasi',
            'map_x' => 30,
            'map_y' => 75,
            'status' => 'published',
            'impact_metrics' => [
                ['value' => '', 'icon' => 'rocket', 'label_ar' => 'مستفيد'],
            ],
        ])->assertSessionHasErrors(['impact_metrics.0.value', 'impact_metrics.0.icon']);

        $this->actingAs($admin)->put(route('dashboard.regions.update', $region), [
            'name_ar' => 'منطقة المواصي',
            'key' => 'mawasi',
            'map_x' => 30,
            'map_y' => 75,
            'status' => 'published',
            'impact_metrics' => [
                ['value' => '3,200', 'icon' => 'droplet', 'label_ar' => '', 'label_en' => ''],
            ],
        ])->assertSessionHasErrors('impact_metrics.0.label_ar');

        $this->actingAs($admin)->delete(route('dashboard.regions.destroy', $region))
            ->assertRedirect(route('dashboard.regions.index'));
        $this->assertModelMissing($region);
        $this->get(route('contact'))->assertDontSee('منطقة المواصي');
    }

    public function test_admin_can_manage_sponsorship_cases(): void
    {
        $admin = User::factory()->create();
        $region = Region::factory()->create();
        SponsorshipCase::factory()->create(['code' => 'GZ-100']);

        $this->actingAs($admin)->get(route('dashboard.sponsorship-cases.create'))->assertOk();

        $caseData = [
            'type' => 'widow',
            'name_ar' => 'أم محمد',
            'region_id' => $region->id,
            'monthly_amount' => 60,
            'duration_months' => 12,
            'status' => SponsorshipCase::STATUS_AVAILABLE,
        ];

        $this->actingAs($admin)->post(route('dashboard.sponsorship-cases.store'), $caseData + ['code' => 'gz-100'])
            ->assertSessionHasErrors('code');

        $this->actingAs($admin)->post(route('dashboard.sponsorship-cases.store'), $caseData + ['code' => 'gz-101'])
            ->assertRedirect(route('dashboard.sponsorship-cases.index'));

        $case = SponsorshipCase::where('code', 'GZ-101')->sole();
        $this->assertSame($region->id, $case->region_id);

        $this->actingAs($admin)->get(route('dashboard.sponsorship-cases.index', ['status' => 'available']))
            ->assertOk()
            ->assertSee('GZ-101');

        $this->actingAs($admin)->put(route('dashboard.sponsorship-cases.update', $case), array_merge($caseData, [
            'code' => 'GZ-101',
            'status' => SponsorshipCase::STATUS_SPONSORED,
        ]))->assertRedirect(route('dashboard.sponsorship-cases.index'));
        $this->assertSame(SponsorshipCase::STATUS_SPONSORED, $case->fresh()->status);

        $this->actingAs($admin)->delete(route('dashboard.sponsorship-cases.destroy', $case));
        $this->assertModelMissing($case);
    }

    public function test_sponsorship_case_photo_rejected_by_the_server_limit_shows_an_arabic_message(): void
    {
        $admin = User::factory()->create();
        $region = Region::factory()->create();
        $oversizedPhoto = new UploadedFile(UploadedFile::fake()->image('orphan.jpg')->getRealPath(), 'orphan.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true);

        $this->actingAs($admin)->post(route('dashboard.sponsorship-cases.store'), [
            'type' => 'orphan',
            'code' => 'GZ-200',
            'name_ar' => 'محمد',
            'region_id' => $region->id,
            'monthly_amount' => 50,
            'duration_months' => 12,
            'status' => SponsorshipCase::STATUS_AVAILABLE,
            'photo_file' => $oversizedPhoto,
        ])->assertSessionHasErrors(['photo_file' => __('validation.uploaded', ['attribute' => 'الصورة'])])
            ->assertSessionDoesntHaveErrors(['type', 'code', 'name_ar', 'monthly_amount', 'duration_months']);

        $this->assertStringStartsWith('تعذّر رفع الصورة', __('validation.uploaded', ['attribute' => 'الصورة']));
        $this->assertSame(0, SponsorshipCase::where('code', 'GZ-200')->count());
    }

    public function test_campaign_form_saves_program_region_and_preset_amounts(): void
    {
        $admin = User::factory()->create();
        $program = Program::factory()->create();
        $region = Region::factory()->create();

        $this->actingAs($admin)->get(route('dashboard.campaigns.create'))->assertOk();

        $this->actingAs($admin)->post(route('dashboard.campaigns.store'), [
            'title_ar' => 'سقيا الشمال',
            'key' => 'north-water',
            'program_id' => $program->id,
            'region_id' => $region->id,
            'preset_amounts' => '15, 30.5, 15, 60',
            'allows_monthly' => '0',
            'status' => 'published',
        ])->assertRedirect(route('dashboard.campaigns.index'));

        $campaign = Campaign::sole();
        $this->assertSame($program->id, $campaign->program_id);
        $this->assertSame($region->id, $campaign->region_id);
        $this->assertSame([15, 30.5, 60], $campaign->preset_amounts);
        $this->assertFalse($campaign->allows_monthly);

        $this->actingAs($admin)->put(route('dashboard.campaigns.update', $campaign), [
            'title_ar' => 'سقيا الشمال',
            'preset_amounts' => '10, abc',
            'status' => 'published',
        ])->assertSessionHasErrors('preset_amounts');
    }

    public function test_admin_can_configure_giving_tools(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->put(route('dashboard.settings.update'), [
            'whatsapp_number' => '+970 59 123 4567',
            'gold_price_per_gram' => '101.5',
            'quick_give' => [
                ['label_ar' => 'سقيا ماء', 'label_en' => 'Water', 'category' => 'sadaqah', 'amount' => '20'],
                ['label_ar' => '', 'label_en' => '', 'category' => 'general', 'amount' => ''],
            ],
        ])->assertRedirect(route('dashboard.settings.edit'));

        $this->assertSame('101.5', Setting::get('gold_price_per_gram'));
        $this->assertSame(
            [['category' => 'sadaqah', 'amount' => 20, 'label_ar' => 'سقيا ماء', 'label_en' => 'Water']],
            json_decode(Setting::get('quick_give_options'), true),
        );

        $this->get(route('contact'))->assertSee('https://wa.me/970591234567', false);

        $this->actingAs($admin)->put(route('dashboard.settings.update'), [
            'quick_give' => [['label_ar' => 'بدون مبلغ', 'category' => 'general', 'amount' => '']],
        ])->assertSessionHasErrors('quick_give.0.amount');
    }
}
