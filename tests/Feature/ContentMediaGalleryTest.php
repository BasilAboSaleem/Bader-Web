<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Program;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentMediaGalleryTest extends TestCase
{
    use RefreshDatabase;

    private const YOUTUBE_LINK = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

    private const FACEBOOK_LINK = 'https://www.facebook.com/BaderOrg/videos/1234567890/';

    public function test_news_item_gets_photos_and_videos_and_can_drop_a_photo_later(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $program = Program::factory()->create();

        $this->actingAs($admin)->post(route('dashboard.stories.store'), [
            'title_ar' => 'توزيع طرود غذائية',
            'key' => 'food-parcels',
            'program_id' => $program->id,
            'status' => 'published',
            'gallery_files' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.jpg')],
            'videos' => self::YOUTUBE_LINK."\n\n  ".self::FACEBOOK_LINK."  \n".self::YOUTUBE_LINK,
        ])->assertRedirect(route('dashboard.stories.index'));

        $story = Story::where('key', 'food-parcels')->firstOrFail();
        $this->assertSame($program->id, $story->program_id);
        $this->assertCount(2, $story->gallery);
        $this->assertSame([self::YOUTUBE_LINK, self::FACEBOOK_LINK], $story->videos);
        foreach ($story->gallery as $photo) {
            $this->assertStringStartsWith('storage/stories/gallery/', $photo);
            Storage::disk('public')->assertExists(substr($photo, strlen('storage/')));
        }

        [$removedPhoto, $keptPhoto] = $story->gallery;

        $this->actingAs($admin)->put(route('dashboard.stories.update', $story), [
            'title_ar' => 'توزيع طرود غذائية',
            'key' => 'food-parcels',
            'program_id' => '',
            'status' => 'published',
            'gallery_remove' => [$removedPhoto],
            'gallery_files' => [UploadedFile::fake()->image('three.jpg')],
            'videos' => '',
        ])->assertRedirect(route('dashboard.stories.index'));

        $story->refresh();
        $this->assertSame($keptPhoto, $story->gallery[0]);
        $this->assertCount(2, $story->gallery);
        $this->assertNull($story->videos);
        $this->assertNull($story->program_id);
        Storage::disk('public')->assertMissing(substr($removedPhoto, strlen('storage/')));
    }

    public function test_removing_a_photo_never_deletes_files_outside_the_gallery_folder(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('hero/main.jpg', 'image');
        $program = Program::factory()->create(['gallery' => ['storage/hero/main.jpg']]);

        $this->actingAs(User::factory()->create())->put(route('dashboard.programs.update', $program), [
            'title_ar' => $program->title_ar,
            'status' => 'published',
            'gallery_remove' => ['storage/hero/main.jpg'],
        ])->assertRedirect(route('dashboard.programs.index'));

        $this->assertNull($program->fresh()->gallery);
        Storage::disk('public')->assertExists('hero/main.jpg');
    }

    public function test_unsupported_or_too_many_video_links_are_rejected(): void
    {
        $admin = User::factory()->create();
        $campaign = Campaign::factory()->create();
        $payload = ['title_ar' => $campaign->title_ar, 'status' => 'published'];

        $this->actingAs($admin)->put(route('dashboard.campaigns.update', $campaign), [...$payload, 'videos' => 'https://vimeo.com/123'])
            ->assertSessionHasErrors('videos');

        $tooMany = implode("\n", array_map(fn (int $number): string => 'https://youtu.be/dQw4w9WgX'.sprintf('%02d', $number), range(1, 13)));
        $this->actingAs($admin)->put(route('dashboard.campaigns.update', $campaign), [...$payload, 'videos' => $tooMany])
            ->assertSessionHasErrors('videos');

        $this->assertNull($campaign->fresh()->videos);
    }

    public function test_program_page_shows_its_media_and_only_its_own_published_news(): void
    {
        $program = Program::factory()->create([
            'gallery' => ['images/programs/water.jpg'],
            'videos' => [self::YOUTUBE_LINK],
        ]);
        $ownNews = Story::create(['key' => 'own', 'title_ar' => 'خبر البرنامج', 'program_id' => $program->id, 'published_at' => now(), 'status' => 'published']);
        $draftNews = Story::create(['key' => 'draft', 'title_ar' => 'خبر مسودة', 'program_id' => $program->id, 'published_at' => now(), 'status' => 'draft']);
        $otherNews = Story::create(['key' => 'other', 'title_ar' => 'خبر آخر', 'published_at' => now(), 'status' => 'published']);

        $this->get(route('programs.show', $program->key))
            ->assertOk()
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false)
            ->assertSee('data-lightbox="gallery-Program-'.$program->id.'"', false)
            ->assertSee($ownNews->title_ar)
            ->assertDontSee($draftNews->title_ar)
            ->assertDontSee($otherNews->title_ar);
    }

    public function test_news_page_links_its_program_and_shows_the_gallery_with_hidden_extra_photos(): void
    {
        $program = Program::factory()->create();
        $story = Story::create([
            'key' => 'with-media',
            'title_ar' => 'خبر بالصور',
            'program_id' => $program->id,
            'published_at' => now(),
            'status' => 'published',
            'gallery' => array_map(fn (int $number): string => "images/news/{$number}.jpg", range(1, 10)),
            'videos' => [self::FACEBOOK_LINK],
        ]);

        $this->get(route('news.show', $story->key))
            ->assertOk()
            ->assertSee(route('programs.show', $program->key), false)
            ->assertSee('facebook.com/plugins/video.php', false)
            ->assertSee('data-gallery-more', false)
            ->assertSee('hidden data-gallery-extra', false);
    }

    public function test_a_draft_program_is_not_linked_from_its_news(): void
    {
        $program = Program::factory()->create(['status' => 'draft']);
        $story = Story::create(['key' => 'news', 'title_ar' => 'خبر', 'program_id' => $program->id, 'published_at' => now(), 'status' => 'published']);

        $this->get(route('news.show', $story->key))
            ->assertOk()
            ->assertDontSee(route('programs.show', $program->key), false);
    }

    public function test_project_page_shows_its_media(): void
    {
        $campaign = Campaign::factory()->create(['videos' => ['https://youtube.com/shorts/dQw4w9WgXcQ']]);

        $this->get(route('campaigns.show', $campaign->key))
            ->assertOk()
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false)
            ->assertSee('aspect-[9/16]', false);
    }
}
