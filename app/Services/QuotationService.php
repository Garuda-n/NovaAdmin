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

        // Resolve counter for logged in user or default active counter
        $counter = null;
        if (isset($user->counter_id) && $user->counter_id) {
            $counter = Counter::find($user->counter_id);
        }
        if (!$counter) {
            $counter = Counter::where('status', true)->first();
        }

        // Resolve business date from Day Closing table or default system date
        $businessDate = date('Y-m-d');
        if (Schema::hasTable('day_closings')) {
            $closingDate = DB::table('day_closings')->where('status', 'open')->value('business_date');
            if ($closingDate) {
                $businessDate = $closingDate;
            }
        }

        $customers = Customer::where('status', true)->orderBy('customer_name')->get();
        $products = Product::with(['uom', 'tax'])->where('status', true)->orderBy('name')->get();
        $uoms = Uom::where('status', true)->orderBy('name')->get();

        return [
            'branch' => $branch,
            'counter' => $counter,
            'businessDate' => $businessDate,
            'customers' => $customers,
            'products' => $products,
            'uoms' => $uoms,
        ];
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
            'details.uom'
        ]);

        $branch = $quotation->branch;
        $counter = $quotation->counter;
        $businessDate = $quotation->business_date ? $quotation->business_date->format('Y-m-d') : date('Y-m-d');

        $customers = Customer::where('status', true)->orderBy('customer_name')->get();
        $products = Product::with(['uom', 'tax'])->where('status', true)->orderBy('name')->get();
        $uoms = Uom::where('status', true)->orderBy('name')->get();

        return [
            'quotation' => $quotation,
            'branch' => $branch,
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

            // 3. Generate quotation number using primary id (quotation_no = id)
            $quotation->quotation_no = $quotation->id;
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
            return $quotation->fresh(['customer', 'branch', 'counter', 'details.product', 'details.uom', 'creator']);
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
            // STEP 2: Reject editing if quotation is Converted
            if ($quotation->status == Quotation::STATUS_CONVERTED) {
                throw ValidationException::withMessages([
                    'status' => 'Converted quotations are locked and cannot be edited.',
                ]);
            }

            // STEP 3: Store current quotation state as old_data
            $quotation->load(['customer', 'branch', 'counter', 'details.product', 'details.uom']);
            $oldData = $quotation->toArray();

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
            $this->saveDetails($quotation, $data['items'] ?? []);

            // STEP 7: Calculate and update subtotal, tax_amount, grand_total
            $this->calculateAndUpdateTotals($quotation);

            // STEP 8: Create Quotation Log for audit history
            $newData = $quotation->fresh(['details'])->toArray();
            $this->saveLog($quotation, $oldData, $newData, $userId);

            // STEP 9: Return updated quotation model
            return $quotation->fresh(['customer', 'branch', 'counter', 'details.product', 'details.uom', 'creator', 'updater']);
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
     * @return void
     */
    public function saveDetails(Quotation $quotation, array $items): void
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
                'product_id'   => $product->id,
                'product_name' => $productName,
                'uom_id'       => $uomId,
                'uom_name'     => $uomName,
                'qty'          => $calculatedLine['qty'],
                'rate'         => $calculatedLine['rate'],
                'tax_percent'  => $calculatedLine['tax_percent'],
                'tax_amount'   => $calculatedLine['tax_amount'],
                'line_total'   => $calculatedLine['line_total'],
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
