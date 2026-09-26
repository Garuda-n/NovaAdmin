<?php

namespace App\Services\Calculation;

/**
 * Central rounding helper for the engine. Kept as a thin wrapper (rather than
 * switching to bcmath) so results continue to match the amounts already
 * stored in the database from the legacy PricingService/TaxCalculationService.
 */
class Money
{
    public static function round(float $amount, int $decimals = 2): float
    {
        return (float) number_format(round($amount, $decimals), $decimals, '.', '');
    }
}
