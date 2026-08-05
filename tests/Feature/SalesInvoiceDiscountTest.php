<?php

namespace Tests\Feature;

use App\Services\Sales\TaxCalculationService;
use Tests\TestCase;

class SalesInvoiceDiscountTest extends TestCase
{
    public function test_tax_calculation_with_auto_round_off_and_invoice_discount()
    {
        $taxService = new TaxCalculationService();

        $items = [
            [
                'quantity' => 1,
                'rate' => 120.00,
                'discount_type' => 2,
                'discount_value' => 0,
                'tax_percentage' => 18,
            ]
        ];

        // 1. Without discount and with auto round-off:
        // Subtotal = 120, Tax = 21.60 -> Exact total = 141.60.
        // Auto Round Off = +0.40 -> Rounded Bill Amount = 142.00
        $totalsAutoRoundOff = $taxService->calculateTax($items, 1, 0.00, null);
        $this->assertEquals(120.00, $totalsAutoRoundOff['subtotal']);
        $this->assertEquals(21.60, $totalsAutoRoundOff['tax_amount']);
        $this->assertEquals(0.40, $totalsAutoRoundOff['round_off']);
        $this->assertEquals(142.00, $totalsAutoRoundOff['grand_total']);

        // 2. Biller closes bill at 140 with Invoice Discount = 2.00:
        // Subtotal = 120, Invoice Discount = 2.00 -> Net Subtotal = 118.00.
        // Tax = 21.60 -> Exact total = 139.60.
        // Auto Round Off = +0.40 -> Final Bill Amount = 140.00
        $totalsWithDiscount = $taxService->calculateTax($items, 1, 2.00, null);
        $this->assertEquals(120.00, $totalsWithDiscount['subtotal']);
        $this->assertEquals(2.00, $totalsWithDiscount['invoice_discount']);
        $this->assertEquals(21.60, $totalsWithDiscount['tax_amount']);
        $this->assertEquals(0.40, $totalsWithDiscount['round_off']);
        $this->assertEquals(140.00, $totalsWithDiscount['grand_total']);
    }
}
