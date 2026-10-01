<?php

namespace App\Support;

use App\Models\Campaign;
use App\Models\Program;
use App\Models\Region;
use App\Models\SponsorshipCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class PublicNavigation
{
    public const MENU_CACHE_KEY = 'public-navigation.menus';

    /**
     * Models whose changes must refresh the cached mega-menu data.
     *
     * @var list<class-string<Model>>
     */
    public const MENU_SOURCES = [Campaign::class, Program::class, Region::class, SponsorshipCase::class];

    /**
     * @return list<array{route: string, key: string}>
     */
    public static function primary(): array
    {
        return [
            ['route' => 'home', 'key' => 'nav.home'],
            ['route' => 'about', 'key' => 'nav.about'],
            ['route' => 'programs', 'key' => 'nav.programs'],
            ['route' => 'campaigns', 'key' => 'nav.campaigns'],
            ['route' => 'sponsorship', 'key' => 'nav.sponsorship'],
            ['route' => 'news', 'key' => 'nav.news'],
        ];
    }

    /**
     * @return list<array{route: string, key: string}>
     */
    public static function secondary(): array
    {
        return [
            ['route' => 'impact', 'key' => 'nav.impact'],
            ['route' => 'gift', 'key' => 'nav.gift'],
            ['route' => 'zakat', 'key' => 'nav.zakat'],
            ['route' => 'partners', 'key' => 'nav.partners'],
            ['route' => 'volunteer', 'key' => 'nav.volunteer'],
            ['route' => 'faq', 'key' => 'nav.faq'],
            ['route' => 'contact', 'key' => 'nav.contact'],
        ];
    }

    /**
     * @return list<array{route: string, key: string}>
     */
    public static function all(): array
    {
        return array_merge(self::primary(), self::secondary());
    }

    /**
     * Links grouped under the "About Bader" mega menu.
     *
     * @return list<array{route: string, key: string}>
     */
    public static function aboutLinks(): array
    {
        return [
            ['route' => 'about', 'key' => 'nav.about_us'],
            ['route' => 'impact', 'key' => 'nav.impact'],
            ['route' => 'partners', 'key' => 'nav.partners'],
            ['route' => 'volunteer', 'key' => 'nav.volunteer'],
            ['route' => 'faq', 'key' => 'nav.faq'],
            ['route' => 'contact', 'key' => 'nav.contact'],
        ];
    }

    /**
     * Database-driven content for the header mega menus.
     *
     * @return array{regions: Collection<int, Region>, programs: Collection<int, Program>, campaigns: Collection<int, Campaign>, waitingCases: Collection<int, SponsorshipCase>}
     */
    public static function menus(): array
    {
        $cached = Cache::remember(self::MENU_CACHE_KEY, now()->addHour(), fn (): array => [
            'regions' => self::toCacheable(Region::published()
                ->withCount(['campaigns' => fn ($campaigns) => $campaigns->published()])
                ->get()),
            'programs' => self::toCacheable(Program::published()
                ->withCount(['campaigns' => fn ($campaigns) => $campaigns->published()])
                ->get()),
            'campaigns' => self::toCacheable(Campaign::published()->ordered()->with('region')->take(3)->get(), 'region'),
            'waitingCases' => self::toCacheable(SponsorshipCase::available()->longestWaiting()->with('region')->take(3)->get(), 'region'),
        ]);

        return [
            'regions' => self::fromCacheable(Region::class, $cached['regions']),
            'programs' => self::fromCacheable(Program::class, $cached['programs']),
            'campaigns' => self::fromCacheable(Campaign::class, $cached['campaigns'], 'region', Region::class),
            'waitingCases' => self::fromCacheable(SponsorshipCase::class, $cached['waitingCases'], 'region', Region::class),
        ];
    }

    /**
     * Reduce models to raw attribute arrays, because the cache refuses to unserialize PHP objects.
     *
     * @param  Collection<int, Model>  $models
     * @return list<array{attributes: array<string, mixed>, relation: array<string, mixed>|null}>
     */
    private static function toCacheable(Collection $models, ?string $relation = null): array
    {
        return $models->map(fn (Model $model): array => [
            'attributes' => $model->getAttributes(),
            'relation' => $relation !== null ? $model->getRelation($relation)?->getAttributes() : null,
        ])->values()->all();
    }

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelClass
     * @param  list<array{attributes: array<string, mixed>, relation: array<string, mixed>|null}>  $items
     * @param  class-string<Model>|null  $relationClass
     * @return Collection<int, TModel>
     */
    private static function fromCacheable(string $modelClass, array $items, ?string $relation = null, ?string $relationClass = null): Collection
    {
        return new Collection(array_map(function (array $item) use ($modelClass, $relation, $relationClass): Model {
            $model = (new $modelClass)->newFromBuilder($item['attributes']);

            if ($relation !== null && $relationClass !== null) {
                $model->setRelation($relation, $item['relation'] !== null ? (new $relationClass)->newFromBuilder($item['relation']) : null);
            }

            return $model;
        }, $items));
    }

    public static function flushMenus(): void
    {
        Cache::forget(self::MENU_CACHE_KEY);
    }
}
