<?php

namespace App\Services\Calculation;

readonly class CalculationResult
{
    /**
     * @param LineResult[] $lines
     */
    public function __construct(
        public array $lines,
        public float $subtotal,
        public float $itemDiscount,
        public float $documentDiscount,
        public float $netSubtotal,
        public float $cgstAmount,
        public float $sgstAmount,
        public float $igstAmount,
        public float $taxAmount,
        public float $roundOff,
        public float $grandTotal,
    ) {
    }
}
