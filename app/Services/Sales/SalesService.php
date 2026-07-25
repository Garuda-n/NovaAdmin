<?php

namespace App\Services\Sales;

use App\Models\Branch;
use App\Models\CustomerReceivable;
use App\Models\PaymentMode;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\SalesInvoiceSnapshot;
use App\Models\StockItem;
use App\Services\Inventory\InventoryService;
use App\Services\SettingService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SalesService
{
    protected TaxCalculationService $taxService;
    protected ReceivableService $receivableService;
    protected PaymentService $paymentService;
    protected InventoryService $inventoryService;

    /**
     * SalesService constructor.
     *
     * @param TaxCalculationService $taxService
     * @param ReceivableService $receivableService
     * @param PaymentService $paymentService
     * @param InventoryService $inventoryService
     */
    public function __construct(
        TaxCalculationService $taxService,
        ReceivableService $receivableService,
        PaymentService $paymentService,
        InventoryService $inventoryService
    ) {
        $this->taxService = $taxService;
        $this->receivableService = $receivableService;
        $this->paymentService = $paymentService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Get paginated sales listing with filter options.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getPaginatedSales(Request $request): LengthAwarePaginator
    {
        $query = Sale::with(['customer', 'branch', 'counter', 'creator', 'salesPayments', 'customerReceivable']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('invoice_no_display', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('mobile', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sale_type')) {
            $query->where('sale_type', $request->sale_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        return $query->latest('id')->paginate(15)->withQueryString();
    }

    /**
     * Get data required to render the Quotation -> Sale conversion form screen.
     *
     * @param Quotation $quotation
     * @return array
     */
    public function getCreateFromQuotationData(Quotation $quotation): array
    {
        if (!$quotation->isConvertible()) {
            throw new InvalidArgumentException("Quotation #{$quotation->quotation_no} is expired or already converted.");
        }

        $quotation->load(['customer', 'branch.company', 'counter', 'details.product', 'details.uom']);

        if (!$quotation->customer) {
            throw new InvalidArgumentException("Selected quotation does not have a valid associated customer.");
        }

        if ($quotation->details->isEmpty()) {
            throw new InvalidArgumentException("Selected quotation does not contain any line items.");
        }

        // Determine GST type: 1 = CGST+SGST, 2 = IGST based on customer/branch location or default 1
        $gstType = Sale::GST_CGST_SGST;
        if (isset($quotation->customer->gst_number) && !empty($quotation->customer->gst_number)) {
            // Check state code prefix (e.g. "33") if available
            $branchGst = $quotation->branch->gst_number ?? '';
            if (!empty($branchGst) && strlen($branchGst) >= 2 && strlen($quotation->customer->gst_number) >= 2) {
                if (substr($branchGst, 0, 2) !== substr($quotation->customer->gst_number, 0, 2)) {
                    $gstType = Sale::GST_IGST;
                }
            }
        }

        $itemsPreview = [];
        foreach ($quotation->details as $detail) {
            $itemsPreview[] = [
                'product_id' => $detail->product_id,
                'uom_id' => $detail->uom_id,
                'allocated_item_id' => null,
                'product_code' => $detail->product->product_code ?? 'PROD',
                'product_name' => $detail->product_name ?? $detail->product->name,
                'item_type' => SalesDetail::ITEM_UNALLOCATED,
                'quantity' => (float) $detail->qty,
                'rate' => (float) $detail->rate,
                'discount_type' => 2,
                'discount_value' => 0.00,
                'discount_amount' => 0.00,
                'tax_percentage' => (float) $detail->tax_percent,
            ];
        }

        $totals = $this->calculateTotals($itemsPreview, $gstType);

        $paymentModes = PaymentMode::where('status', PaymentMode::STATUS_ACTIVE)
            ->orderBy('display_order', 'asc')
            ->get();

        if ($paymentModes->isEmpty()) {
            (new \Database\Seeders\PaymentModeSeeder())->run();
            $paymentModes = PaymentMode::where('status', PaymentMode::STATUS_ACTIVE)
                ->orderBy('display_order', 'asc')
                ->get();
        }

        return [
            'quotation' => $quotation,
            'customer' => $quotation->customer,
            'branch' => $quotation->branch,
            'counter' => $quotation->counter,
            'itemsPreview' => $totals['items'],
            'totals' => $totals,
            'paymentModes' => $paymentModes,
            'gstType' => $gstType,
        ];
    }

    /**
     * Execute the atomic Quotation to Sales Invoice conversion within a DB transaction.
     *
     * @param Quotation $quotation
     * @param array $data
     * @return Sale
     */
    public function convertQuotationToSale(Quotation $quotation, array $data): Sale
    {
        if (!$quotation->isConvertible()) {
            throw new InvalidArgumentException("Quotation #{$quotation->quotation_no} is expired or already converted.");
        }

        return DB::transaction(function () use ($quotation, $data) {
            $quotation->load(['customer', 'branch.company', 'counter', 'details.product', 'details.uom']);

            if (!$quotation->customer) {
                throw new InvalidArgumentException("Quotation customer missing.");
            }

            if ($quotation->details->isEmpty()) {
                throw new InvalidArgumentException("Quotation contains no line items.");
            }

            $userId = Auth::id() ?? $quotation->created_by;
            $gstType = (int) ($data['gst_type'] ?? Sale::GST_CGST_SGST);
            $saleType = (int) ($data['sale_type'] ?? Sale::TYPE_CASH);
            $invoiceDiscount = (float) ($data['invoice_discount'] ?? 0.00);
            $roundOff = (float) ($data['round_off'] ?? 0.00);

            // Prepare line items from quotation or submitted data
            $rawItems = $data['items'] ?? [];
            if (empty($rawItems)) {
                foreach ($quotation->details as $detail) {
                    $rawItems[] = [
                        'product_id' => $detail->product_id,
                        'uom_id' => $detail->uom_id,
                        'allocated_item_id' => null,
                        'product_code' => $detail->product->product_code ?? 'PROD',
                        'product_name' => $detail->product_name ?? $detail->product->name,
                        'item_type' => SalesDetail::ITEM_UNALLOCATED,
                        'quantity' => (float) $detail->qty,
                        'rate' => (float) $detail->rate,
                        'discount_type' => 2,
                        'discount_value' => 0.00,
                        'discount_amount' => 0.00,
                        'tax_percentage' => (float) $detail->tax_percent,
                    ];
                }
            }

            // Calculate GST & invoice totals
            $totals = $this->calculateTotals($rawItems, $gstType, $invoiceDiscount, $roundOff);

            // Generate invoice numbers
            $companyId = $quotation->branch->company_id ?? 1;
            $branchId = $quotation->branch_id;

            // Validate Stock Availability via InventoryService before creating financial invoice
            $this->inventoryService->checkAvailability($totals['items'], $branchId);

            $invoiceNo = $this->generateNextInvoiceNo($companyId, $branchId);
            $invoiceNoDisplay = $this->formatInvoiceNoDisplay($invoiceNo, $branchId);

            // 1. Create Sales Header Record
            $sale = Sale::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'counter_id' => $quotation->counter_id,
                'quotation_id' => $quotation->id,
                'customer_id' => $quotation->customer_id,
                'sales_person_id' => $userId,
                'invoice_no' => $invoiceNo,
                'invoice_no_display' => $invoiceNoDisplay,
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'gst_type' => $gstType,
                'subtotal' => $totals['subtotal'],
                'item_discount' => $totals['item_discount'],
                'invoice_discount' => $totals['invoice_discount'],
                'cgst_amount' => $totals['cgst_amount'],
                'sgst_amount' => $totals['sgst_amount'],
                'igst_amount' => $totals['igst_amount'],
                'tax_amount' => $totals['tax_amount'],
                'round_off' => $totals['round_off'],
                'grand_total' => $totals['grand_total'],
                'sale_type' => $saleType,
                'due_date' => $data['due_date'] ?? null,
                'status' => Sale::STATUS_COMPLETED,
                'remarks' => $data['remarks'] ?? "Converted from Quotation #{$quotation->quotation_no}",
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // 2. Create Sales Details
            foreach ($totals['items'] as $item) {
                SalesDetail::create([
                    'sales_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'uom_id' => $item['uom_id'],
                    'allocated_item_id' => $item['allocated_item_id'] ?? null,
                    'product_code' => $item['product_code'],
                    'product_name' => $item['product_name'],
                    'item_type' => $item['item_type'] ?? SalesDetail::ITEM_UNALLOCATED,
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'discount_type' => $item['discount_type'] ?? 2,
                    'discount_value' => $item['discount_value'] ?? 0.00,
                    'discount_amount' => $item['discount_amount'],
                    'tax_percentage' => $item['tax_percentage'] ?? 0.00,
                    'cgst_percentage' => $item['cgst_percentage'] ?? 0.00,
                    'cgst_amount' => $item['cgst_amount'],
                    'sgst_percentage' => $item['sgst_percentage'] ?? 0.00,
                    'sgst_amount' => $item['sgst_amount'],
                    'igst_percentage' => $item['igst_percentage'] ?? 0.00,
                    'igst_amount' => $item['igst_amount'],
                    'tax_amount' => $item['tax_amount'],
                    'line_total' => $item['line_total'],
                ]);
            }

            // 3. Create Billing Snapshot
            $this->createInvoiceSnapshot($sale, $data['snapshot_overrides'] ?? []);

            // 4. Handle Receivable / Payment Workflow
            if ($sale->isCreditSale()) {
                $this->createReceivable($sale);
            } elseif ($sale->isCashSale() && !empty($data['payment_mode_id']) && !empty($data['paid_amount'])) {
                $this->paymentService->createPayment($sale, [
                    'payment_mode_id' => $data['payment_mode_id'],
                    'payment_date' => $sale->invoice_date->format('Y-m-d'),
                    'amount' => (float) $data['paid_amount'],
                    'reference_no' => $data['reference_no'] ?? null,
                    'remarks' => 'Cash sale payment during quotation conversion',
                ]);
            }

            // 5. Reduce Inventory Stock via InventoryService
            $this->inventoryService->reduceStock($sale);

            // 6. Update Quotation Status to Converted
            $quotation->update([
                'status' => Quotation::STATUS_CONVERTED,
                'updated_by' => $userId,
            ]);

            return $sale;
        });
    }

    /**
     * Get detailed sale invoice payload for display/viewing.
     *
     * @param Sale $sale
     * @return Sale
     */
    public function getShowData(Sale $sale): Sale
    {
        return $sale->load([
            'customer',
            'branch.company',
            'counter',
            'salesPerson',
            'details.product',
            'details.uom',
            'details.allocatedItem',
            'salesPayments.paymentMode',
            'customerReceivable.paymentAllocations',
            'salesInvoiceSnapshot',
            'creator',
            'updater',
            'cancelledBy',
        ]);
    }

    /**
     * Cancel a completed sale invoice.
     *
     * @param Sale $sale
     * @param array $data
     * @return Sale
     */
    public function cancelSale(Sale $sale, array $data = []): Sale
    {
        return DB::transaction(function () use ($sale, $data) {
            if ($sale->isCancelled()) {
                throw new InvalidArgumentException("Sale invoice #{$sale->invoice_no_display} is already cancelled.");
            }

            $userId = Auth::id() ?? $sale->created_by;

            // Cancel payments
            foreach ($sale->salesPayments as $payment) {
                if (!$payment->isCancelled()) {
                    $this->paymentService->cancelPayment($payment, $userId, $data['cancel_reason'] ?? 'Sales invoice cancellation');
                }
            }

            // Cancel receivable if credit sale
            if ($sale->customerReceivable) {
                $this->receivableService->cancelReceivable($sale->customerReceivable);
            }

            // Reverse Inventory Stock Movement
            $this->inventoryService->reverseStock($sale);

            $sale->update([
                'status' => Sale::STATUS_CANCELLED,
                'cancelled_by' => $userId,
                'cancelled_at' => now(),
                'cancel_reason' => $data['cancel_reason'] ?? 'Manual cancellation',
                'cancel_remarks' => $data['cancel_remarks'] ?? null,
                'updated_by' => $userId,
            ]);

            return $sale;
        });
    }

    /**
     * Create Sale header record (standalone/direct sales).
     */
    public function createSale(array $data): Sale
    {
        // Reuses convert / standalone logic
        return $this->convertQuotationToSale(
            new Quotation(),
            $data
        );
    }

    /**
     * Calculate totals using TaxCalculationService.
     */
    public function calculateTotals(
        array $items,
        int $gstType = Sale::GST_CGST_SGST,
        float $invoiceDiscount = 0.00,
        float $roundOff = 0.00
    ): array {
        return $this->taxService->calculateTax($items, $gstType, $invoiceDiscount, $roundOff);
    }

    /**
     * Create billing-time snapshot of Customer, Company, Branch, and Tax details.
     */
    public function createInvoiceSnapshot(Sale $sale, array $overrides = []): SalesInvoiceSnapshot
    {
        $sale->loadMissing(['customer', 'company', 'branch']);

        $customer = $sale->customer;
        $company = $sale->company;
        $branch = $sale->branch;

        $userId = Auth::id() ?? $sale->created_by;

        return SalesInvoiceSnapshot::create([
            'sales_id' => $sale->id,

            // Customer Snapshot
            'customer_name' => $overrides['customer_name'] ?? ($customer->customer_name ?? 'Walk-in Customer'),
            'customer_mobile' => $overrides['customer_mobile'] ?? ($customer->mobile ?? '0000000000'),
            'customer_email' => $overrides['customer_email'] ?? ($customer->email ?? null),
            'customer_address' => $overrides['customer_address'] ?? ($customer->address ?? null),
            'customer_gst_number' => $overrides['customer_gst_number'] ?? ($customer->gstin ?? $customer->gst_number ?? null),

            // Company Snapshot
            'company_name' => $overrides['company_name'] ?? ($company->company_name ?? 'NovaAdmin ERP'),
            'company_gst_number' => $overrides['company_gst_number'] ?? ($company->gst_number ?? null),
            'company_address' => $overrides['company_address'] ?? ($company->address ?? null),

            // Branch Snapshot
            'branch_name' => $overrides['branch_name'] ?? ($branch->branch_name ?? 'Main Branch'),
            'branch_gst_number' => $overrides['branch_gst_number'] ?? ($branch->gst_number ?? null),
            'branch_address' => $overrides['branch_address'] ?? ($branch->address ?? null),

            // Tax & Notes Snapshot
            'gst_type' => $sale->gst_type,
            'notes' => $overrides['notes'] ?? $sale->remarks,

            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }

    /**
     * Delegate CustomerReceivable creation for Credit Sales.
     */
    public function createReceivable(Sale $sale): CustomerReceivable
    {
        return $this->receivableService->createReceivable($sale);
    }

    /**
     * Generate dynamic next sequence number for invoices.
     */
    protected function generateNextInvoiceNo(int $companyId, int $branchId): int
    {
        $maxNo = Sale::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->max('invoice_no');

        return ($maxNo ? (int) $maxNo : 0) + 1;
    }

    /**
     * Format displayable invoice number using branch prefix settings.
     */
    protected function formatInvoiceNoDisplay(int $invoiceNo, ?int $branchId = null): string
    {
        $prefix = SettingService::get('invoice_prefix', 'INV');
        $paddedNo = str_pad((string) $invoiceNo, 5, '0', STR_PAD_LEFT);

        if ($branchId) {
            $branchCode = Branch::where('id', $branchId)->value('branch_code');
            if ($branchCode) {
                return "{$prefix}/{$branchCode}/{$paddedNo}";
            }
        }

        return "{$prefix}-{$paddedNo}";
    }
}
