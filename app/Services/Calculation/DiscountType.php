<?php

namespace App\Services\Calculation;

/**
 * Values match the existing `sales_details.discount_type` convention
 * (1 = Percentage, 2 = Fixed) so callers can pass the raw stored int straight through.
 */
enum DiscountType: int
{
    case Percentage = 1;
    case Fixed = 2;
}
