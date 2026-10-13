<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeFacilitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_the_picked_published_facilities_right_after_the_regions_band(): void
    {
        $second = Facility::factory()->create(['name_ar' => 'مطبخ الشمال', 'order' => 2, 'show_on_home' => true]);
        $first = Facility::factory()->create(['name_ar' => 'نقطة مياه الوسطى', 'order' => 1, 'show_on_home' => true]);
        $notPicked = Facility::factory()->create(['name_ar' => 'مرفق غير مختار']);
        $draft = Facility::factory()->draft()->create(['name_ar' => 'مرفق مسودة', 'show_on_home' => true]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder([
                'aria-labelledby="regions-band-title"',
                'aria-labelledby="facilities-band-title"',
                route('facilities.show', $first->key),
                'نقطة مياه الوسطى',
                route('facilities.show', $second->key),
                'مطبخ الشمال',
                'aria-labelledby="programs-tabs-title"',
            ], false)
            ->assertDontSee($notPicked->name_ar)
            ->assertDontSee($draft->name_ar);
    }

    public function test_home_falls_back_to_the_first_published_facilities_when_none_are_picked(): void
    {
        $facilities = Facility::factory()->count(5)->sequence(fn ($sequence) => ['order' => $sequence->index])->create();

        $response = $this->get(route('home'))->assertOk();

        $facilities->take(Facility::HOME_LIMIT)->each(fn (Facility $facility) => $response->assertSee(route('facilities.show', $facility->key)));
        $response->assertDontSee(route('facilities.show', $facilities->last()->key));
    }

    public function test_facilities_section_lands_after_regions_for_a_previously_saved_section_order(): void
    {
        Facility::factory()->create(['show_on_home' => true]);
        Setting::set('home_sections', json_encode([
            ['key' => 'quick_give', 'visible' => true],
            ['key' => 'trust', 'visible' => true],
            ['key' => 'regions', 'visible' => true],
            ['key' => 'programs', 'visible' => true],
        ]));

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder([
                'aria-labelledby="trust-pillars-title"',
                'aria-labelledby="regions-band-title"',
                'aria-labelledby="facilities-band-title"',
                'aria-labelledby="programs-tabs-title"',
            ], false);
    }

    public function test_admin_picks_facilities_for_the_home_page_up_to_the_limit(): void
    {
        $admin = User::factory()->create();
        Facility::factory()->count(Facility::HOME_LIMIT - 1)->create(['show_on_home' => true]);
        $facility = Facility::factory()->create();

        $this->actingAs($admin)
            ->put(route('dashboard.facilities.update', $facility), $this->formData($facility, showOnHome: true))
            ->assertRedirect(route('dashboard.facilities.index'));
        $this->assertTrue($facility->fresh()->show_on_home);

        $this->actingAs($admin)
            ->post(route('dashboard.facilities.store'), [
                'name_ar' => 'مرفق خامس',
                'status' => 'published',
                'show_on_home' => '1',
            ])
            ->assertSessionHasErrors('show_on_home');
        $this->assertDatabaseMissing('facilities', ['name_ar' => 'مرفق خامس']);

        $this->actingAs($admin)
            ->put(route('dashboard.facilities.update', $facility), $this->formData($facility, showOnHome: false))
            ->assertRedirect(route('dashboard.facilities.index'));
        $this->assertFalse($facility->fresh()->show_on_home);
    }

    /**
     * @return array<string, string>
     */
    private function formData(Facility $facility, bool $showOnHome): array
    {
        return [
            'name_ar' => $facility->name_ar,
            'key' => $facility->key,
            'status' => 'published',
            'show_on_home' => $showOnHome ? '1' : '0',
        ];
    }
}
