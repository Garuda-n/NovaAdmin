<?php

namespace Tests\Support\Legacy;

/**
 * Frozen, unmodified copy of app/Services/PricingService.php as it existed
 * before the shared Calculation Engine was introduced. Used only as a parity
 * reference in tests/Unit/Calculation/LegacyParityTest.php — never used by
 * application code.
 */
class LegacyPricingService
{
    public function calculateLine(float $qty, float $rate, float $taxPercent = 0.00, bool $isTaxInclusive = false): array
    {
        $taxPercent = max(0.00, $taxPercent);
        $qty = max(0.00, $qty);
        $rate = max(0.00, $rate);

        if ($isTaxInclusive && $taxPercent > 0) {
            $lineTotal = $this->roundAmount($qty * $rate);
            $baseAmount = $this->roundAmount($lineTotal / (1 + ($taxPercent / 100)));
            $taxAmount = $this->roundAmount($lineTotal - $baseAmount);
            $subtotal = $baseAmount;
        } else {
            $subtotal = $this->roundAmount($qty * $rate);
            $taxAmount = $this->roundAmount($subtotal * ($taxPercent / 100));
            $lineTotal = $this->roundAmount($subtotal + $taxAmount);
        }

        return [
            'qty' => $qty,
            'rate' => $rate,
            'tax_percent' => $taxPercent,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'line_total' => $lineTotal,
        ];
    }

    public function calculateTotals(array $items): array
    {
        $calculatedItems = [];
        $subtotal = 0.00;
        $taxAmount = 0.00;
        $grandTotal = 0.00;

        foreach ($items as $item) {
            $qty = (float) ($item['qty'] ?? 0);
            $rate = (float) ($item['rate'] ?? 0);
            $taxPercent = (float) ($item['tax_percent'] ?? 0);
            $isInclusive = (bool) ($item['is_tax_inclusive'] ?? false);

            $calculatedLine = $this->calculateLine($qty, $rate, $taxPercent, $isInclusive);

            $lineItem = array_merge($item, $calculatedLine);
            $calculatedItems[] = $lineItem;

            $subtotal += $calculatedLine['subtotal'];
            $taxAmount += $calculatedLine['tax_amount'];
            $grandTotal += $calculatedLine['line_total'];
        }

        return [
            'subtotal' => $this->roundAmount($subtotal),
            'tax_amount' => $this->roundAmount($taxAmount),
            'grand_total' => $this->roundAmount($grandTotal),
            'items' => $calculatedItems,
        ];
    }

    public function roundAmount(float $amount, int $decimals = 2): float
    {
        return (float) number_format(round($amount, $decimals), $decimals, '.', '');
    }
}
