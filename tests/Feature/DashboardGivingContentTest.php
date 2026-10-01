<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Program;
use App\Models\Region;
use App\Models\Setting;
use App\Models\SponsorshipCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            'status' => 'published',
        ])->assertRedirect(route('dashboard.regions.index'));

        $region = Region::sole();
        $this->assertSame(0, $region->order);
        $this->get(route('contact'))->assertSee('منطقة المواصي');

        $this->actingAs($admin)->put(route('dashboard.regions.update', $region), [
            'name_ar' => 'منطقة المواصي',
            'key' => 'mawasi',
            'map_x' => 120,
            'map_y' => 75,
            'status' => 'draft',
        ])->assertSessionHasErrors('map_x');

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
            'whatsapp_number' => '+968 9123 4567',
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

        $this->get(route('contact'))->assertSee('https://wa.me/96891234567', false);

        $this->actingAs($admin)->put(route('dashboard.settings.update'), [
            'quick_give' => [['label_ar' => 'بدون مبلغ', 'category' => 'general', 'amount' => '']],
        ])->assertSessionHasErrors('quick_give.0.amount');
    }
}
