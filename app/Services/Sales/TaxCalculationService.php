<?php

namespace App\Services\Sales;

use App\Models\Sale;

class TaxCalculationService
{
    /**
     * Calculate comprehensive line and invoice tax totals.
     *
     * @param array $items
     * @param int $gstType 1 = CGST + SGST, 2 = IGST
     * @param float $invoiceDiscount
     * @param float $roundOff
     * @return array
     */
    public function calculateTax(
        array $items,
        int $gstType = Sale::GST_CGST_SGST,
        float $invoiceDiscount = 0.00,
        float $roundOff = 0.00
    ): array {
        $subtotal = 0.00;
        $itemDiscountTotal = 0.00;
        $cgstTotal = 0.00;
        $sgstTotal = 0.00;
        $igstTotal = 0.00;
        $taxTotal = 0.00;
        $calculatedItems = [];

        foreach ($items as $item) {
            $line = $this->calculateLineTax($item, $gstType);
            $calculatedItems[] = $line;

            $subtotal += $line['gross_amount'];
            $itemDiscountTotal += $line['discount_amount'];
            $cgstTotal += $line['cgst_amount'];
            $sgstTotal += $line['sgst_amount'];
            $igstTotal += $line['igst_amount'];
            $taxTotal += $line['tax_amount'];
        }

        $netSubtotal = max(0, $subtotal - $itemDiscountTotal - $invoiceDiscount);
        $grandTotal = round($netSubtotal + $taxTotal + $roundOff, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'item_discount' => round($itemDiscountTotal, 2),
            'invoice_discount' => round($invoiceDiscount, 2),
            'cgst_amount' => round($cgstTotal, 2),
            'sgst_amount' => round($sgstTotal, 2),
            'igst_amount' => round($igstTotal, 2),
            'tax_amount' => round($taxTotal, 2),
            'round_off' => round($roundOff, 2),
            'grand_total' => $grandTotal,
            'items' => $calculatedItems,
        ];
    }

    /**
     * Calculate individual line item tax breakup.
     *
     * @param array $item
     * @param int $gstType
     * @return array
     */
    public function calculateLineTax(array $item, int $gstType = Sale::GST_CGST_SGST): array
    {
        $quantity = (float) ($item['quantity'] ?? 1);
        $rate = (float) ($item['rate'] ?? 0);
        $grossAmount = $quantity * $rate;

        // Discount calculation
        $discountType = (int) ($item['discount_type'] ?? 2); // 1 = %, 2 = Fixed
        $discountValue = (float) ($item['discount_value'] ?? 0);
        if ($discountType === 1) {
            $discountAmount = ($grossAmount * $discountValue) / 100;
        } else {
            $discountAmount = $discountValue;
        }
        $discountAmount = min($grossAmount, max(0, $discountAmount));
        $taxableAmount = max(0, $grossAmount - $discountAmount);

        $taxPercentage = (float) ($item['tax_percentage'] ?? 0);

        if ($gstType === Sale::GST_CGST_SGST) {
            $cgstPercentage = round($taxPercentage / 2, 2);
            $sgstPercentage = round($taxPercentage / 2, 2);
            $igstPercentage = 0.00;

            $cgstAmount = $this->calculateCGST($taxableAmount, $cgstPercentage);
            $sgstAmount = $this->calculateSGST($taxableAmount, $sgstPercentage);
            $igstAmount = 0.00;
            $taxAmount = round($cgstAmount + $sgstAmount, 2);
        } else {
            $cgstPercentage = 0.00;
            $sgstPercentage = 0.00;
            $igstPercentage = $taxPercentage;

            $cgstAmount = 0.00;
            $sgstAmount = 0.00;
            $igstAmount = $this->calculateIGST($taxableAmount, $igstPercentage);
            $taxAmount = $igstAmount;
        }

        $lineTotal = round($taxableAmount + $taxAmount, 2);

        return array_merge($item, [
            'quantity' => $quantity,
            'rate' => $rate,
            'gross_amount' => round($grossAmount, 2),
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_amount' => round($discountAmount, 2),
            'taxable_amount' => round($taxableAmount, 2),
            'tax_percentage' => $taxPercentage,
            'cgst_percentage' => $cgstPercentage,
            'cgst_amount' => $cgstAmount,
            'sgst_percentage' => $sgstPercentage,
            'sgst_amount' => $sgstAmount,
            'igst_percentage' => $igstPercentage,
            'igst_amount' => $igstAmount,
            'tax_amount' => $taxAmount,
            'line_total' => $lineTotal,
        ]);
    }

    /**
     * Calculate CGST component.
     */
    public function calculateCGST(float $taxableAmount, float $rate): float
    {
        return round(($taxableAmount * $rate) / 100, 2);
    }

    /**
     * Calculate SGST component.
     */
    public function calculateSGST(float $taxableAmount, float $rate): float
    {
        return round(($taxableAmount * $rate) / 100, 2);
    }

    /**
     * Calculate IGST component.
     */
    public function calculateIGST(float $taxableAmount, float $rate): float
    {
        return round(($taxableAmount * $rate) / 100, 2);
    }
}
