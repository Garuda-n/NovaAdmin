<?php

use App\Services\Calculation\CalculationEngine;
use App\Services\Calculation\CalculationInput;
use App\Services\Calculation\CalculationOptions;
use App\Services\Calculation\DiscountType;
use App\Services\Calculation\LineInput;
use App\Services\Calculation\RoundOffMode;
use App\Services\Calculation\TaxMode;

function quotationOptions(): CalculationOptions
{
    return new CalculationOptions(
        taxMode: TaxMode::Flat,
        roundOffMode: RoundOffMode::None,
        roundLineAmountBeforeTax: true,
    );
}

function salesOptions(TaxMode $taxMode = TaxMode::CgstSgst): CalculationOptions
{
    return new CalculationOptions(
        taxMode: $taxMode,
        roundOffMode: RoundOffMode::NearestWhole,
        roundLineAmountBeforeTax: false,
    );
}

beforeEach(function () {
    $this->engine = new CalculationEngine();
});

test('single line, flat tax, quotation-style rounding', function () {
    $line = $this->engine->calculateLine(new LineInput(quantity: 2, rate: 50.5, taxPercent: 18), quotationOptions());

    expect($line->grossAmount)->toBe(101.0)
        ->and($line->taxableAmount)->toBe(101.0)
        ->and($line->taxAmount)->toBe(18.18)
        ->and($line->lineTotal)->toBe(119.18);
});

test('multiple lines sum to document totals with no round-off', function () {
    $result = $this->engine->calculate(new CalculationInput(
        lines: [
            new LineInput(quantity: 1, rate: 100, taxPercent: 18),
            new LineInput(quantity: 2, rate: 50, taxPercent: 5),
        ],
        options: quotationOptions(),
    ));

    expect($result->subtotal)->toBe(200.0)
        ->and($result->taxAmount)->toBe(23.0)
        ->and($result->roundOff)->toBe(0.0)
        ->and($result->grandTotal)->toBe(223.0);
});

test('decimal quantity and rate', function () {
    $line = $this->engine->calculateLine(new LineInput(quantity: 1.25, rate: 40.00, taxPercent: 18), quotationOptions());

    expect($line->grossAmount)->toBe(50.0)
        ->and($line->taxAmount)->toBe(9.0)
        ->and($line->lineTotal)->toBe(59.0);
});

test('empty line list produces all-zero totals', function () {
    $result = $this->engine->calculate(new CalculationInput(lines: [], options: salesOptions()));

    expect($result->subtotal)->toBe(0.0)
        ->and($result->taxAmount)->toBe(0.0)
        ->and($result->grandTotal)->toBe(0.0)
        ->and($result->roundOff)->toBe(0.0)
        ->and($result->lines)->toBe([]);
});

test('zero discount leaves taxable amount unchanged', function () {
    $line = $this->engine->calculateLine(
        new LineInput(quantity: 1, rate: 100, taxPercent: 18, discountType: DiscountType::Fixed, discountValue: 0),
        salesOptions()
    );

    expect($line->discountAmount)->toBe(0.0)
        ->and($line->taxableAmount)->toBe(100.0);
});

test('percentage discount reduces the taxable amount before tax', function () {
    $line = $this->engine->calculateLine(
        new LineInput(quantity: 1, rate: 200, taxPercent: 18, discountType: DiscountType::Percentage, discountValue: 10),
        salesOptions()
    );

    expect($line->discountAmount)->toBe(20.0)
        ->and($line->taxableAmount)->toBe(180.0)
        ->and($line->cgstAmount)->toBe(16.2)
        ->and($line->sgstAmount)->toBe(16.2)
        ->and($line->taxAmount)->toBe(32.4)
        ->and($line->lineTotal)->toBe(212.4);
});

test('fixed discount larger than gross amount is clamped to the gross amount', function () {
    $line = $this->engine->calculateLine(
        new LineInput(quantity: 1, rate: 50, taxPercent: 18, discountType: DiscountType::Fixed, discountValue: 100),
        salesOptions()
    );

    expect($line->discountAmount)->toBe(50.0)
        ->and($line->taxableAmount)->toBe(0.0)
        ->and($line->taxAmount)->toBe(0.0)
        ->and($line->lineTotal)->toBe(0.0);
});

