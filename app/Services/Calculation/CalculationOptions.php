<?php

namespace App\Services\Calculation;

/**
 * Per-document-type configuration. The engine itself has no notion of
 * "Quotation" or "Sale" — each consumer supplies the options that reproduce
 * its own existing business rules.
 */
readonly class CalculationOptions
{
    public function __construct(
        public TaxMode $taxMode = TaxMode::Flat,
        public RoundOffMode $roundOffMode = RoundOffMode::None,
        /**
         * Quotation rounds qty*rate to 2 decimals before computing tax on it;
         * Sales computes tax on the raw (unrounded) gross amount. This flag
         * preserves that difference instead of forcing one universal order.
         */
        public bool $roundLineAmountBeforeTax = false,
    ) {
    }
}
