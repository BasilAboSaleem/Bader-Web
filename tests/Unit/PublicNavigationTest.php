<?php

namespace Tests\Unit;

use App\Support\PublicNavigation;
use PHPUnit\Framework\TestCase;

class PublicNavigationTest extends TestCase
{
    public function test_navigation_lists_are_not_empty_and_unique(): void
    {
        $primary = array_column(PublicNavigation::primary(), 'route');
        $secondary = array_column(PublicNavigation::secondary(), 'route');
        $all = array_column(PublicNavigation::all(), 'route');

        $this->assertNotEmpty($primary);
        $this->assertNotEmpty($secondary);
        $this->assertSame(array_values(array_unique($all)), $all);
        $this->assertContains('home', $primary);
        $this->assertContains('contact', $secondary);
    }
}
