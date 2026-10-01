<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds revalidation headers (ETag + 304) to public GET responses for guests.
 *
 * Pages are "private, max-age=0" on purpose: the same URL renders in Arabic or English
 * depending on the session, and forms embed a per-session CSRF token, so neither the
 * browser nor a shared proxy (e.g. Cloudways Varnish) may serve a stored copy without
 * asking the server first. Unchanged pages still cost only an empty 304 response.
 *
 * Skipped automatically for:
 *  - Authenticated users (personalised dashboard content)
 *  - Non-GET / HEAD requests
 */
class CachePublicResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only cache GET / HEAD responses for guests.
        if (
            $request->isMethod('GET') &&
            ! $request->user() &&
            $response->isSuccessful()
        ) {
            $response->headers->set('Cache-Control', 'private, max-age=0, must-revalidate');

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
