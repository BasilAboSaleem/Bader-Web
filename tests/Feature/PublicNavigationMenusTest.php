<?php

namespace Tests\Feature;

use App\Models\CompletedProject;
use App\Models\Region;
use App\Models\SponsorshipCase;
use App\Support\PublicNavigation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PublicNavigationMenusTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_menus_survive_a_cache_store_that_refuses_to_unserialize_objects(): void
    {
        config([
            'cache.stores.array.serialize' => true,
            'cache.serializable_classes' => false,
        ]);
        Cache::forgetDriver('array');

        $region = Region::factory()->create(['name_ar' => 'منطقة الشمال']);
        $project = CompletedProject::factory()->create(['region_id' => $region->id, 'title_ar' => 'بئر مياه']);
        $case = SponsorshipCase::factory()->create(['region_id' => $region->id]);

        PublicNavigation::menus();
        $menus = PublicNavigation::menus();

        $this->assertSame(1, $menus['regions']->find($region->id)?->projects_count);
        $this->assertSame($region->id, $menus['projects']->find($project->id)?->region?->id);
        $this->assertNotNull($menus['waitingCases']->find($case->id));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('منطقة الشمال')
            ->assertSee('بئر مياه');
    }

    public function test_saving_a_project_refreshes_the_cached_menu_counts(): void
    {
        $region = Region::factory()->create();
        $this->assertSame(0, PublicNavigation::menus()['regions']->find($region->id)?->projects_count);

        CompletedProject::factory()->create(['region_id' => $region->id]);

        $this->assertSame(1, PublicNavigation::menus()['regions']->find($region->id)?->projects_count);
    }
}
