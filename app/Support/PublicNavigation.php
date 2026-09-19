<?php

namespace App\Support;

final class PublicNavigation
{
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
            ['route' => 'impact', 'key' => 'nav.impact'],
        ];
    }

    /**
     * @return list<array{route: string, key: string}>
     */
    public static function secondary(): array
    {
        return [
            ['route' => 'news', 'key' => 'nav.news'],
            ['route' => 'sponsorship', 'key' => 'nav.sponsorship'],
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
}
