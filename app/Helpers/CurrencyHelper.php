<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function format(float $amount, string $currency = 'USD'): string
    {
        return match ($currency) {
            'KHR' => number_format($amount, 0).' ៛',
            default => '$'.number_format($amount, 2),
        };
    }

    public static function khrToUsd(float $khr, float $rate = 4100): float
    {
        return round($khr / $rate, 2);
    }
}
