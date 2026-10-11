<?php

namespace Tests\Feature;

use App\Models\Story;
use App\Support\ResponsiveImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ResponsiveImageTest extends TestCase
{
    use RefreshDatabase;

    private const SOURCE_DIRECTORY = 'images/responsive-image-test';

    protected function tearDown(): void
    {
        File::deleteDirectory(public_path(self::SOURCE_DIRECTORY));

        foreach (ResponsiveImage::WIDTHS as $width) {
            File::deleteDirectory(public_path('variants/'.$width.'/'.self::SOURCE_DIRECTORY));
        }

        parent::tearDown();
    }

    public function test_first_request_saves_a_resized_webp_where_the_web_server_serves_it_next_time(): void
    {
        $this->createPhoto('wide.jpg', 2000, 1000);

        $this->get('/variants/480/'.self::SOURCE_DIRECTORY.'/wide.jpg.webp')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/webp');

        $variant = public_path('variants/480/'.self::SOURCE_DIRECTORY.'/wide.jpg.webp');
        $this->assertFileExists($variant);
        $this->assertSame([480, 240], array_slice(getimagesize($variant), 0, 2));
    }

    public function test_small_photos_are_not_enlarged(): void
    {
        $this->createPhoto('small.png', 300, 200);

        $this->get('/variants/960/'.self::SOURCE_DIRECTORY.'/small.png.webp')->assertOk();

        $this->assertSame(300, getimagesize(public_path('variants/960/'.self::SOURCE_DIRECTORY.'/small.png.webp'))[0]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function rejectedVariantUrls(): array
    {
        return [
            'width outside the list' => ['/variants/500/'.self::SOURCE_DIRECTORY.'/wide.jpg.webp'],
            'missing photo' => ['/variants/480/'.self::SOURCE_DIRECTORY.'/missing.jpg.webp'],
            'folder outside storage and images' => ['/variants/480/build/wide.jpg.webp'],
            'parent directory traversal' => ['/variants/480/images/%2e%2e/'.self::SOURCE_DIRECTORY.'/wide.jpg.webp'],
            'not a photo' => ['/variants/480/'.self::SOURCE_DIRECTORY.'/notes.txt.webp'],
        ];
    }

    #[DataProvider('rejectedVariantUrls')]
    public function test_rejects_variant_requests_that_are_not_listed_widths_of_existing_public_photos(string $url): void
    {
        $this->createPhoto('wide.jpg', 2000, 1000);
        File::put(public_path(self::SOURCE_DIRECTORY.'/notes.txt'), 'not an image');

        $this->get($url)->assertNotFound();
    }

    public function test_pages_offer_resized_copies_of_local_photos_and_leave_external_images_untouched(): void
    {
        $localStory = $this->publishedStory('local-photo', 'storage/stories/rafah.jpg');
        $externalStory = $this->publishedStory('external-photo', 'https://cdn.example.org/rafah.jpg');

        $this->get(route('news.show', $localStory->key))
            ->assertOk()
            ->assertSee('src="'.asset('variants/960/storage/stories/rafah.jpg.webp').'"', false)
            ->assertSee(asset('variants/480/storage/stories/rafah.jpg.webp').' 480w', false)
            ->assertSee(asset('variants/1280/storage/stories/rafah.jpg.webp').' 1280w', false)
            ->assertSee(asset('variants/1920/storage/stories/rafah.jpg.webp').' 1920w', false);

        $this->get(route('news.show', $externalStory->key))
            ->assertOk()
            ->assertSee('src="https://cdn.example.org/rafah.jpg"', false)
            ->assertDontSee('variants/960/https', false);
    }

    private function createPhoto(string $name, int $width, int $height): void
    {
        File::ensureDirectoryExists(public_path(self::SOURCE_DIRECTORY));

        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 20, 120, 90));

        $path = public_path(self::SOURCE_DIRECTORY.'/'.$name);
        str_ends_with($name, '.png') ? imagepng($image, $path) : imagejpeg($image, $path);
    }

    private function publishedStory(string $key, string $image): Story
    {
        return Story::create([
            'key' => $key,
            'title_ar' => 'من رفح',
            'image' => $image,
            'published_at' => now()->toDateString(),
            'status' => 'published',
        ]);
    }
}
