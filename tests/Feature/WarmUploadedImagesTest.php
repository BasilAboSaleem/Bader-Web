<?php

namespace Tests\Feature;

use App\Jobs\WarmRecentImageVariants;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WarmUploadedImagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_photos_uploaded_from_the_dashboard_get_their_resized_copies_after_the_response(): void
    {
        Storage::fake('public');
        Bus::fake();

        $this->actingAs(User::factory()->create())
            ->post(route('dashboard.facilities.store'), [
                'name_ar' => 'مرفق بصورة',
                'status' => 'published',
                'image_file' => UploadedFile::fake()->image('facility.jpg', 1600, 900),
            ])
            ->assertRedirect(route('dashboard.facilities.index'));

        Bus::assertDispatchedAfterResponse(WarmRecentImageVariants::class);
    }

    public function test_saving_without_files_does_not_generate_copies(): void
    {
        Bus::fake();
        $facility = Facility::factory()->create();

        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.facilities.update', $facility), [
                'name_ar' => $facility->name_ar,
                'key' => $facility->key,
                'status' => 'published',
            ])
            ->assertRedirect(route('dashboard.facilities.index'));

        Bus::assertNothingDispatched();
    }
}
