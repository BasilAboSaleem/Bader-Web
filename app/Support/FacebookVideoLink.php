<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Facebook's video player cannot play "share" short links (facebook.com/share/v/…, /share/r/…, fb.watch/…),
 * so they are followed once to the real video address and the answer is cached.
 */
final class FacebookVideoLink
{
    private const MAX_REDIRECTS = 5;

    private const USER_AGENT = 'facebookexternalhit/1.1';

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
