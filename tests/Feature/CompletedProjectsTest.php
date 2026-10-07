<?php

namespace Tests\Feature;

use App\Models\CompletedProject;
use App\Models\Program;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompletedProjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_open_the_completed_projects_module(): void
    {
        $completedProject = CompletedProject::factory()->create();

        $this->get(route('dashboard.completed-projects.index'))->assertRedirect(route('login'));
        $this->post(route('dashboard.completed-projects.store'), ['title_ar' => 'x', 'status' => 'published'])->assertRedirect(route('login'));
        $this->delete(route('dashboard.completed-projects.destroy', $completedProject))->assertRedirect(route('login'));
        $this->assertModelExists($completedProject);
    }

    public function test_admin_can_add_edit_and_delete_a_completed_project(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $region = Region::factory()->create();
        $program = Program::factory()->create();

        $this->actingAs($admin)->get(route('dashboard.completed-projects.create'))->assertOk();

        $this->actingAs($admin)->post(route('dashboard.completed-projects.store'), [
            'title_ar' => 'حفر بئر مياه في دير البلح',
            'title_en' => 'Water Well in Deir al-Balah',
            'region_id' => $region->id,
            'program_id' => $program->id,
            'completed_at' => '2026-06-15',
            'beneficiaries' => 1200,
            'cost' => 8500.50,
            'status' => 'published',
            'gallery_files' => [UploadedFile::fake()->image('well.jpg')],
            'videos' => 'https://youtu.be/dQw4w9WgXcQ',
        ])->assertRedirect(route('dashboard.completed-projects.index'));

        $completedProject = CompletedProject::firstOrFail();
        $this->assertSame('water-well-in-deir-al-balah', $completedProject->key);
        $this->assertSame($region->id, $completedProject->region_id);
        $this->assertSame($program->id, $completedProject->program_id);
        $this->assertSame('2026-06-15', $completedProject->completed_at->toDateString());
        $this->assertSame(1200, $completedProject->beneficiaries);
        $this->assertSame('8500.50', $completedProject->cost);
        $this->assertStringStartsWith('storage/completed-projects/gallery/', $completedProject->gallery[0]);
        $this->assertSame(['https://youtu.be/dQw4w9WgXcQ'], $completedProject->videos);

        $this->actingAs($admin)->get(route('dashboard.completed-projects.index'))->assertOk()->assertSee('حفر بئر مياه في دير البلح');
        $this->actingAs($admin)->get(route('dashboard.completed-projects.edit', $completedProject))->assertOk();

        $this->actingAs($admin)->put(route('dashboard.completed-projects.update', $completedProject), [
            'title_ar' => 'حفر بئر مياه',
            'region_id' => '',
            'completed_at' => '',
            'beneficiaries' => '',
            'status' => 'draft',
        ])->assertRedirect(route('dashboard.completed-projects.index'));

        $completedProject->refresh();
        $this->assertSame('water-well-in-deir-al-balah', $completedProject->key);
        $this->assertNull($completedProject->region_id);
        $this->assertNull($completedProject->completed_at);
        $this->assertNull($completedProject->beneficiaries);
        $this->assertSame('draft', $completedProject->status);
        $this->assertCount(1, $completedProject->gallery);

        $this->actingAs($admin)->delete(route('dashboard.completed-projects.destroy', $completedProject))
            ->assertRedirect(route('dashboard.completed-projects.index'));
        $this->assertModelMissing($completedProject);
    }

    public function test_completed_project_input_is_validated(): void
    {
        $existing = CompletedProject::factory()->create(['key' => 'taken-key']);

        $this->actingAs(User::factory()->create())->post(route('dashboard.completed-projects.store'), [
            'title_ar' => '',
            'key' => 'taken-key',
            'region_id' => 999,
            'completed_at' => now()->addDay()->toDateString(),
            'beneficiaries' => -5,
            'cost' => 'free',
            'status' => 'archived',
        ])->assertSessionHasErrors(['title_ar', 'key', 'region_id', 'completed_at', 'beneficiaries', 'cost', 'status']);

        $this->assertSame(1, CompletedProject::count());
        $this->assertModelExists($existing);
    }

    public function test_public_list_shows_only_published_projects_and_filters_by_region(): void
    {
        $north = Region::factory()->create();
        $inNorth = CompletedProject::factory()->create(['region_id' => $north->id, 'beneficiaries' => 300]);
        $elsewhere = CompletedProject::factory()->create(['beneficiaries' => 200]);
        $draft = CompletedProject::factory()->draft()->create(['region_id' => $north->id]);

        $this->get(route('projects'))
            ->assertOk()
            ->assertSee($inNorth->title_ar)
            ->assertSee($elsewhere->title_ar)
            ->assertDontSee($draft->title_ar)
            ->assertViewHas('totals', fn ($totals): bool => (int) $totals->projects_count === 2 && (int) $totals->beneficiaries_total === 500);

        $this->get(route('projects', ['region' => $north->key]))
            ->assertOk()
            ->assertViewHas('completedProjects', fn ($projects): bool => $projects->pluck('id')->all() === [$inNorth->id]);
    }

    public function test_old_completed_projects_links_redirect_permanently_to_projects(): void
    {
        $completedProject = CompletedProject::factory()->create();

        $this->get('/completed-projects?region=north')
            ->assertStatus(301)
            ->assertRedirect(route('projects', ['region' => 'north']));

        $this->get('/completed-projects/'.$completedProject->key)
            ->assertStatus(301)
            ->assertRedirect(route('projects.show', $completedProject->key));
    }

    public function test_detail_page_shows_the_delivery_facts_without_a_donation_target(): void
    {
        $region = Region::factory()->create();
        $completedProject = CompletedProject::factory()->create([
            'region_id' => $region->id,
            'completed_at' => '2026-03-10',
            'beneficiaries' => 4500,
            'cost' => 12000,
            'content_ar' => 'تفاصيل تنفيذ المشروع على الأرض',
        ]);

        $this->get(route('projects.show', $completedProject->key))
            ->assertOk()
            ->assertSee($completedProject->title_ar)
            ->assertSee('4,500')
            ->assertSee('$12,000')
            ->assertSee('تفاصيل تنفيذ المشروع على الأرض')
            ->assertSee(route('regions.show', $region->key), false)
            ->assertDontSee('target_type=', false);

        $this->withSession(['locale' => 'en'])
            ->get(route('projects.show', $completedProject->key))
            ->assertOk()
            ->assertSee($completedProject->title_en);
    }

    public function test_draft_completed_project_page_is_not_found(): void
    {
        $completedProject = CompletedProject::factory()->draft()->create();

        $this->get(route('projects.show', $completedProject->key))->assertNotFound();
    }

    public function test_impact_map_pins_and_counts_published_projects_per_region(): void
    {
        $region = Region::factory()->create();
        $published = CompletedProject::factory()->count(2)->create(['region_id' => $region->id]);
        $draft = CompletedProject::factory()->draft()->create(['region_id' => $region->id]);

        $response = $this->get(route('impact-map'))->assertOk();

        foreach ($published as $completedProject) {
            $response->assertSee('href="'.route('projects.show', $completedProject->key).'"', false);
        }
        $response
            ->assertSee('region-facility is-project', false)
            ->assertSee(trans_choice('region.projects_count', 2, ['count' => 2]))
            ->assertDontSee(route('projects.show', $draft->key), false);
    }

    public function test_region_and_program_pages_list_their_completed_projects(): void
    {
        $region = Region::factory()->create();
        $program = Program::factory()->create();
        $completedProject = CompletedProject::factory()->create(['region_id' => $region->id, 'program_id' => $program->id]);
        CompletedProject::factory()->create();

        $listsOnlyTheirProject = fn ($projects): bool => $projects->pluck('id')->all() === [$completedProject->id];

        $this->get(route('regions.show', $region->key))
            ->assertOk()
            ->assertSee($completedProject->title_ar)
            ->assertViewHas('completedProjects', $listsOnlyTheirProject);

        $this->get(route('programs.show', $program->key))
            ->assertOk()
            ->assertSee($completedProject->title_ar)
            ->assertViewHas('completedProjects', $listsOnlyTheirProject);
    }
}
