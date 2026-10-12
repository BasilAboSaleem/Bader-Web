<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * Facebook's video player cannot play "share" short links (facebook.com/share/v/…, /share/r/…, fb.watch/…),
 * so they are followed once to the real video address and the answer is cached.
 *
 * Facebook also refuses to embed some videos (e.g. when they may contain music owned by someone else);
 * isEmbeddable() asks its player once a week so those videos can link to Facebook instead.
 */
final class FacebookVideoLink
{
    private const MAX_REDIRECTS = 5;

    private const USER_AGENT = 'facebookexternalhit/1.1';

    private const BROWSER_USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0 Safari/537.36';

    /**
     * Phrases of the English "Unavailable" notice Facebook's player shows instead of a blocked video.
     */
    private const BLOCKED_PLAYER_PHRASES = ["can't be embedded", 'can&#039;t be embedded', 'This video is unavailable'];

    /**
     * Whether Facebook's player plays the video on other sites. Unknown answers (network errors) count as playable.
     */
    public static function isEmbeddable(string $url): bool
    {
        $embed = VideoEmbed::fromUrl($url);

        if ($embed === null || $embed['provider'] !== VideoEmbed::PROVIDER_FACEBOOK) {
            return true;
        }

        $cacheKey = 'facebook-video-embeddable.'.sha1($url);
        $cached = Cache::get($cacheKey);

        if (is_bool($cached)) {
            return $cached;
        }

        try {
            $response = Http::withUserAgent(self::BROWSER_USER_AGENT)
                ->withHeaders(['Accept-Language' => 'en-US,en;q=0.9'])
                ->timeout(6)
                ->get($embed['embed_url']);
        } catch (Throwable) {
            $response = null;
        }

        if ($response === null || ! $response->successful()) {
            Cache::put($cacheKey, true, now()->addHour());

            return true;
        }

        $isEmbeddable = ! Str::contains($response->body(), self::BLOCKED_PLAYER_PHRASES);
        Cache::put($cacheKey, $isEmbeddable, now()->addWeek());

        return $isEmbeddable;
    }

    public static function isShortLink(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);

        if ($host === 'fb.watch' || $host === 'www.fb.watch') {
            return true;
        }

        return in_array($host, ['facebook.com', 'www.facebook.com', 'm.facebook.com', 'web.facebook.com'], true)
            && preg_match('#^/share/(v|r)/#', $path) === 1;
    }

    /**
     * The playable address for a short link, or the link unchanged when it is not one or cannot be resolved.
     */
    public static function resolve(string $url): string
    {
        if (! self::isShortLink($url)) {
            return $url;
        }

        $cacheKey = 'facebook-video-link.'.sha1($url);
        $resolved = Cache::get($cacheKey);

        if (is_string($resolved)) {
            return $resolved;
        }

        $resolved = self::follow($url);

        if ($resolved !== null) {
            Cache::forever($cacheKey, $resolved);

            return $resolved;
        }

        Cache::put($cacheKey, $url, now()->addHour());

        return $url;
    }

    private static function follow(string $url): ?string
    {
        $current = $url;

        for ($hop = 0; $hop < self::MAX_REDIRECTS; $hop++) {
            try {
                $response = Http::withUserAgent(self::USER_AGENT)->withoutRedirecting()->timeout(6)->get($current);
            } catch (Throwable) {
                return null;
            }

            $location = $response->header('Location');

            if (! $response->redirect() || $location === '') {
                return null;
            }

            $current = str_starts_with($location, '/') ? 'https://www.facebook.com'.$location : $location;
            $canonical = self::canonical($current);

            if ($canonical !== null) {
                return $canonical;
            }
        }

        return null;
    }

    private static function canonical(string $url): ?string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if (! in_array($host, ['facebook.com', 'www.facebook.com', 'm.facebook.com', 'web.facebook.com'], true)) {
            return null;
        }

        if (preg_match('#^/reel/(\d+)#', $path, $matches) === 1) {
            return 'https://www.facebook.com/reel/'.$matches[1].'/';
        }

        if (preg_match('#^/([^/]+)/videos/(?:[^/]+/)?(\d+)#', $path, $matches) === 1) {
            return 'https://www.facebook.com/'.$matches[1].'/videos/'.$matches[2].'/';
        }

        if (rtrim($path, '/') === '/watch' && is_string($query['v'] ?? null) && ctype_digit($query['v'])) {
            return 'https://www.facebook.com/watch/?v='.$query['v'];
        }

        return null;
    }
}
