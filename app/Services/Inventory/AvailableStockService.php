<?php

namespace App\Services\Inventory;

use App\Enums\StockItemStatus;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Services\SettingService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AvailableStockService
{
    /**
     * Single reusable method to compute real-time available stock quantity.
     * Consumed by POS Billing, Sales Availability Checks, and Quotations.
     *
     * @param int $productId
     * @param int $branchId
     * @param int|null $counterId
     * @return float
     */
    public function getAvailableQuantity(int $productId, int $branchId, ?int $counterId = null): float
    {
        $product = Product::find($productId);
        if (!$product) {
            return 0.00;
        }

        if ($product->tracking_type == Product::TRACKING_INDIVIDUAL) {
            $query = StockItem::where('product_id', $productId)
                ->where('branch_id', $branchId);

            if ($counterId) {
                $query->where('counter_id', $counterId);
            }

            $reserveSetting = SettingService::get('reserve_stock_on_quotation', false);
            if ($reserveSetting && in_array(strtolower((string) $reserveSetting), ['1', 'true', 'yes'])) {
                $query->where('status', StockItemStatus::AVAILABLE->value);
            } else {
                $query->whereIn('status', [StockItemStatus::AVAILABLE->value, StockItemStatus::RESERVED->value]);
            }

            return (float) $query->count();
        }

        // Quantity-based tracking
        $query = StockMovement::where('product_id', $productId)
            ->where('branch_id', $branchId);

        if ($counterId) {
            $query->where('counter_id', $counterId);
        }

        $netStock = $query->sum('quantity');

        return (float) max(0, $netStock);
    }

    /**
     * Get paginated Available Stock Register data formatted with Opening, Inward, Outward, Closing Qty & Unit.
     *
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function getReportData(array $filters, int $perPage = 15): array
    {
        $fromDate = !empty($filters['from_date']) ? $filters['from_date'] : now()->startOfMonth()->toDateString();
        $toDate = !empty($filters['to_date']) ? $filters['to_date'] : now()->toDateString();

        $branchId = !empty($filters['branch_id']) ? (int) $filters['branch_id'] : null;
        $counterId = !empty($filters['counter_id']) ? (int) $filters['counter_id'] : null;
        $categoryId = !empty($filters['category_id']) ? (int) $filters['category_id'] : null;
        $productId = !empty($filters['product_id']) ? (int) $filters['product_id'] : null;
        $trackingType = !empty($filters['tracking_type']) ? (int) $filters['tracking_type'] : null;
        $search = !empty($filters['search']) ? trim($filters['search']) : null;
        if (empty($search) && !empty($filters['item_code'])) {
            $search = trim($filters['item_code']);
        }

        $reserveSetting = SettingService::get('reserve_stock_on_quotation', false);
        $isReserveEnabled = $reserveSetting && in_array(strtolower((string) $reserveSetting), ['1', 'true', 'yes']);

        // Individual Tracking Products Query (Aggregated per Product with Opening, Inward, Outward, Closing Qty)
        $indivStatuses = $isReserveEnabled
            ? [StockItemStatus::AVAILABLE->value, StockItemStatus::SOLD->value]
            : [StockItemStatus::AVAILABLE->value, StockItemStatus::RESERVED->value, StockItemStatus::SOLD->value];

        $indivQuery = DB::table('products as p')
            ->leftJoin('uoms as u', 'p.uom_id', '=', 'u.id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->leftJoin('stock_items as si', function ($join) use ($branchId, $counterId, $indivStatuses) {
                $join->on('p.id', '=', 'si.product_id')
                    ->whereIn('si.status', $indivStatuses);

                if ($branchId) {
                    $join->where('si.branch_id', '=', $branchId);
                }
                if ($counterId) {
                    $join->where('si.counter_id', '=', $counterId);
                }
            })
            ->select([
                'p.id as product_id',
                'p.code as item_code',
                'p.name as product_name',
                'c.name as category_name',
                'u.name as unit_name',
                'p.tracking_type',
                DB::raw("COALESCE(SUM(CASE WHEN COALESCE(si.allocated_at, si.created_at) < '{$fromDate} 00:00:00' AND (si.status != 5 OR si.updated_at >= '{$fromDate} 00:00:00') THEN 1 ELSE 0 END), 0) as opening_qty"),
                DB::raw("COALESCE(SUM(CASE WHEN COALESCE(si.allocated_at, si.created_at) BETWEEN '{$fromDate} 00:00:00' AND '{$toDate} 23:59:59' THEN 1 ELSE 0 END), 0) as inward_qty"),
                DB::raw("COALESCE(SUM(CASE WHEN si.status = 5 AND si.updated_at BETWEEN '{$fromDate} 00:00:00' AND '{$toDate} 23:59:59' THEN 1 ELSE 0 END), 0) as outward_qty"),
                DB::raw("COALESCE(SUM(CASE WHEN COALESCE(si.allocated_at, si.created_at) <= '{$toDate} 23:59:59' AND (si.status != 5 OR si.updated_at > '{$toDate} 23:59:59') THEN 1 ELSE 0 END), 0) as closing_qty"),
            ])
            ->where('p.status', 1)
            ->where('p.tracking_type', Product::TRACKING_INDIVIDUAL);

        if ($categoryId) {
            $indivQuery->where('p.category_id', $categoryId);
        }
        if ($productId) {
            $indivQuery->where('p.id', $productId);
        }
        if ($search) {
            $indivQuery->where(function ($q) use ($search) {
                $q->where('p.name', 'like', "%{$search}%")
                  ->orWhere('p.code', 'like', "%{$search}%");
            });
        }

        if ($trackingType === Product::TRACKING_QUANTITY) {
            $indivQuery->whereRaw('1 = 0');
        }

        $indivQuery->groupBy('p.id', 'p.code', 'p.name', 'c.name', 'u.name', 'p.tracking_type');

        // Quantity Tracking Products Query (Aggregated per Product with Opening, Inward, Outward, Closing Qty)
        $bulkQuery = DB::table('products as p')
            ->leftJoin('uoms as u', 'p.uom_id', '=', 'u.id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->leftJoin('stock_movements as sm', function ($join) use ($toDate, $branchId, $counterId) {
                $join->on('p.id', '=', 'sm.product_id')
                    ->whereDate('sm.business_date', '<=', $toDate);

                if ($branchId) {
                    $join->where('sm.branch_id', '=', $branchId);
                }
                if ($counterId) {
                    $join->where('sm.counter_id', '=', $counterId);
                }
            })
            ->select([
                'p.id as product_id',
                'p.code as item_code',
                'p.name as product_name',
                'c.name as category_name',
                'u.name as unit_name',
                'p.tracking_type',
                DB::raw("COALESCE(SUM(CASE WHEN sm.business_date < '{$fromDate}' THEN sm.quantity ELSE 0 END), 0) as opening_qty"),
                DB::raw("COALESCE(SUM(CASE WHEN sm.business_date BETWEEN '{$fromDate}' AND '{$toDate}' AND sm.quantity > 0 THEN sm.quantity ELSE 0 END), 0) as inward_qty"),
                DB::raw("COALESCE(SUM(CASE WHEN sm.business_date BETWEEN '{$fromDate}' AND '{$toDate}' AND sm.quantity < 0 THEN ABS(sm.quantity) ELSE 0 END), 0) as outward_qty"),
                DB::raw("COALESCE(SUM(CASE WHEN sm.business_date <= '{$toDate}' THEN sm.quantity ELSE 0 END), 0) as closing_qty"),
            ])
            ->where('p.status', 1)
            ->where('p.tracking_type', Product::TRACKING_QUANTITY);

        if ($categoryId) {
            $bulkQuery->where('p.category_id', $categoryId);
        }
        if ($productId) {
            $bulkQuery->where('p.id', $productId);
        }
        if ($search) {
            $bulkQuery->where(function ($q) use ($search) {
                $q->where('p.name', 'like', "%{$search}%")
                  ->orWhere('p.code', 'like', "%{$search}%");
            });
        }

        if ($trackingType === Product::TRACKING_INDIVIDUAL) {
            $bulkQuery->whereRaw('1 = 0');
        }

        $bulkQuery->groupBy('p.id', 'p.code', 'p.name', 'c.name', 'u.name', 'p.tracking_type');

        // Union Query & Paginate
        $unionQuery = $indivQuery->unionAll($bulkQuery);

        $page = (int) request('page', 1);
        $offset = ($page - 1) * $perPage;

        $totalCount = DB::query()->fromSub($unionQuery, 'combined')->count();

        $results = DB::query()
            ->fromSub($unionQuery, 'combined')
            ->orderBy('product_name', 'asc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $paginator = new LengthAwarePaginator(
            $results,
            $totalCount,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Header KPI Totals across all products
        $summaryQuery = DB::query()
            ->fromSub($unionQuery, 'combined_summary')
            ->selectRaw('
                COALESCE(SUM(opening_qty), 0) as total_opening,
                COALESCE(SUM(inward_qty), 0) as total_inward,
                COALESCE(SUM(outward_qty), 0) as total_outward,
                COALESCE(SUM(closing_qty), 0) as total_closing
            ')
            ->first();

        $summary = [
            'from_date'     => $fromDate,
            'to_date'       => $toDate,
            'total_opening' => (float) ($summaryQuery->total_opening ?? 0),
            'total_inward'  => (float) ($summaryQuery->total_inward ?? 0),
            'total_outward' => (float) ($summaryQuery->total_outward ?? 0),
            'total_closing' => (float) ($summaryQuery->total_closing ?? 0),
        ];

        return [
            'items'     => $paginator,
            'summary'   => $summary,
            'filters'   => array_merge(['from_date' => $fromDate, 'to_date' => $toDate], $filters),
        ];
    }

    /**
     * Get list of individual stock_items for serial drill-down modal.
     */
    public function getSerializedItemsDetail(int $productId, ?int $branchId = null, ?int $counterId = null): Collection
    {
        $reserveSetting = SettingService::get('reserve_stock_on_quotation', false);
        $isReserveEnabled = $reserveSetting && in_array(strtolower((string) $reserveSetting), ['1', 'true', 'yes']);

        $statuses = $isReserveEnabled
            ? [StockItemStatus::AVAILABLE->value]
            : [StockItemStatus::AVAILABLE->value, StockItemStatus::RESERVED->value];

        $query = StockItem::with(['branch', 'counter', 'subProduct', 'size', 'stockInwardItem'])
            ->where('product_id', $productId)
            ->whereIn('status', $statuses);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($counterId) {
            $query->where('counter_id', $counterId);
        }

        return $query->latest('allocated_at')->get();
    }
}
