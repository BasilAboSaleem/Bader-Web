<?php

namespace App\Models\Concerns;

use App\Support\VideoEmbed;

/**
 * Photo gallery (stored public paths) and video links (YouTube / Facebook) for a content model.
 */
trait HasMediaGallery
{
    public function initializeHasMediaGallery(): void
    {
        $this->mergeFillable(['gallery', 'videos']);
        $this->mergeCasts(['gallery' => 'array', 'videos' => 'array']);
    }

    /**
     * @return list<string>
     */
    public function galleryPhotos(): array
    {
        return array_values(array_filter(
            $this->gallery ?? [],
            fn (mixed $path): bool => is_string($path) && $path !== '',
        ));
    }

    /**
     * @return list<array{provider: string, url: string, embed_url: string, is_vertical: bool}>
     */
    public function videoEmbeds(): array
    {
        return VideoEmbed::fromList($this->videos);
    }

    public function hasMedia(): bool
    {
        return $this->galleryPhotos() !== [] || $this->videoEmbeds() !== [];
    }
}
