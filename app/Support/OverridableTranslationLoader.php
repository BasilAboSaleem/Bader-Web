<?php

namespace App\Support;

use Illuminate\Contracts\Translation\Loader;

/**
 * Wraps the file loader so JSON translation strings edited in Dashboard → Site texts replace the lang file values.
 */
class OverridableTranslationLoader implements Loader
{
    public function __construct(private Loader $loader) {}

    /**
     * @return array<string, mixed>
     */
    public function load($locale, $group, $namespace = null): array
    {
        $lines = $this->loader->load($locale, $group, $namespace);

        if ($group !== '*' || $namespace !== '*') {
            return $lines;
        }

        return array_replace($lines, SiteSettings::textOverrides($locale));
    }

    public function addNamespace($namespace, $hint): void
    {
        $this->loader->addNamespace($namespace, $hint);
    }

    public function addJsonPath($path): void
    {
        $this->loader->addJsonPath($path);
    }

    /**
     * @return array<string, string>
     */
    public function namespaces(): array
    {
        return $this->loader->namespaces();
    }

    /**
     * @param  array<int, mixed>  $arguments
     */
    public function __call(string $method, array $arguments): mixed
    {
        return $this->loader->{$method}(...$arguments);
    }
}
