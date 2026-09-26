<?php

namespace App\Services\Calculation;

readonly class CalculationInput
{
    /**
     * @param LineInput[] $lines
     */
    public function __construct(
        public array $lines,
        public CalculationOptions $options,
        public float $documentDiscount = 0.00,
        public ?float $roundOffOverride = null,
    ) {
    }
}
