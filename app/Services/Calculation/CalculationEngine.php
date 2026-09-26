<?php

namespace App\Services\Calculation;

/**
 * Shared, document-type-agnostic calculation engine for line and document
 * totals (subtotal, discount, tax, round-off, grand total).
 *
 * This engine has no knowledge of Quotation/Sale/Customer Order — each
 * consumer supplies a CalculationOptions that reproduces its own existing
 * business rules. It intentionally does not touch stock, invoicing,
 * persistence, or workflow state.
 */
class CalculationEngine
{
    public function calculateLine(LineInput $line, CalculationOptions $options): LineResult
    {
        $quantity = max(0.00, $line->quantity);
        $rate = max(0.00, $line->rate);
        $taxPercent = max(0.00, $line->taxPercent);

        $gross = $quantity * $rate;
        if ($options->roundLineAmountBeforeTax) {
            $gross = Money::round($gross);
        }

        $discountAmount = $line->discountType === DiscountType::Percentage
            ? ($gross * $line->discountValue) / 100
            : $line->discountValue;
        $discountAmount = min($gross, max(0, $discountAmount));

        $taxable = max(0, $gross - $discountAmount);

        [$cgstPercent, $cgstAmount, $sgstPercent, $sgstAmount, $igstPercent, $igstAmount, $taxAmount] =
            $this->calculateTax($taxable, $taxPercent, $options->taxMode);

        $lineTotal = Money::round($taxable + $taxAmount);

        return new LineResult(
            quantity: $quantity,
            rate: $rate,
            grossAmount: Money::round($gross),
            discountType: $line->discountType,
            discountValue: $line->discountValue,
            discountAmount: Money::round($discountAmount),
            taxableAmount: Money::round($taxable),
            taxPercent: $taxPercent,
            cgstPercent: $cgstPercent,
            cgstAmount: $cgstAmount,
            sgstPercent: $sgstPercent,
            sgstAmount: $sgstAmount,
            igstPercent: $igstPercent,
            igstAmount: $igstAmount,
            taxAmount: $taxAmount,
            lineTotal: $lineTotal,
        );
    }

    /**
     * @return array{0: float, 1: float, 2: float, 3: float, 4: float, 5: float, 6: float}
     *         [cgstPercent, cgstAmount, sgstPercent, sgstAmount, igstPercent, igstAmount, taxAmount]
     */
    private function calculateTax(float $taxable, float $taxPercent, TaxMode $mode): array
    {
        return match ($mode) {
            TaxMode::Flat => [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, Money::round($taxable * ($taxPercent / 100))],
            TaxMode::CgstSgst => (function () use ($taxable, $taxPercent) {
                $cgstPercent = Money::round($taxPercent / 2);
                $sgstPercent = Money::round($taxPercent / 2);
                $cgstAmount = Money::round(($taxable * $cgstPercent) / 100);
                $sgstAmount = Money::round(($taxable * $sgstPercent) / 100);

                return [$cgstPercent, $cgstAmount, $sgstPercent, $sgstAmount, 0.00, 0.00, Money::round($cgstAmount + $sgstAmount)];
            })(),
            TaxMode::Igst => (function () use ($taxable, $taxPercent) {
                $igstAmount = Money::round(($taxable * $taxPercent) / 100);

                return [0.00, 0.00, 0.00, 0.00, $taxPercent, $igstAmount, $igstAmount];
            })(),
        };
    }

    public function calculate(CalculationInput $input): CalculationResult
    {
        $lines = [];
        $subtotal = 0.00;
        $itemDiscount = 0.00;
        $cgstAmount = 0.00;
        $sgstAmount = 0.00;
        $igstAmount = 0.00;
        $taxAmount = 0.00;

        foreach ($input->lines as $lineInput) {
            $line = $this->calculateLine($lineInput, $input->options);
            $lines[] = $line;

            $subtotal += $line->grossAmount;
            $itemDiscount += $line->discountAmount;
            $cgstAmount += $line->cgstAmount;
            $sgstAmount += $line->sgstAmount;
            $igstAmount += $line->igstAmount;
            $taxAmount += $line->taxAmount;
        }

        $documentDiscount = $input->documentDiscount;
        $netSubtotal = max(0, $subtotal - $itemDiscount - $documentDiscount);
        $unrounded = $netSubtotal + $taxAmount;

        if ($input->roundOffOverride !== null) {
            $roundOff = round($input->roundOffOverride, 2);
            $grandTotal = round($unrounded + $roundOff, 2);
        } elseif ($input->options->roundOffMode === RoundOffMode::NearestWhole) {
            $grandTotal = (float) round($unrounded);
            $roundOff = round($grandTotal - $unrounded, 2);
        } else {
            $roundOff = 0.00;
            $grandTotal = Money::round($unrounded);
        }

        return new CalculationResult(
            lines: $lines,
            subtotal: Money::round($subtotal),
            itemDiscount: Money::round($itemDiscount),
            documentDiscount: Money::round($documentDiscount),
            netSubtotal: Money::round($netSubtotal),
            cgstAmount: Money::round($cgstAmount),
            sgstAmount: Money::round($sgstAmount),
            igstAmount: Money::round($igstAmount),
            taxAmount: Money::round($taxAmount),
            roundOff: $roundOff,
            grandTotal: $grandTotal,
        );
    }
}
