<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Counter;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\QuotationLog;
use App\Models\Uom;
use App\Services\PricingService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class QuotationService
{
    protected PricingService $pricingService;

    /**
     * QuotationService constructor.
     *
     * @param PricingService $pricingService
     */
    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Get paginated quotations list with filter options.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getPaginatedQuotations(Request $request): LengthAwarePaginator
    {
        // Auto-revert any converted quotations whose associated sales invoices were cancelled
        $convertedQuotationIds = Quotation::where('status', Quotation::STATUS_CONVERTED)->pluck('id');
        foreach ($convertedQuotationIds as $qId) {
            $hasActiveSale = \App\Models\Sale::where('quotation_id', $qId)
                ->where('status', '!=', \App\Models\Sale::STATUS_CANCELLED)
                ->exists();

            if (!$hasActiveSale) {
                Quotation::where('id', $qId)->update(['status' => Quotation::STATUS_CREATED]);
            }
        }

        $query = Quotation::with(['customer', 'branch', 'counter', 'creator']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('quotation_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('mobile', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('business_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('business_date', '<=', $request->date_to);
        }

        return $query->latest()->paginate(15)->withQueryString();
    }

    /**
     * Prepare form dependencies for creating a new quotation.
     *
     * @return array
     */
    public function getCreateFormData(): array
    {
        $user = Auth::user();

        // Resolve branch for logged in user or default active branch
        $branch = null;
        if (isset($user->branch_id) && $user->branch_id) {
            $branch = Branch::find($user->branch_id);
        }
        if (!$branch) {
            $branch = Branch::where('status', true)->first();
        }

        // Resolve counter for logged in user (optional)
        $counter = null;
        if (isset($user->counter_id) && $user->counter_id) {
            $counter = Counter::find($user->counter_id);
        }

        // Resolve business date from Day Closing table or default system date
        $businessDate = date('Y-m-d');
        if (Schema::hasTable('day_closings')) {
            $closingDate = DB::table('day_closings')->where('status', 'open')->value('business_date');
            if ($closingDate) {
                $businessDate = $closingDate;
            }
        }

        $branches = Branch::where('status', true)->orderBy('name')->get();
        $counters = Counter::with(['branches' => function ($q) {
            $q->where('branch_counters.status', 1);
        }])->where('status', true)->orderBy('counter_name')->get();
        $customers = collect();
        $products = Product::with(['uom', 'tax'])->where('status', true)->orderBy('name')->get();
        $uoms = Uom::where('status', true)->orderBy('name')->get();

        return [
            'branches' => $branches,
            'branch' => $branch,
            'counters' => $counters,
            'counter' => $counter,
            'businessDate' => $businessDate,
            'customers' => $customers,
            'products' => $products,
            'uoms' => $uoms,
        ];
    }

    /**
     * Search customers via AJAX for quotation form dropdown.
     *
     * @param string|null $query
     * @return array
     */
    public function searchCustomers(?string $query = null): array
    {
        $q = trim($query ?? '');
        $customerQuery = Customer::where('status', true);

        if (!empty($q)) {
            $customerQuery->where(function ($sub) use ($q) {
                $sub->where('customer_name', 'like', "%{$q}%")
                    ->orWhere('mobile', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('gst_number', 'like', "%{$q}%")
                    ->orWhere('id', $q);
            });
        }

        $customers = $customerQuery->orderBy('customer_name')->limit(30)->get();

        return $customers->map(function ($cust) {
            $displayText = strtoupper($cust->customer_name) . ($cust->mobile ? '-' . $cust->mobile : '');
            $searchContent = strtolower($cust->customer_name . ' ' . ($cust->mobile ?? '') . ' ' . ($cust->email ?? '') . ' ' . ($cust->gst_number ?? ''));

            return [
                'id' => $cust->id,
                'display' => $displayText,
                'name' => $cust->customer_name,
                'mobile' => $cust->mobile ?? '',
                'search' => $searchContent,
                'type' => $cust->customer_type ?? 'B2C',
            ];
        })->toArray();
    }

    /**
     * Retrieve quotation data for show view.
     *
     * @param Quotation $quotation
     * @return Quotation
     */
    public function getShowData(Quotation $quotation): Quotation
    {
        return $quotation->load([
            'customer',
            'branch',
            'counter',
            'creator',
            'updater',
            'details.product',
            'details.uom',
            'details.stockItem',
            'logs.changedBy'
        ]);
    }

    /**
     * Prepare form dependencies for editing an existing quotation.
     *
     * @param Quotation $quotation
     * @return array
     */
    public function getEditFormData(Quotation $quotation): array
    {
        $quotation->load([
            'customer',
            'branch',
            'counter',
            'creator',
            'updater',
            'details.product',
            'details.uom',
            'details.stockItem'
        ]);

        $branch = $quotation->branch;
        $counter = $quotation->counter;
        $businessDate = $quotation->business_date ? $quotation->business_date->format('Y-m-d') : date('Y-m-d');

        $branches = Branch::where('status', true)->orderBy('name')->get();
        $counters = Counter::with(['branches' => function ($q) {
            $q->where('branch_counters.status', 1);
        }])->where('status', true)->orderBy('counter_name')->get();
        $customers = Customer::where('status', true)->orderBy('customer_name')->get();
        $products = Product::with(['uom', 'tax'])->where('status', true)->orderBy('name')->get();
        $uoms = Uom::where('status', true)->orderBy('name')->get();

        return [
            'quotation' => $quotation,
            'branches' => $branches,
            'branch' => $branch,
            'counters' => $counters,
            'counter' => $counter,
            'businessDate' => $businessDate,
            'customers' => $customers,
            'products' => $products,
            'uoms' => $uoms,
        ];
    }

    /**
     * Store a new quotation and its line item details in a database transaction.
     *
     * @param array $data
     * @return Quotation
     * @throws ValidationException
     */
    public function store(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            // 1. Business date validation from day_closings table
            $businessDate = $this->getActiveBusinessDate();

            $userId = Auth::id();

            // 2. Create quotation header (Status: Created=1, quotation_no: NULL initially)
            $quotation = Quotation::create([
                'quotation_no'  => null,
                'business_date' => $businessDate,
                'branch_id'     => $data['branch_id'],
                'counter_id'    => $data['counter_id'],
                'customer_id'   => $data['customer_id'],
                'customer_type' => $data['customer_type'],
                'status'        => Quotation::STATUS_CREATED,
                'subtotal'      => 0.00,
                'tax_amount'    => 0.00,
                'grand_total'   => 0.00,
                'remarks'       => $data['remarks'] ?? null,
                'created_by'    => $userId,
                'updated_by'    => null,
            ]);

            // 3. Generate quotation number based on Business Date (starts from 1, increments daily)
            $maxNo = Quotation::where('business_date', $businessDate)
                ->lockForUpdate()
                ->max('quotation_no') ?? 0;
            $quotation->quotation_no = $maxNo + 1;
            $quotation->save();

            // 4. Save product line items
            $this->saveDetails($quotation, $data['items'] ?? []);

            // 5. Calculate and update subtotal, tax_amount, grand_total
            $this->calculateAndUpdateTotals($quotation);

            // 6. Create Quotation Log
            $this->saveLog(
                $quotation,
                null,
                $quotation->fresh(['details'])->toArray(),
                $userId
            );

            // 7. Commit & return clean quotation model
            return $quotation->fresh(['customer', 'branch', 'counter', 'details.product', 'details.uom', 'details.stockItem', 'creator']);
        });
    }

    /**
     * Update an existing quotation and replace its line item details in a database transaction.
     *
     * @param Quotation $quotation
     * @param array $data
     * @return Quotation
     * @throws ValidationException
     */
    public function update(Quotation $quotation, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $data) {
            // STEP 2: Reject editing if quotation is Converted or Expired
            if ($quotation->status == Quotation::STATUS_CONVERTED) {
                throw ValidationException::withMessages([
                    'status' => 'Converted quotations are locked and cannot be edited.',
                ]);
            }

            if ($quotation->isExpired()) {
                throw ValidationException::withMessages([
                    'quotation' => 'Quotation has expired. Please create a new quotation.',
                ]);
            }

            // STEP 3: Store current quotation state as old_data
            $quotation->load(['customer', 'branch', 'counter', 'details.product', 'details.uom', 'details.stockItem']);
            $oldData = $quotation->toArray();
            $existingStockItemIds = $quotation->details()->pluck('stock_item_id')->filter()->toArray();

            $userId = Auth::id();

            // STEP 4: Update quotation header (Business date, quotation_no, and status remain UNCHANGED)
            $quotation->update([
                'branch_id'     => $data['branch_id'],
                'counter_id'    => $data['counter_id'],
                'customer_id'   => $data['customer_id'],
                'customer_type' => $data['customer_type'],
                'remarks'       => $data['remarks'] ?? null,
                'updated_by'    => $userId,
            ]);

            // STEP 5: Delete existing quotation line item details
            $quotation->details()->delete();

            // STEP 6: Loop request items and create fresh QuotationDetail records
            $this->saveDetails($quotation, $data['items'] ?? [], $existingStockItemIds);

            // STEP 7: Calculate and update subtotal, tax_amount, grand_total
            $this->calculateAndUpdateTotals($quotation);

            // STEP 8: Create Quotation Log for audit history
            $newData = $quotation->fresh(['details'])->toArray();
            $this->saveLog($quotation, $oldData, $newData, $userId);

            // STEP 9: Return updated quotation model
            return $quotation->fresh(['customer', 'branch', 'counter', 'details.product', 'details.uom', 'details.stockItem', 'creator', 'updater']);
        });
    }

    /**
     * Generate unique numeric quotation number based on ID.
     *
     * @param Quotation $quotation
     * @return int
     */
    public function generateQuotationNumber(Quotation $quotation): int
    {
        return (int) $quotation->id;
    }

    /**
     * Save product line item details for a quotation using PricingService.
     *
     * @param Quotation $quotation
     * @param array $items
     * @param array $existingStockItemIds
     * @return void
     */
    public function saveDetails(Quotation $quotation, array $items, array $existingStockItemIds = []): void
    {
        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            if (!$productId) {
                continue;
            }

            $product = Product::with(['uom', 'tax'])->find($productId);
            if (!$product) {
                continue;
            }

            $stockItemId = $item['stock_item_id'] ?? null;

            $branchId = $quotation->branch_id;
            $branchName = $quotation->branch->name ?? "Branch #{$branchId}";

            // If product has individual tracking (tracking_type == 2)
            if ($product->tracking_type == 2) {
                if (!$stockItemId) {
                    throw ValidationException::withMessages([
                        'items' => ["Serial stock item selection missing for product '{$product->name}'."],
                    ]);
                }

                $stockItem = \App\Models\StockItem::with('branch')->find($stockItemId);
                if (!$stockItem) {
                    throw ValidationException::withMessages([
                        'items' => ["Selected stock item ID #{$stockItemId} for product '{$product->name}' does not exist."],
                    ]);
                }

                if ((int) $stockItem->branch_id !== (int) $branchId) {
                    $itemBranchName = $stockItem->branch->name ?? "Branch #{$stockItem->branch_id}";
                    throw ValidationException::withMessages([
                        'items' => ["Selected item '{$stockItem->item_code}' belongs to branch '{$itemBranchName}', but the quotation branch is set to '{$branchName}'. Line items must match the selected branch."],
                    ]);
                }

                $isAvailable = $stockItem->status === \App\Enums\StockItemStatus::AVAILABLE->value;
                $isAlreadySelected = in_array((int) $stockItemId, array_map('intval', $existingStockItemIds), true);

                if (!$isAvailable && !$isAlreadySelected) {
                    $statusLabel = \App\Enums\StockItemStatus::tryFrom($stockItem->status)?->label() ?? 'Unavailable';
                    throw ValidationException::withMessages([
                        'items' => ["Selected stock item '{$stockItem->item_code}' for product '{$product->name}' is no longer available (Status: {$statusLabel}). Please select an available serial item."],
                    ]);
                }
            } elseif ($product->tracking_type == Product::TRACKING_QUANTITY) {
                $requestedQty = (float) ($item['qty'] ?? 1);
                $availableQty = app(\App\Services\Inventory\AvailableStockService::class)->getAvailableQuantity($product->id, $branchId, $quotation->counter_id);
                if ($availableQty < $requestedQty) {
                    throw ValidationException::withMessages([
                        'items' => ["Product '{$product->name}' has insufficient stock in branch '{$branchName}' (Available: {$availableQty}, Requested: {$requestedQty})."],
                    ]);
                }
            }

            $productName = $item['product_name'] ?? $product->name;
            $uomId = $item['uom_id'] ?? $product->uom_id;

            $uomName = $item['uom_name'] ?? '';
            if (empty($uomName) && $uomId) {
                $uomObj = Uom::find($uomId);
                $uomName = $uomObj->name ?? '';
            }

            $qty = (float) ($item['qty'] ?? 1);
            $rate = (float) ($item['rate'] ?? 0);
            $taxPercent = isset($product->tax) ? (float) $product->tax->percentage : 0.00;

            // Calculate line totals using PricingService
            $calculatedLine = $this->pricingService->calculateLine($qty, $rate, $taxPercent);

            $quotation->details()->create([
                'product_id'    => $product->id,
                'stock_item_id' => $stockItemId,
                'product_name'  => $productName,
                'uom_id'        => $uomId,
                'uom_name'      => $uomName,
                'qty'           => $calculatedLine['qty'],
                'rate'          => $calculatedLine['rate'],
                'tax_percent'   => $calculatedLine['tax_percent'],
                'tax_amount'    => $calculatedLine['tax_amount'],
                'line_total'    => $calculatedLine['line_total'],
            ]);
        }
    }

    /**
     * Calculate and update document totals on quotation header using PricingService.
     *
     * @param Quotation $quotation
     * @return void
     */
    public function calculateAndUpdateTotals(Quotation $quotation): void
    {
        $details = $quotation->details()->get();

        $lines = $details->map(function ($detail) {
            return [
                'qty'         => $detail->qty,
                'rate'        => $detail->rate,
                'tax_percent' => $detail->tax_percent,
            ];
        })->toArray();

        $totals = $this->pricingService->calculateTotals($lines);

        $quotation->update([
            'subtotal'    => $totals['subtotal'],
            'tax_amount'  => $totals['tax_amount'],
            'grand_total' => $totals['grand_total'],
        ]);
    }

    /**
     * Create a log entry for quotation creation/updates.
     *
     * @param Quotation $quotation
     * @param array|null $oldData
     * @param array|null $newData
     * @param int $userId
     * @return QuotationLog
     */
    public function saveLog(Quotation $quotation, ?array $oldData, ?array $newData, int $userId): QuotationLog
    {
        return QuotationLog::create([
            'quotation_id' => $quotation->id,
            'old_data'     => $oldData,
            'new_data'     => $newData,
            'changed_by'   => $userId,
        ]);
    }

    /**
     * Validate and retrieve current active business date from day_closings table.
     *
     * @return string
     * @throws ValidationException
     */
    public function getActiveBusinessDate(): string
    {
        if (Schema::hasTable('day_closings')) {
            $businessDate = DB::table('day_closings')
                ->where('status', 'open')
                ->value('business_date');

            if ($businessDate) {
                return $businessDate;
            }

            $hasDayClosings = DB::table('day_closings')->exists();
            if ($hasDayClosings) {
                throw ValidationException::withMessages([
                    'business_date' => 'No active business day found in Day Closing. Please open a business day before creating quotations.',
                ]);
            }
        }

        // Fallback to system date if day_closings table is empty or missing
        return date('Y-m-d');
    }

    /**
     * Method stub for validating business date against Day Closing table.
     *
     * @param string $date
     * @return bool
     */
    public function validateBusinessDate(string $date): bool
    {
        if (Schema::hasTable('day_closings')) {
            return DB::table('day_closings')
                ->where('business_date', $date)
                ->where('status', 'open')
                ->exists();
        }

        return true;
    }

    /**
     * Method stub for preparing PDF export data.
     *
     * @param Quotation $quotation
     * @return array
     */
    public function preparePdf(Quotation $quotation): array
    {
        return [
            'quotation' => $this->getShowData($quotation),
        ];
    }
}
