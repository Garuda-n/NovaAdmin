<?php

namespace App\Services\Reports;

use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockRegisterService
{
    /**
     * Get stock register report data based on applied filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function getReport(array $filters, int $perPage = 15): array
    {
        $fromDate = $filters['from_date'] ?? now()->startOfMonth()->toDateString();
        $toDate = $filters['to_date'] ?? now()->toDateString();
        $companyId = !empty($filters['company_id']) ? (int) $filters['company_id'] : null;
        $branchId = !empty($filters['branch_id']) ? (int) $filters['branch_id'] : null;
        $counterId = !empty($filters['counter_id']) ? (int) $filters['counter_id'] : null;
        $categoryId = !empty($filters['category_id']) ? (int) $filters['category_id'] : null;
        $productId = !empty($filters['product_id']) ? (int) $filters['product_id'] : null;
        $showZeroStock = isset($filters['show_zero_stock']) ? (int) $filters['show_zero_stock'] : 1;

        // Build single-pass conditional aggregate query
        $query = DB::table('products as p')
            ->select([
                'p.id as product_id',
                'p.code as item_code',
                'p.name as product_name',
                'u.name as uom_name',
                DB::raw("COALESCE(SUM(CASE WHEN sm.business_date < '{$fromDate}' THEN sm.quantity ELSE 0 END), 0) as opening_qty"),
                DB::raw("COALESCE(SUM(CASE WHEN sm.business_date BETWEEN '{$fromDate}' AND '{$toDate}' AND sm.quantity > 0 THEN sm.quantity ELSE 0 END), 0) as inward_qty"),
                DB::raw("COALESCE(SUM(CASE WHEN sm.business_date BETWEEN '{$fromDate}' AND '{$toDate}' AND sm.quantity < 0 THEN ABS(sm.quantity) ELSE 0 END), 0) as outward_qty"),
                DB::raw("COALESCE(SUM(CASE WHEN sm.business_date <= '{$toDate}' THEN sm.quantity ELSE 0 END), 0) as closing_qty"),
            ])
            ->leftJoin('uoms as u', 'p.uom_id', '=', 'u.id')
            ->leftJoin('stock_movements as sm', function ($join) use ($toDate, $companyId, $branchId, $counterId) {
                $join->on('p.id', '=', 'sm.product_id')
                    ->whereDate('sm.business_date', '<=', $toDate);

                if ($companyId) {
                    $join->where('sm.company_id', '=', $companyId);
                }
                if ($branchId) {
                    $join->where('sm.branch_id', '=', $branchId);
                }
                if ($counterId) {
                    $join->where('sm.counter_id', '=', $counterId);
                }
            })
            ->where('p.status', 1);

        if ($categoryId) {
            $query->where('p.category_id', $categoryId);
        }

        if ($productId) {
            $query->where('p.id', $productId);
        }

        $query->groupBy('p.id', 'p.code', 'p.name', 'u.name');

        if ($showZeroStock === 0) {
            $query->havingRaw("opening_qty != 0 OR inward_qty != 0 OR outward_qty != 0 OR closing_qty != 0");
        }

        $query->orderBy('p.name', 'asc');

        $paginator = $query->paginate($perPage)->withQueryString();

        // Calculate header summary KPIs across all matching products
        $summary = $this->calculateReportSummary($filters);

        return [
            'filters'   => $filters,
            'summary'   => $summary,
            'paginator' => $paginator,
            'items'     => $paginator->items(),
        ];
    }

    /**
     * Calculate global header summary totals across all products matching filters.
     *
     * @param array $filters
     * @return array
     */
    protected function calculateReportSummary(array $filters): array
    {
        $fromDate = $filters['from_date'] ?? now()->startOfMonth()->toDateString();
        $toDate = $filters['to_date'] ?? now()->toDateString();
        $companyId = !empty($filters['company_id']) ? (int) $filters['company_id'] : null;
        $branchId = !empty($filters['branch_id']) ? (int) $filters['branch_id'] : null;
        $counterId = !empty($filters['counter_id']) ? (int) $filters['counter_id'] : null;
        $categoryId = !empty($filters['category_id']) ? (int) $filters['category_id'] : null;
        $productId = !empty($filters['product_id']) ? (int) $filters['product_id'] : null;

        $query = DB::table('stock_movements as sm')
            ->join('products as p', 'sm.product_id', '=', 'p.id')
            ->whereDate('sm.business_date', '<=', $toDate)
            ->where('p.status', 1);

        if ($companyId) {
            $query->where('sm.company_id', $companyId);
        }
        if ($branchId) {
            $query->where('sm.branch_id', $branchId);
        }
        if ($counterId) {
            $query->where('sm.counter_id', $counterId);
        }
        if ($categoryId) {
            $query->where('p.category_id', $categoryId);
        }
        if ($productId) {
            $query->where('sm.product_id', $productId);
        }

        $totals = $query->selectRaw("
            COALESCE(SUM(CASE WHEN sm.business_date < ? THEN sm.quantity ELSE 0 END), 0) as total_opening,
            COALESCE(SUM(CASE WHEN sm.business_date BETWEEN ? AND ? AND sm.quantity > 0 THEN sm.quantity ELSE 0 END), 0) as total_inward,
            COALESCE(SUM(CASE WHEN sm.business_date BETWEEN ? AND ? AND sm.quantity < 0 THEN ABS(sm.quantity) ELSE 0 END), 0) as total_outward,
            COALESCE(SUM(CASE WHEN sm.business_date <= ? THEN sm.quantity ELSE 0 END), 0) as total_closing
        ", [$fromDate, $fromDate, $toDate, $fromDate, $toDate, $toDate])->first();

        return [
            'total_opening' => (float) ($totals->total_opening ?? 0),
            'total_inward'  => (float) ($totals->total_inward ?? 0),
            'total_outward' => (float) ($totals->total_outward ?? 0),
            'total_closing' => (float) ($totals->total_closing ?? 0),
        ];
    }

    /**
     * Get total opening stock quantity.
     *
     * @param int|null $productId
     * @param int|null $branchId
     * @param string|null $fromDate
     * @return float
     */
    public function getOpeningQty(?int $productId = null, ?int $branchId = null, ?string $fromDate = null): float
    {
        $fromDate = $fromDate ?? now()->startOfMonth()->toDateString();

        $query = StockMovement::whereDate('business_date', '<', $fromDate);

        if ($productId) {
            $query->where('product_id', $productId);
        }
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return (float) $query->sum('quantity');
    }

    /**
     * Get total inward stock quantity.
     *
     * @param int|null $productId
     * @param int|null $branchId
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return float
     */
    public function getInwardQty(?int $productId = null, ?int $branchId = null, ?string $fromDate = null, ?string $toDate = null): float
    {
        $fromDate = $fromDate ?? now()->startOfMonth()->toDateString();
        $toDate = $toDate ?? now()->toDateString();

        $query = StockMovement::whereDate('business_date', '>=', $fromDate)
            ->whereDate('business_date', '<=', $toDate)
            ->where('quantity', '>', 0);

        if ($productId) {
            $query->where('product_id', $productId);
        }
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return (float) $query->sum('quantity');
    }

    /**
     * Get total outward stock quantity.
     *
     * @param int|null $productId
     * @param int|null $branchId
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return float
     */
    public function getOutwardQty(?int $productId = null, ?int $branchId = null, ?string $fromDate = null, ?string $toDate = null): float
    {
        $fromDate = $fromDate ?? now()->startOfMonth()->toDateString();
        $toDate = $toDate ?? now()->toDateString();

        $query = StockMovement::whereDate('business_date', '>=', $fromDate)
            ->whereDate('business_date', '<=', $toDate)
            ->where('quantity', '<', 0);

        if ($productId) {
            $query->where('product_id', $productId);
        }
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return (float) abs($query->sum('quantity'));
    }

    /**
     * Get total closing stock quantity.
     *
     * @param int|null $productId
     * @param int|null $branchId
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return float
     */
    public function getClosingQty(?int $productId = null, ?int $branchId = null, ?string $fromDate = null, ?string $toDate = null): float
    {
        $toDate = $toDate ?? now()->toDateString();

        $query = StockMovement::whereDate('business_date', '<=', $toDate);

        if ($productId) {
            $query->where('product_id', $productId);
        }
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return (float) $query->sum('quantity');
    }

    /**
     * Get complete lifecycle history for a single allocated item by Item Code.
     *
     * @param string $itemCode
     * @return array
     */
    public function getAllocatedItemHistory(string $itemCode): array
    {
        $itemCode = trim($itemCode);
        if (empty($itemCode)) {
            return [
                'found' => false,
                'item_code' => '',
                'summary' => null,
                'timeline' => collect(),
            ];
        }

        $stockItem = \App\Models\StockItem::with([
            'product',
            'branch',
            'counter',
            'subProduct',
            'size',
            'allocatedBy',
            'stockInward.supplier',
            'stockInwardItem',
            'logs',
        ])
        ->where('item_code', $itemCode)
        ->orWhere('item_code', strtoupper($itemCode))
        ->first();

        if (!$stockItem) {
            return [
                'found' => false,
                'item_code' => $itemCode,
                'summary' => null,
                'timeline' => collect(),
            ];
        }

        // Find if item is sold via SalesDetail
        $salesDetail = \App\Models\SalesDetail::with(['sale.customer', 'sale.branch', 'sale.counter'])
            ->where('allocated_item_id', $stockItem->id)
            ->latest('id')
            ->first();

        $currentOwner = $salesDetail?->sale?->customer?->customer_name ?? null;

        // Map status name
        $statusLabel = match ((int) $stockItem->status) {
            1 => 'Available',
            2 => 'Reserved',
            3 => 'Allocated',
            4 => 'In Transit',
            5 => 'Sold',
            6 => 'Returned',
            default => 'Active (' . $stockItem->status . ')',
        };

        $summary = [
            'stock_item_id'   => $stockItem->id,
            'item_code'       => $stockItem->item_code,
            'product_name'    => $stockItem->product?->name ?? 'N/A',
            'product_code'    => $stockItem->product?->code ?? '',
            'status'          => $statusLabel,
            'status_code'     => (int) $stockItem->status,
            'current_branch'  => $stockItem->branch?->name ?? 'N/A',
            'current_counter' => $stockItem->counter?->counter_name ?? 'N/A',
            'current_owner'   => $currentOwner,
            'created_date'    => $stockItem->allocated_at ?? $stockItem->created_at,
        ];

        // Gather Timeline Events
        $timelineEvents = collect();

        // 1. Bulk Stock Inward Event
        if ($stockItem->stockInward) {
            $inward = $stockItem->stockInward;
            $inwardDate = $inward->invoice_date ? $inward->invoice_date->format('Y-m-d') : ($inward->created_at ? $inward->created_at->format('Y-m-d') : date('Y-m-d'));
            $timelineEvents->push([
                'type'          => 'inward',
                'title'         => 'Stock Inward',
                'icon'          => 'arrow-down-tray',
                'color'         => 'indigo',
                'business_date' => $inwardDate,
                'created_at'    => $inward->created_at ? $inward->created_at->format('Y-m-d H:i:s') : $inwardDate . ' 00:00:00',
                'reference_no'  => $inward->invoice_no ?? ('INW-' . $inward->id),
                'description'   => "Initial stock inward received from supplier " . ($inward->supplier?->supplier_name ?? 'N/A'),
                'metadata'      => [
                    'Supplier'       => $inward->supplier?->supplier_name ?? 'N/A',
                    'Purchase Price' => $stockItem->stockInwardItem?->purchase_price ? ('₹' . number_format($stockItem->stockInwardItem->purchase_price, 2)) : 'N/A',
                    'MRP'            => $stockItem->stockInwardItem?->mrp ? ('₹' . number_format($stockItem->stockInwardItem->mrp, 2)) : 'N/A',
                    'Branch'         => $stockItem->branch?->name ?? 'N/A',
                ],
            ]);
        }

        // 2. Counter Allocation Event
        if ($stockItem->allocated_at || $stockItem->counter_id) {
            $allocDate = $stockItem->allocated_at ? $stockItem->allocated_at->format('Y-m-d') : ($stockItem->created_at ? $stockItem->created_at->format('Y-m-d') : date('Y-m-d'));
            $timelineEvents->push([
                'type'          => 'allocation',
                'title'         => 'Counter Allocation',
                'icon'          => 'building-storefront',
                'color'         => 'purple',
                'business_date' => $allocDate,
                'created_at'    => $stockItem->allocated_at ? $stockItem->allocated_at->format('Y-m-d H:i:s') : ($stockItem->created_at ? $stockItem->created_at->format('Y-m-d H:i:s') : date('Y-m-d H:i:s')),
                'reference_no'  => 'ALLOC-' . $stockItem->id,
                'description'   => "Item allocated to counter " . ($stockItem->counter?->counter_name ?? 'Main Counter'),
                'metadata'      => [
                    'Counter'      => $stockItem->counter?->counter_name ?? 'N/A',
                    'Branch'       => $stockItem->branch?->name ?? 'N/A',
                    'Allocated By' => $stockItem->allocatedBy?->name ?? 'System',
                ],
            ]);
        }

        // 3. Quotation Event
        if ($salesDetail && $salesDetail->sale && $salesDetail->sale->quotation_id) {
            $quotation = \App\Models\Quotation::with('customer')->find($salesDetail->sale->quotation_id);
            if ($quotation) {
                $qDate = $quotation->business_date ?? ($quotation->quotation_date ? $quotation->quotation_date->format('Y-m-d') : ($quotation->created_at ? $quotation->created_at->format('Y-m-d') : date('Y-m-d')));
                $timelineEvents->push([
                    'type'          => 'quotation',
                    'title'         => 'Quotation',
                    'icon'          => 'document-text',
                    'color'         => 'amber',
                    'business_date' => $qDate,
                    'created_at'    => $quotation->created_at ? $quotation->created_at->format('Y-m-d H:i:s') : $qDate . ' 00:00:00',
                    'reference_no'  => 'QUO-' . $quotation->quotation_no,
                    'description'   => "Included in Quotation for customer " . ($quotation->customer?->customer_name ?? 'Walk-in Customer'),
                    'metadata'      => [
                        'Quotation No' => $quotation->quotation_no,
                        'Customer'     => $quotation->customer?->customer_name ?? 'Walk-in Customer',
                        'Customer Type'=> $quotation->customer_type ?? 'B2C',
                    ],
                ]);
            }
        }

        // 4. Sales Invoice Event
        if ($salesDetail && $salesDetail->sale) {
            $sale = $salesDetail->sale;
            $sDate = $sale->business_date ?? ($sale->invoice_date ? $sale->invoice_date->format('Y-m-d') : ($sale->created_at ? $sale->created_at->format('Y-m-d') : date('Y-m-d')));
            $timelineEvents->push([
                'type'          => 'sales',
                'title'         => 'Sales Invoice',
                'icon'          => 'shopping-cart',
                'color'         => 'emerald',
                'business_date' => $sDate,
                'created_at'    => $sale->created_at ? $sale->created_at->format('Y-m-d H:i:s') : $sDate . ' 00:00:00',
                'reference_no'  => $sale->invoice_number ?? ('INV-' . $sale->id),
                'description'   => "Item sold to customer " . ($sale->customer?->customer_name ?? 'Walk-in Customer'),
                'metadata'      => [
                    'Invoice No'  => $sale->invoice_number,
                    'Customer'    => $sale->customer?->customer_name ?? 'Walk-in Customer',
                    'Sale Rate'   => '₹' . number_format($salesDetail->rate, 2),
                    'Branch'      => $sale->branch?->name ?? 'N/A',
                    'Counter'     => $sale->counter?->counter_name ?? 'N/A',
                ],
            ]);
        }

        // 5. Stock Movements (Transfers, Returns, Adjustments, etc.)
        $movements = \App\Models\StockMovement::with(['branch', 'counter'])
            ->where('stock_item_id', $stockItem->id)
            ->get();

        foreach ($movements as $m) {
            if (in_array((int)$m->movement_type, [2, 3])) {
                continue;
            }

            $mType = match ((int)$m->movement_type) {
                4 => ['type' => 'transfer', 'title' => 'Stock Transfer', 'icon' => 'arrows-right-left', 'color' => 'blue'],
                5 => ['type' => 'adjustment', 'title' => 'Stock Adjustment', 'icon' => 'adjustments-horizontal', 'color' => 'slate'],
                6 => ['type' => 'return', 'title' => 'Stock Return', 'icon' => 'arrow-path', 'color' => 'rose'],
                default => ['type' => 'movement', 'title' => 'Stock Movement', 'icon' => 'cube', 'color' => 'gray'],
            };

            $mDate = $m->business_date ?? ($m->movement_date ? $m->movement_date->format('Y-m-d') : ($m->created_at ? $m->created_at->format('Y-m-d') : date('Y-m-d')));

            $timelineEvents->push([
                'type'          => $mType['type'],
                'title'         => $mType['title'],
                'icon'          => $mType['icon'],
                'color'         => $mType['color'],
                'business_date' => $mDate,
                'created_at'    => $m->created_at ? $m->created_at->format('Y-m-d H:i:s') : $mDate . ' 00:00:00',
                'reference_no'  => $m->reference_type ? (basename(str_replace('\\', '/', $m->reference_type)) . ' #' . $m->reference_id) : 'MOV-' . $m->id,
                'description'   => $m->remarks ?? "Stock movement record #{$m->id}",
                'metadata'      => [
                    'Branch'   => $m->branch?->name ?? 'N/A',
                    'Counter'  => $m->counter?->counter_name ?? 'N/A',
                    'Quantity' => $m->quantity,
                    'Remarks'  => $m->remarks ?? 'N/A',
                ],
            ]);
        }

        // Sort by Business Date ASC, then Created At ASC
        $sortedTimeline = $timelineEvents->sortBy(function ($event) {
            $bDate = !empty($event['business_date']) ? $event['business_date'] : '9999-12-31';
            $cDate = !empty($event['created_at']) ? $event['created_at'] : '9999-12-31 23:59:59';
            return $bDate . '_' . $cDate;
        })->values();

        return [
            'found'     => true,
            'item_code' => $stockItem->item_code,
            'summary'   => $summary,
            'timeline'  => $sortedTimeline,
        ];
    }
}
