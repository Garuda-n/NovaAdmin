<?php

namespace App\Services\Calculation;

readonly class LineResult
{
    public function __construct(
        public float $quantity,
        public float $rate,
        public float $grossAmount,
        public DiscountType $discountType,
        public float $discountValue,
        public float $discountAmount,
        public float $taxableAmount,
        public float $taxPercent,
        public float $cgstPercent,
        public float $cgstAmount,
        public float $sgstPercent,
        public float $sgstAmount,
        public float $igstPercent,
        public float $igstAmount,
        public float $taxAmount,
        public float $lineTotal,
    ) {
    }
}
