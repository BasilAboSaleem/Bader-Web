<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\Story;
use Database\Seeders\FacebookNewsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FacebookNewsSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_imports_facebook_news_as_drafts_with_their_media_and_programs(): void
    {
        $education = Program::factory()->create(['key' => 'education']);

        $this->seed(FacebookNewsSeeder::class);

        $this->assertSame(12, Story::count());
        $this->assertSame(12, Story::where('status', 'draft')->count());

        $story = Story::where('key', 'university-fees-first-batch-ptc')->sole();
        $this->assertSame($education->id, $story->program_id);
        $this->assertSame('storage/stories/facebook/university-fees-first-batch-ptc.jpg', $story->image);
        $this->assertCount(8, $story->galleryPhotos());
        $this->assertSame(['https://www.facebook.com/bader.gaza/videos/1101666599484346/'], $story->videos);
        $this->assertCount(1, $story->videoEmbeds());

        Storage::disk('public')->assertExists('stories/facebook/university-fees-first-batch-ptc.jpg');
        Storage::disk('public')->assertExists('stories/facebook/university-fees-first-batch-ptc-8.jpg');
    }

    public function test_running_again_refreshes_content_without_duplicates_or_unpublishing(): void
    {
        $this->seed(FacebookNewsSeeder::class);

        $story = Story::where('key', 'bader-desalination-plant')->sole();
        $story->update([
            'status' => 'published',
            'is_featured' => true,
            'title_ar' => 'عنوان معدّل',
            'gallery' => ['storage/stories/gallery/extra.jpg'],
        ]);

        $this->seed(FacebookNewsSeeder::class);

        $this->assertSame(12, Story::count());

        $story->refresh();
        $this->assertSame('published', $story->status);
        $this->assertTrue($story->is_featured);
        $this->assertSame('محطة بادر لتحلية المياه تخدم آلاف النازحين في مخيمات غزة', $story->title_ar);
        $this->assertSame(['storage/stories/gallery/extra.jpg'], $story->galleryPhotos());
    }
}
