<?php

namespace App\Services;

class PricingService
{
    /**
     * Calculate single line totals including subtotal, tax amount, and line total.
     *
     * @param float $qty Quantity
     * @param float $rate Rate / Price per unit
     * @param float $taxPercent Tax percentage (e.g., 18.00 for 18%)
     * @param bool $isTaxInclusive Whether rate is inclusive of tax
     * @return array Calculated line values
     */
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

    /**
     * Calculate overall document totals from an array of item lines.
     *
     * @param array $items Array of item lines
     * @return array Aggregated subtotal, tax_amount, and grand_total
     */
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

            // Merge calculated values back into line item
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

    /**
     * Round float amount to fixed decimal precision.
     *
     * @param float $amount Amount to round
     * @param int $decimals Decimal places (default 2)
     * @return float Rounded amount
     */
    public function roundAmount(float $amount, int $decimals = 2): float
    {
        return (float) number_format(round($amount, $decimals), $decimals, '.', '');
    }
}
