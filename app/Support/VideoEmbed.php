<?php

namespace App\Support;

final class VideoEmbed
{
    public const PROVIDER_YOUTUBE = 'youtube';

    public const PROVIDER_FACEBOOK = 'facebook';

    private const YOUTUBE_HOSTS = ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'music.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'];

    private const FACEBOOK_HOSTS = ['facebook.com', 'www.facebook.com', 'm.facebook.com', 'web.facebook.com', 'fb.watch', 'www.fb.watch'];

    /**
     * Turn a public YouTube or Facebook video link into an embeddable player URL.
     *
     * @return array{provider: string, url: string, embed_url: string, is_vertical: bool}|null
     */
    public static function fromUrl(?string $url): ?array
    {
        $url = trim((string) $url);
        $parts = parse_url($url);

        if ($url === '' || $parts === false || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';

        if ($host === 'youtu.be' || $host === 'www.youtu.be') {
            return self::youtube($url, trim($path, '/'), false);
        }

        if (in_array($host, self::YOUTUBE_HOSTS, true)) {
            parse_str($parts['query'] ?? '', $query);

            if (rtrim($path, '/') === '/watch') {
                return self::youtube($url, is_string($query['v'] ?? null) ? $query['v'] : '', false);
            }

            if (preg_match('#^/(embed|shorts|live|v)/([^/?]+)#', $path, $matches) === 1) {
                return self::youtube($url, $matches[2], $matches[1] === 'shorts');
            }

            return null;
        }

        if (in_array($host, self::FACEBOOK_HOSTS, true)) {
            $isFbWatch = str_ends_with($host, 'fb.watch');
            $isVideoPath = preg_match('#/(videos/|watch|reel/|share/(v|r)/)#', $path) === 1;

            if (! $isFbWatch && ! $isVideoPath) {
                return null;
            }

            return [
                'provider' => self::PROVIDER_FACEBOOK,
                'url' => $url,
                'embed_url' => 'https://www.facebook.com/plugins/video.php?href='.urlencode($url).'&show_text=false&width=1280',
                'is_vertical' => preg_match('#/(reel/|share/r/)#', $path) === 1,
            ];
        }

        return null;
    }

    /**
     * @param  array<int, mixed>|null  $urls
     * @return list<array{provider: string, url: string, embed_url: string, is_vertical: bool}>
     */
    public static function fromList(?array $urls): array
    {
        return array_values(array_filter(array_map(
            fn (mixed $url): ?array => is_string($url) ? self::fromUrl($url) : null,
            $urls ?? [],
        )));
    }

    /**
     * @return array{provider: string, url: string, embed_url: string, is_vertical: bool}|null
     */
    private static function youtube(string $url, string $videoId, bool $isVertical): ?array
    {
        if (preg_match('/^[\w-]{11}$/', $videoId) !== 1) {
            return null;
        }

        return [
            'provider' => self::PROVIDER_YOUTUBE,
            'url' => $url,
            'embed_url' => 'https://www.youtube-nocookie.com/embed/'.$videoId,
            'is_vertical' => $isVertical,
        ];
    }
}
