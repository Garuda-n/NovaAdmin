<?php

namespace App\Services\Calculation;

readonly class LineInput
{
    public function __construct(
        public float $quantity,
        public float $rate,
        public float $taxPercent = 0.00,
        public DiscountType $discountType = DiscountType::Fixed,
        public float $discountValue = 0.00,
    ) {
    }
}