test('IGST mode taxes the full percentage as a single component', function () {
    $line = $this->engine->calculateLine(new LineInput(quantity: 1, rate: 1000, taxPercent: 18), salesOptions(TaxMode::Igst));

    expect($line->cgstAmount)->toBe(0.0)
        ->and($line->sgstAmount)->toBe(0.0)
        ->and($line->igstPercent)->toBe(18.0)
        ->and($line->igstAmount)->toBe(180.0)
        ->and($line->taxAmount)->toBe(180.0)
        ->and($line->lineTotal)->toBe(1180.0);
});

test('CGST+SGST split can round a paisa differently than flat tax on the same amount (documented, preserved)', function () {
    $salesStyle = $this->engine->calculateLine(new LineInput(quantity: 1, rate: 10.30, taxPercent: 18), salesOptions());
    $quotationStyle = $this->engine->calculateLine(new LineInput(quantity: 1, rate: 10.30, taxPercent: 18), quotationOptions());

    expect($salesStyle->cgstAmount)->toBe(0.93)
        ->and($salesStyle->sgstAmount)->toBe(0.93)
        ->and($salesStyle->taxAmount)->toBe(1.86)
        ->and($salesStyle->lineTotal)->toBe(12.16)
        ->and($quotationStyle->taxAmount)->toBe(1.85)
        ->and($quotationStyle->lineTotal)->toBe(12.15);
});

test('invoice/document discount reduces the grand total but not the tax base', function () {
    $items = [new LineInput(quantity: 1, rate: 120, taxPercent: 18)];

    $withoutDiscount = $this->engine->calculate(new CalculationInput($items, salesOptions(), documentDiscount: 0));
    $withDiscount = $this->engine->calculate(new CalculationInput($items, salesOptions(), documentDiscount: 2.00));

    expect($withoutDiscount->taxAmount)->toBe(21.6)
        ->and($withoutDiscount->grandTotal)->toBe(142.0)
        ->and($withoutDiscount->roundOff)->toBe(0.4)
        ->and($withDiscount->taxAmount)->toBe(21.6)
        ->and($withDiscount->documentDiscount)->toBe(2.0)
        ->and($withDiscount->grandTotal)->toBe(140.0)
        ->and($withDiscount->roundOff)->toBe(0.4);
});

test('round-off mode None never rounds the grand total to a whole unit', function () {
    $result = $this->engine->calculate(new CalculationInput(
        [new LineInput(quantity: 1, rate: 100, taxPercent: 18)],
        quotationOptions(),
    ));

    expect($result->grandTotal)->toBe(118.0)
        ->and($result->roundOff)->toBe(0.0);
});

test('round-off mode NearestWhole rounds a value exactly at the half-rupee boundary', function () {
    $result = $this->engine->calculate(new CalculationInput(
        [new LineInput(quantity: 1, rate: 100.50, taxPercent: 0)],
        salesOptions(),
    ));

    expect($result->grandTotal)->toBe(101.0)
        ->and($result->roundOff)->toBe(0.5);
});

test('manual round-off override is honoured regardless of round-off mode', function () {
    $result = $this->engine->calculate(new CalculationInput(
        [new LineInput(quantity: 1, rate: 100, taxPercent: 0)],
        salesOptions(),
        roundOffOverride: 5.00,
    ));

    expect($result->roundOff)->toBe(5.0)
        ->and($result->grandTotal)->toBe(105.0);
});

test('negative quantity, rate and tax percent are clamped to zero', function () {
    $line = $this->engine->calculateLine(new LineInput(quantity: -5, rate: -10, taxPercent: -18), quotationOptions());

    expect($line->quantity)->toBe(0.0)
        ->and($line->rate)->toBe(0.0)
        ->and($line->grossAmount)->toBe(0.0)
        ->and($line->taxAmount)->toBe(0.0)
        ->and($line->lineTotal)->toBe(0.0);
});

test('negative percentage discount is clamped so it cannot inflate the taxable amount', function () {
    $line = $this->engine->calculateLine(
        new LineInput(quantity: 1, rate: 100, taxPercent: 0, discountType: DiscountType::Percentage, discountValue: -10),
        salesOptions()
    );

    expect($line->discountAmount)->toBe(0.0)
        ->and($line->taxableAmount)->toBe(100.0);
});
