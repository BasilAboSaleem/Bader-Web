<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Facility;
use App\Models\Program;
use App\Models\Region;
use App\Models\SponsorshipCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProjectsPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_program_page_lists_its_published_projects_with_a_program_donate_box(): void
    {
        $program = Program::factory()->create();
        $publishedCampaign = Campaign::factory()->create(['program_id' => $program->id]);
        $draftCampaign = Campaign::factory()->draft()->create(['program_id' => $program->id]);

        $this->get(route('programs.show', $program->key))
            ->assertOk()
            ->assertSee($program->title_ar)
            ->assertSee($publishedCampaign->title_ar)
            ->assertViewHas('campaigns', fn ($campaigns): bool => ! $campaigns->contains($draftCampaign))
            ->assertSee('target_type=program&amp;target_id='.$program->id, false);
    }

    public function test_draft_program_and_campaign_pages_are_not_found(): void
    {
        $program = Program::factory()->create(['status' => 'draft']);
        $campaign = Campaign::factory()->draft()->create();

        $this->get(route('programs.show', $program->key))->assertNotFound();
        $this->get(route('campaigns.show', $campaign->key))->assertNotFound();
    }

    public function test_projects_page_filters_by_program_and_region(): void
    {
        $water = Program::factory()->create();
        $north = Region::factory()->create();
        $matching = Campaign::factory()->create(['program_id' => $water->id, 'region_id' => $north->id]);
        $otherProgram = Campaign::factory()->create(['region_id' => $north->id]);
        $otherRegion = Campaign::factory()->create(['program_id' => $water->id]);

        $this->get(route('campaigns', ['program' => $water->key, 'region' => $north->key]))
            ->assertOk()
            ->assertSee($matching->title_ar)
            ->assertViewHas('campaigns', fn ($campaigns): bool => $campaigns->pluck('id')->all() === [$matching->id]);

        $this->get(route('campaigns', ['program' => 'unknown-program']))
            ->assertOk()
            ->assertViewHas('campaigns', fn ($campaigns): bool => $campaigns->pluck('id')->sort()->values()->all() === [$matching->id, $otherProgram->id, $otherRegion->id]);
    }

    public function test_campaign_page_shows_progress_and_the_campaign_amounts_in_both_languages(): void
    {
        $campaign = Campaign::factory()->create([
            'goal_amount' => 10000,
            'raised_amount' => 2500,
            'preset_amounts' => [15, 30, 60],
            'allows_monthly' => true,
            'content_ar' => 'تفاصيل التنفيذ في الميدان',
            'content_en' => 'Field delivery details',
        ]);

        $this->get(route('campaigns.show', $campaign->key))
            ->assertOk()
            ->assertSee('25%')
            ->assertSee('$2,500')
            ->assertSee('data-amount="60"', false)
            ->assertSee('data-frequency-option', false)
            ->assertSee('تفاصيل التنفيذ في الميدان');

        $this->withSession(['locale' => 'en'])
            ->get(route('campaigns.show', $campaign->key))
            ->assertOk()
            ->assertSee($campaign->title_en)
            ->assertSee('Field delivery details')
            ->assertDontSee('campaign_page.', false)
            ->assertDontSee('donate_box.', false);
    }

    public function test_region_page_shows_its_projects_and_waiting_cases(): void
    {
        $region = Region::factory()->create();
        $campaign = Campaign::factory()->create(['region_id' => $region->id]);
        $case = SponsorshipCase::factory()->create(['region_id' => $region->id]);
        $sponsoredCase = SponsorshipCase::factory()->sponsored()->create(['region_id' => $region->id]);

        $this->get(route('regions.show', $region->key))
            ->assertOk()
            ->assertSee($region->name_ar)
            ->assertSee($campaign->title_ar)
            ->assertSee($case->name_ar)
            ->assertViewHas('cases', fn ($cases): bool => ! $cases->contains($sponsoredCase));
    }

    public function test_facility_page_offers_a_facility_donation_and_links_back_to_its_region(): void
    {
        $region = Region::factory()->create();
        $facility = Facility::factory()->create([
            'region_id' => $region->id,
            'content_ar' => 'تفاصيل تشغيل المرفق',
            'gallery' => ['images/programs/water.jpg', 'images/programs/education.jpg'],
        ]);
        $draftFacility = Facility::factory()->draft()->create();

        $this->get(route('facilities.show', $facility->key))
            ->assertOk()
            ->assertSee($facility->name_ar)
            ->assertSee('تفاصيل تشغيل المرفق')
            ->assertSee('target_type=facility&amp;target_id='.$facility->id, false)
            ->assertSee(route('regions.show', $region->key), false)
            ->assertSee('data-lightbox', false);

        $this->withSession(['locale' => 'en'])
            ->get(route('facilities.show', $facility->key))
            ->assertOk()
            ->assertSee($facility->name_en)
            ->assertDontSee('facility_page.', false);

        $this->get(route('facilities.show', $draftFacility->key))->assertNotFound();
    }

    public function test_sitemap_lists_program_project_and_region_detail_pages(): void
    {
        $program = Program::factory()->create();
        $campaign = Campaign::factory()->create();
        $region = Region::factory()->create();
        $draft = Campaign::factory()->draft()->create();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('programs.show', $program->key), false)
            ->assertSee(route('campaigns.show', $campaign->key), false)
            ->assertSee(route('regions.show', $region->key), false)
            ->assertDontSee(route('campaigns.show', $draft->key), false);
    }
}
