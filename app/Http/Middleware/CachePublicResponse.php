<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds HTTP cache-control headers to public GET responses.
 *
 * Skipped automatically for:
 *  - Authenticated users (personalised dashboard content)
 *  - Non-GET / HEAD requests
 */
class CachePublicResponse
{
    /** Default max-age in seconds (10 minutes). */
    private const MAX_AGE = 600;

    /** stale-while-revalidate window in seconds (1 minute). */
    private const STALE_WHILE_REVALIDATE = 60;

    public function handle(Request $request, Closure $next, int $maxAge = self::MAX_AGE): Response
    {
        $response = $next($request);

        // Only cache GET / HEAD responses for guests.
        if (
            $request->isMethod('GET') &&
            ! $request->user() &&
            $response->isSuccessful()
        ) {
            $response->headers->set(
                'Cache-Control',
                sprintf(
                    'public, max-age=%d, stale-while-revalidate=%d',
                    $maxAge,
                    self::STALE_WHILE_REVALIDATE,
                )
            );

            // Add ETag based on response content hash for conditional requests.
            $etag = '"'.md5($response->getContent()).'"';
            $response->headers->set('ETag', $etag);

            if ($request->headers->get('If-None-Match') === $etag) {
                $response->setStatusCode(304);
                $response->setContent('');
            }
        }

        return $response;
    }
}
