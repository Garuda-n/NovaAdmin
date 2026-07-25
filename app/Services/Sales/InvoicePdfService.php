<?php

namespace App\Services\Sales;

use App\Models\Sale;
use App\Models\SalesDetail;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use NumberFormatter;

class InvoicePdfService
{
    /**
     * Generate printable/downloadable invoice document payload.
     *
     * @param Sale $sale
     * @return array
     */
    public function prepareInvoiceData(Sale $sale): array
    {
        $sale->load([
            'salesInvoiceSnapshot',
            'details',
            'salesPayments.paymentMode',
            'customerReceivable',
            'creator',
            'updater',
            'cancelledBy',
        ]);

        $snapshot = $sale->salesInvoiceSnapshot;

        // Ensure snapshot fallback exists if missing
        $companyName = $snapshot->company_name ?? 'NovaAdmin ERP';
        $companyGst = $snapshot->company_gst_number ?? 'N/A';
        $companyAddress = $snapshot->company_address ?? '';

        $branchName = $snapshot->branch_name ?? 'Main Branch';
        $branchGst = $snapshot->branch_gst_number ?? '';
        $branchAddress = $snapshot->branch_address ?? '';

        $customerName = $snapshot->customer_name ?? 'Walk-in Customer';
        $customerMobile = $snapshot->customer_mobile ?? '0000000000';
        $customerEmail = $snapshot->customer_email ?? '';
        $customerAddress = $snapshot->customer_address ?? 'N/A';
        $customerGst = $snapshot->customer_gst_number ?? 'Unregistered / B2C';

        $gstType = $snapshot->gst_type ?? $sale->gst_type;

        // Calculate Amount in Words
        $amountInWords = $this->calculateAmountInWords((float) $sale->grand_total);

        // Tax summary grouping by GST rate
        $taxSummary = [];
        foreach ($sale->details as $detail) {
            $rateKey = number_format($detail->tax_percentage, 2);
            if (!isset($taxSummary[$rateKey])) {
                $taxSummary[$rateKey] = [
                    'tax_rate' => $detail->tax_percentage,
                    'taxable_amount' => 0.00,
                    'cgst_amount' => 0.00,
                    'sgst_amount' => 0.00,
                    'igst_amount' => 0.00,
                    'tax_amount' => 0.00,
                ];
            }

            $taxable = ($detail->quantity * $detail->rate) - $detail->discount_amount;
            $taxSummary[$rateKey]['taxable_amount'] += $taxable;
            $taxSummary[$rateKey]['cgst_amount'] += $detail->cgst_amount;
            $taxSummary[$rateKey]['sgst_amount'] += $detail->sgst_amount;
            $taxSummary[$rateKey]['igst_amount'] += $detail->igst_amount;
            $taxSummary[$rateKey]['tax_amount'] += $detail->tax_amount;
        }

        return [
            'sale' => $sale,
            'snapshot' => $snapshot,
            'companyName' => $companyName,
            'companyGst' => $companyGst,
            'companyAddress' => $companyAddress,
            'branchName' => $branchName,
            'branchGst' => $branchGst,
            'branchAddress' => $branchAddress,
            'customerName' => $customerName,
            'customerMobile' => $customerMobile,
            'customerEmail' => $customerEmail,
            'customerAddress' => $customerAddress,
            'customerGst' => $customerGst,
            'gstType' => $gstType,
            'amountInWords' => $amountInWords,
            'taxSummary' => $taxSummary,
            'isCancelled' => $sale->isCancelled(),
            'generatedBy' => Auth::user()->name ?? ($sale->creator->name ?? 'System'),
        ];
    }

    /**
     * Render the invoice PDF view.
     *
     * @param Sale $sale
     * @return View
     */
    public function generateInvoicePdf(Sale $sale): View
    {
        $data = $this->prepareInvoiceData($sale);

        return view('invoices.invoice', $data);
    }

    /**
     * Convert monetary amount to words in Indian Rupees format.
     *
     * @param float $amount
     * @return string
     */
    public function calculateAmountInWords(float $amount): string
    {
        $amount = round($amount, 2);
        $rupees = (int) floor($amount);
        $paisa = (int) round(($amount - $rupees) * 100);

        $rupeesInWords = $this->numberToWordsIndian($rupees);
        $result = $rupeesInWords ? "Rupees " . $rupeesInWords : "Rupees Zero";

        if ($paisa > 0) {
            $paisaInWords = $this->numberToWordsIndian($paisa);
            $result .= " and " . $paisaInWords . " Paisa";
        }

        return $result . " Only";
    }

    /**
     * Helper to format integer to Indian numbering format words.
     *
     * @param int $number
     * @return string
     */
    protected function numberToWordsIndian(int $number): string
    {
        if ($number === 0) {
            return '';
        }

        $dictionary = [
            0 => '',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety',
        ];

        if ($number < 21) {
            return $dictionary[$number];
        }

        if ($number < 100) {
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            return trim($dictionary[$tens] . ' ' . $dictionary[$units]);
        }

        if ($number < 1000) {
            $hundreds = (int) ($number / 100);
            $remainder = $number % 100;
            return trim($dictionary[$hundreds] . ' Hundred ' . $this->numberToWordsIndian($remainder));
        }

        if ($number < 100000) {
            $thousands = (int) ($number / 1000);
            $remainder = $number % 1000;
            return trim($this->numberToWordsIndian($thousands) . ' Thousand ' . $this->numberToWordsIndian($remainder));
        }

        if ($number < 10000000) {
            $lakhs = (int) ($number / 100000);
            $remainder = $number % 100000;
            return trim($this->numberToWordsIndian($lakhs) . ' Lakh ' . $this->numberToWordsIndian($remainder));
        }

        $crores = (int) ($number / 10000000);
        $remainder = $number % 10000000;
        return trim($this->numberToWordsIndian($crores) . ' Crore ' . $this->numberToWordsIndian($remainder));
    }
}
