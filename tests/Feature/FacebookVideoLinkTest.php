<?php

namespace Tests\Feature;

use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FacebookVideoLinkTest extends TestCase
{
    use RefreshDatabase;

    private const SHARE_LINK = 'https://www.facebook.com/share/v/1FXvWGzN3D/';

    private const REEL_LINK = 'https://www.facebook.com/reel/1352189110325583/';

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    public function test_saving_a_facebook_share_link_stores_the_playable_video_address(): void
    {
        Http::fake([self::SHARE_LINK => Http::response('', 302, ['Location' => self::REEL_LINK.'?rdid=abc&share_url=x'])]);

        $this->actingAs(User::factory()->create())->post(route('dashboard.stories.store'), [
            'title_ar' => 'حقيبة جديدة',
            'key' => 'new-bag',
            'status' => 'published',
            'videos' => self::SHARE_LINK,
        ])->assertRedirect(route('dashboard.stories.index'));

        $this->assertSame([self::REEL_LINK], Story::where('key', 'new-bag')->sole()->videos);
    }

    public function test_share_links_saved_earlier_play_through_the_resolved_address_looked_up_once(): void
    {
        Http::fake([self::SHARE_LINK => Http::response('', 302, ['Location' => self::REEL_LINK.'?rdid=abc'])]);
        $story = $this->publishedStoryWithVideo(self::SHARE_LINK);

        $this->get(route('news.show', $story->key))->assertOk()->assertSee(urlencode(self::REEL_LINK), false);
        $this->get(route('news.show', $story->key))->assertOk()->assertDontSee(urlencode(self::SHARE_LINK), false);

        Http::assertSentCount(1);
    }

    public function test_page_still_renders_when_facebook_cannot_resolve_the_link(): void
    {
        Http::fake([self::SHARE_LINK => Http::response('', 500)]);
        $story = $this->publishedStoryWithVideo(self::SHARE_LINK);

        $this->get(route('news.show', $story->key))->assertOk()->assertSee(urlencode(self::SHARE_LINK), false);
        $this->get(route('news.show', $story->key))->assertOk();

        Http::assertSentCount(1);
    }

    private function publishedStoryWithVideo(string $videoUrl): Story
    {
        return Story::create([
            'key' => 'rafah-bag',
            'title_ar' => 'من رفح',
            'published_at' => now()->toDateString(),
            'status' => 'published',
            'videos' => [$videoUrl],
        ]);
    }
}
