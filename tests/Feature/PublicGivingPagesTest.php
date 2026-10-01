<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\SponsorshipCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicGivingPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sponsorship_page_lists_waiting_cases_and_filters_by_type(): void
    {
        $orphan = SponsorshipCase::factory()->create(['type' => 'orphan']);
        $widow = SponsorshipCase::factory()->create(['type' => 'widow']);
        SponsorshipCase::factory()->hidden()->create();

        $this->get(route('sponsorship'))
            ->assertOk()
            ->assertSee($orphan->name_ar)
            ->assertSee($widow->name_ar)
            ->assertSee(route('sponsorship.submit'), false)
            ->assertViewHas('cases', fn ($cases): bool => $cases->count() === 2);

        $this->get(route('sponsorship', ['type' => 'widow']))
            ->assertOk()
            ->assertViewHas('cases', fn ($cases): bool => $cases->pluck('id')->all() === [$widow->id]);
    }

    public function test_case_page_offers_monthly_sponsorship_only_while_the_case_is_available(): void
    {
        $case = SponsorshipCase::factory()->create(['monthly_amount' => 45]);
        $sponsored = SponsorshipCase::factory()->sponsored()->create();
        $hidden = SponsorshipCase::factory()->hidden()->create();

        $this->get(route('sponsorship.show', $case->code))
            ->assertOk()
            ->assertSee($case->name_ar)
            ->assertSee('target_type=sponsorship&amp;target_id='.$case->id, false)
            ->assertSee('data-frequency="monthly"', false);

        $this->get(route('sponsorship.show', $sponsored->code))
            ->assertOk()
            ->assertSee(__('sponsorship_page.already_sponsored'))
            ->assertDontSee('target_type=sponsorship&amp;target_id='.$sponsored->id, false);

        $this->get(route('sponsorship.show', $hidden->code))->assertNotFound();
    }

    public function test_gift_and_zakat_pages_render_in_both_languages(): void
    {
        foreach (['ar', 'en'] as $locale) {
            $this->withSession(['locale' => $locale])
                ->get(route('gift'))
                ->assertOk()
                ->assertSee('name="gift_recipient_name"', false)
                ->assertSee('data-gift-design', false)
                ->assertDontSee('gift_page.', false);

            $this->withSession(['locale' => $locale])
                ->get(route('zakat'))
                ->assertOk()
                ->assertSee('data-zakat', false)
                ->assertSee('data-gold-price="95"', false)
                ->assertDontSee('zakat_page.', false);
        }
    }

    public function test_donate_page_is_prefilled_from_the_query_string(): void
    {
        $campaign = Campaign::factory()->create(['allows_monthly' => true, 'preset_amounts' => [20, 40, 80]]);

        $this->get(route('donate', [
            'target_type' => 'campaign',
            'target_id' => $campaign->id,
            'amount' => 40,
            'frequency' => 'monthly',
            'category' => 'zakat',
        ]))
            ->assertOk()
            ->assertSee($campaign->title_ar)
            ->assertSee('name="target_type" value="campaign"', false)
            ->assertSee('name="target_id" value="'.$campaign->id.'"', false)
            ->assertSee('name="amount" value="40"', false)
            ->assertSee('name="frequency" value="monthly"', false)
            ->assertSee('value="zakat" class="peer sr-only" checked', false);
    }

    public function test_donate_page_ignores_invalid_prefill_values(): void
    {
        $campaign = Campaign::factory()->create(['allows_monthly' => false]);
        $draft = Campaign::factory()->draft()->create();

        $this->get(route('donate', ['target_type' => 'campaign', 'target_id' => $campaign->id, 'frequency' => 'monthly', 'amount' => '-5']))
            ->assertOk()
            ->assertSee('name="frequency" value="once"', false)
            ->assertDontSee('data-frequency-option', false);

        $this->get(route('donate', ['target_type' => 'campaign', 'target_id' => $draft->id, 'category' => 'unknown']))
            ->assertOk()
            ->assertSee('name="target_type" value="general"', false)
            ->assertDontSee('name="target_id"', false)
            ->assertSee('value="general" class="peer sr-only" checked', false);
    }

    public function test_donate_page_prefills_gift_details_from_the_gift_page(): void
    {
        $this->get(route('donate', [
            'gift' => 1,
            'gift_design' => 'mother',
            'gift_recipient_name' => 'Mariam',
            'gift_message' => 'With love',
        ]))
            ->assertOk()
            ->assertSee('name="is_gift" value="1" class="toggle-switch" checked', false)
            ->assertSee('value="mother" class="sr-only"', false)
            ->assertSee('value="Mariam"', false)
            ->assertSee('With love');
    }

    public function test_success_page_shows_a_localized_receipt_with_the_gift_card(): void
    {
        $donation = Donation::create([
            'amount' => 75,
            'frequency' => Donation::FREQUENCY_ONCE,
            'target_type' => 'general',
            'donation_category' => 'sadaqah',
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
            'is_gift' => true,
            'gift_recipient_name' => 'Mariam',
            'gift_card_design' => 'eid_fitr',
        ]);

        $this->withSession(['locale' => 'en'])
            ->get(route('donate.success', ['receipt' => $donation->receipt_number]))
            ->assertOk()
            ->assertSee($donation->receipt_number)
            ->assertSee('$75')
            ->assertSee(__('donate_success.pending_title', [], 'en'))
            ->assertSee('Eid al-Fitr gift')
            ->assertSee('Mariam')
            ->assertDontSee('donate_success.', false);

        $this->get(route('donate.success', ['receipt' => 'BDR-MISSING']))
            ->assertOk()
            ->assertSee(__('donate_success.not_found_title'));
    }
}
