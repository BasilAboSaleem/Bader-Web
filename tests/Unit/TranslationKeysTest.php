<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TranslationKeysTest extends TestCase
{
    public function test_every_literal_translation_key_exists_in_both_languages(): void
    {
        $root = dirname(__DIR__, 2);
        $arabic = json_decode(file_get_contents($root.'/lang/ar.json'), true, flags: JSON_THROW_ON_ERROR);
        $english = json_decode(file_get_contents($root.'/lang/en.json'), true, flags: JSON_THROW_ON_ERROR);

        $missing = [];

        foreach ($this->sourceFiles($root) as $path) {
            preg_match_all("/(?:__|trans_choice)\\('([a-z][a-z0-9_]*\\.[a-z0-9_.]*[a-z0-9_])'\\s*[,)]/", file_get_contents($path), $matches);

            foreach ($matches[1] as $key) {
                foreach (['ar' => $arabic, 'en' => $english] as $locale => $translations) {
                    if (! array_key_exists($key, $translations)) {
                        $missing[] = "{$locale}: {$key} (".basename($path).')';
                    }
                }
            }
        }

        $this->assertSame([], array_values(array_unique($missing)));
        $this->assertSame([], array_keys(array_diff_key($arabic, $english)), 'Keys only defined in Arabic');
        $this->assertSame([], array_keys(array_diff_key($english, $arabic)), 'Keys only defined in English');
    }

    /**
     * @return list<string>
     */
    private function sourceFiles(string $root): array
    {
        $files = [];
        $directories = [$root.'/resources/views', $root.'/app'];

        foreach ($directories as $directory) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
                $path = str_replace('\\', '/', $file->getPathname());

                if (str_ends_with($path, '.php') && ! preg_match('#/views/(auth|dashboard/users)/|/UserController\.php$#', $path)) {
                    $files[] = $path;
                }
            }
        }

        return $files;
    }
}
