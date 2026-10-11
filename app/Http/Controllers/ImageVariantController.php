<?php

namespace App\Http\Controllers;

use App\Support\ResponsiveImage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageVariantController extends Controller
{
    public function __invoke(int $width, string $path): BinaryFileResponse
    {
        $format = Str::afterLast($path, '.');
        $file = ResponsiveImage::generate($width, Str::beforeLast($path, '.'), $format);

        abort_if($file === null, 404);

        return response()->file($file, [
            'Content-Type' => 'image/'.$format,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
