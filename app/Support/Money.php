<?php

namespace App\Support;

class Money
{
    /**
     * Format an amount in US dollars, dropping cents for whole amounts ("$1,250" or "$12.50").
     */
    public static function format(int|float|string|null $amount): string
    {
        $value = (float) $amount;
        $decimals = floor($value) === $value ? 0 : 2;

        return '$'.number_format($value, $decimals);
    }
}
