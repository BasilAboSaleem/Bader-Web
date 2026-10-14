<?php

namespace App\Jobs;

use App\Support\ResponsiveImage;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Creates the resized copies of photos uploaded since the given time, run after the response is sent.
 */
class WarmRecentImageVariants
{
    use Dispatchable;

    public function __construct(public int $uploadedSince) {}

    public function handle(): void
    {
        set_time_limit(0);

        ResponsiveImage::warm($this->uploadedSince);
    }
}
