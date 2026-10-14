<?php

namespace App\Http\Middleware;

use App\Jobs\WarmRecentImageVariants;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Creates the resized copies of photos uploaded from the dashboard once the response has been sent,
 * so the first visitor of a new photo does not wait for them to be generated.
 */
class WarmUploadedImages
{
    public const RECENT_MINUTES = 10;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->allFiles() !== [] && $response->isRedirection()) {
            WarmRecentImageVariants::dispatchAfterResponse(now()->subMinutes(self::RECENT_MINUTES)->getTimestamp());
        }

        return $response;
    }
}
