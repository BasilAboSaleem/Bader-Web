<?php

namespace Tests\Unit;

use App\Support\VideoEmbed;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VideoEmbedTest extends TestCase
{
    /**
     * @return array<string, array{string, string, bool}>
     */
    public static function youtubeLinks(): array
    {
        return [
            'watch' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10s', 'dQw4w9WgXcQ', false],
            'short link' => ['https://youtu.be/dQw4w9WgXcQ?si=abc', 'dQw4w9WgXcQ', false],
            'mobile' => ['https://m.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', false],
            'embed' => ['https://www.youtube.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ', false],
            'live' => ['https://www.youtube.com/live/dQw4w9WgXcQ', 'dQw4w9WgXcQ', false],
            'shorts' => ['https://youtube.com/shorts/dQw4w9WgXcQ', 'dQw4w9WgXcQ', true],
        ];
    }

    #[DataProvider('youtubeLinks')]
    public function test_youtube_links_become_privacy_enhanced_embeds(string $url, string $videoId, bool $isVertical): void
    {
        $embed = VideoEmbed::fromUrl($url);

        $this->assertSame(VideoEmbed::PROVIDER_YOUTUBE, $embed['provider']);
        $this->assertSame('https://www.youtube-nocookie.com/embed/'.$videoId, $embed['embed_url']);
        $this->assertSame($isVertical, $embed['is_vertical']);
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function facebookLinks(): array
    {
        return [
            'page video' => ['https://www.facebook.com/BaderOrg/videos/1234567890/', false],
            'watch' => ['https://www.facebook.com/watch/?v=1234567890', false],
            'fb.watch' => ['https://fb.watch/abcDEF123/', false],
            'reel' => ['https://www.facebook.com/reel/1234567890', true],
            'shared reel' => ['https://www.facebook.com/share/r/AbCdEf/', true],
        ];
    }

    #[DataProvider('facebookLinks')]
    public function test_facebook_video_links_use_the_video_plugin(string $url, bool $isVertical): void
    {
        $embed = VideoEmbed::fromUrl($url);

        $this->assertSame(VideoEmbed::PROVIDER_FACEBOOK, $embed['provider']);
        $this->assertSame('https://www.facebook.com/plugins/video.php?href='.urlencode($url).'&show_text=false&width=1280', $embed['embed_url']);
        $this->assertSame($isVertical, $embed['is_vertical']);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function unsupportedLinks(): array
    {
        return [
            'empty' => [''],
            'not a url' => ['youtube video'],
            'javascript scheme' => ['javascript:alert(1)//youtube.com/watch?v=dQw4w9WgXcQ'],
            'look-alike host' => ['https://youtube.com.evil.example/watch?v=dQw4w9WgXcQ'],
            'bad youtube id' => ['https://www.youtube.com/watch?v=short'],
            'youtube channel' => ['https://www.youtube.com/@BaderOrg'],
            'facebook post' => ['https://www.facebook.com/BaderOrg/posts/123'],
            'other site' => ['https://vimeo.com/123456'],
        ];
    }

    #[DataProvider('unsupportedLinks')]
    public function test_unsupported_links_are_rejected(string $url): void
    {
        $this->assertNull(VideoEmbed::fromUrl($url));
    }

    public function test_list_keeps_only_supported_links_in_order(): void
    {
        $embeds = VideoEmbed::fromList(['https://vimeo.com/1', 'https://youtu.be/dQw4w9WgXcQ', null, 'https://fb.watch/abc/']);

        $this->assertSame([VideoEmbed::PROVIDER_YOUTUBE, VideoEmbed::PROVIDER_FACEBOOK], array_column($embeds, 'provider'));
    }
}
