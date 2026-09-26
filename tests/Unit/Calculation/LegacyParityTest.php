<?php

use App\Services\Calculation\CalculationEngine;
use App\Services\Calculation\CalculationInput;
use App\Services\Calculation\CalculationOptions;
use App\Services\Calculation\DiscountType;
use App\Services\Calculation\LineInput;
use App\Services\Calculation\RoundOffMode;
use App\Services\Calculation\TaxMode;
use Tests\Support\Legacy\LegacyPricingService;
use Tests\Support\Legacy\LegacyTaxCalculationService;

/**
 * Generates a fixed-seed batch of randomised documents and asserts the new
 * CalculationEngine produces byte-for-byte identical results to the frozen
 * legacy PricingService (Quotation) and TaxCalculationService (Sales)
 * implementations, across every output field. This is the primary guarantee
 * that integrating the engine does not change existing Quotation/Sales
 * results.
 */
const PARITY_CASES_PER_PROFILE = 2500;

function parityRandomFloat(float $min, float $max, int $decimals): float
{
    $scale = 10 ** $decimals;
    $value = mt_rand((int) ($min * $scale), (int) ($max * $scale)) / $scale;

    return round($value, $decimals);
}

function parityTaxPercent(): float
{
    $common = [0, 0.1, 0.25, 3, 5, 7.5, 12, 18, 28];

    return mt_rand(0, 1) === 0
        ? $common[array_rand($common)]
        : parityRandomFloat(0, 30, 2);
}

test('engine matches frozen LegacyPricingService across randomised quotation-style documents', function () {
    mt_srand(42);
    $engine = new CalculationEngine();
    $legacy = new LegacyPricingService();
    $options = new CalculationOptions(
        taxMode: TaxMode::Flat,
        roundOffMode: RoundOffMode::None,
        roundLineAmountBeforeTax: true,
    );

    for ($case = 0; $case < PARITY_CASES_PER_PROFILE; $case++) {
        $lineCount = mt_rand(1, 6);
        $legacyItems = [];
        $engineLines = [];

        for ($i = 0; $i < $lineCount; $i++) {
            $qty = parityRandomFloat(0, 500, mt_rand(0, 3));
            $rate = parityRandomFloat(0, 50000, mt_rand(0, 2));
            $taxPercent = parityTaxPercent();

            $legacyItems[] = ['qty' => $qty, 'rate' => $rate, 'tax_percent' => $taxPercent];
            $engineLines[] = new LineInput(quantity: $qty, rate: $rate, taxPercent: $taxPercent);
        }

        $legacyResult = $legacy->calculateTotals($legacyItems);
        $engineResult = $engine->calculate(new CalculationInput($engineLines, $options));

        expect($engineResult->subtotal)->toBe($legacyResult['subtotal'])
            ->and($engineResult->taxAmount)->toBe($legacyResult['tax_amount'])
            ->and($engineResult->grandTotal)->toBe($legacyResult['grand_total']);

        foreach ($engineResult->lines as $i => $line) {
            $legacyLine = $legacyResult['items'][$i];

            expect($line->taxableAmount)->toBe($legacyLine['subtotal'])
                ->and($line->taxAmount)->toBe($legacyLine['tax_amount'])
                ->and($line->lineTotal)->toBe($legacyLine['line_total']);
        }
    }
});

test('engine matches frozen LegacyTaxCalculationService across randomised sales-style documents', function () {
    mt_srand(43);
    $engine = new CalculationEngine();
    $legacy = new LegacyTaxCalculationService();

    for ($case = 0; $case < PARITY_CASES_PER_PROFILE; $case++) {
        $lineCount = mt_rand(1, 6);
        $legacyItems = [];
        $engineLines = [];

        for ($i = 0; $i < $lineCount; $i++) {
            $quantity = parityRandomFloat(0, 500, mt_rand(0, 3));
            $rate = parityRandomFloat(0, 50000, mt_rand(0, 2));
            $taxPercent = parityTaxPercent();
            $discountType = mt_rand(1, 2);
            $discountValue = $discountType === 1
                ? parityRandomFloat(0, 100, 2)
                : parityRandomFloat(0, 200, 2);

            $legacyItems[] = [
                'quantity' => $quantity,
                'rate' => $rate,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'tax_percentage' => $taxPercent,
            ];
            $engineLines[] = new LineInput(
                quantity: $quantity,
                rate: $rate,
                taxPercent: $taxPercent,
                discountType: DiscountType::from($discountType),
                discountValue: $discountValue,
            );
        }

        $gstType = mt_rand(1, 2);
        $invoiceDiscount = mt_rand(0, 3) === 0 ? 0.0 : parityRandomFloat(0, 100, 2);
        $roundOff = mt_rand(0, 2) === 0 ? null : parityRandomFloat(-5, 5, 2);

        $legacyResult = $legacy->calculateTax($legacyItems, $gstType, $invoiceDiscount, $roundOff);

        $options = new CalculationOptions(
            taxMode: $gstType === LegacyTaxCalculationService::GST_CGST_SGST ? TaxMode::CgstSgst : TaxMode::Igst,
            roundOffMode: RoundOffMode::NearestWhole,
            roundLineAmountBeforeTax: false,
        );
        $engineResult = $engine->calculate(new CalculationInput(
            lines: $engineLines,
            options: $options,
            documentDiscount: $invoiceDiscount,
            roundOffOverride: $roundOff,
        ));

        expect($engineResult->subtotal)->toBe($legacyResult['subtotal'])
            ->and($engineResult->itemDiscount)->toBe($legacyResult['item_discount'])
            ->and($engineResult->documentDiscount)->toBe($legacyResult['invoice_discount'])
            ->and($engineResult->cgstAmount)->toBe($legacyResult['cgst_amount'])
            ->and($engineResult->sgstAmount)->toBe($legacyResult['sgst_amount'])
            ->and($engineResult->igstAmount)->toBe($legacyResult['igst_amount'])
            ->and($engineResult->taxAmount)->toBe($legacyResult['tax_amount'])
            ->and($engineResult->roundOff)->toBe($legacyResult['round_off'])
            ->and($engineResult->grandTotal)->toBe($legacyResult['grand_total']);

        foreach ($engineResult->lines as $i => $line) {
            $legacyLine = $legacyResult['items'][$i];

            expect($line->grossAmount)->toBe($legacyLine['gross_amount'])
                ->and($line->discountAmount)->toBe($legacyLine['discount_amount'])
                ->and($line->taxableAmount)->toBe($legacyLine['taxable_amount'])
                ->and($line->cgstPercent)->toBe($legacyLine['cgst_percentage'])
                ->and($line->cgstAmount)->toBe($legacyLine['cgst_amount'])
                ->and($line->sgstPercent)->toBe($legacyLine['sgst_percentage'])
                ->and($line->sgstAmount)->toBe($legacyLine['sgst_amount'])
                ->and($line->igstPercent)->toBe($legacyLine['igst_percentage'])
                ->and($line->igstAmount)->toBe($legacyLine['igst_amount'])
                ->and($line->taxAmount)->toBe($legacyLine['tax_amount'])
                ->and($line->lineTotal)->toBe($legacyLine['line_total']);
        }
    }
});
