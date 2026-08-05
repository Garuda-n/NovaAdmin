<?php

namespace App\Services\Reports;

use App\Models\StockInward;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierReportService
{
    /**
     * Get supplier performance and inward transaction report data based on applied filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function getReport(array $filters, int $perPage = 15): array
    {
        $fromDate = $filters['from_date'] ?? now()->startOfMonth()->toDateString();
        $toDate = $filters['to_date'] ?? now()->toDateString();
        $branchId = !empty($filters['branch_id']) ? (int) $filters['branch_id'] : null;
        $supplierType = !empty($filters['supplier_type']) ? $filters['supplier_type'] : null;
        $stateId = !empty($filters['state_id']) ? (int) $filters['state_id'] : null;
        $cityId = !empty($filters['city_id']) ? (int) $filters['city_id'] : null;
        $search = !empty($filters['search_text']) ? trim($filters['search_text']) : null;
        $status = isset($filters['status']) && $filters['status'] !== '' ? (int) $filters['status'] : null;

        $query = DB::table('suppliers as sup')
            ->select([
                'sup.id as supplier_id',
                'sup.supplier_name',
                'sup.supplier_code',
                'sup.contact_person',
                'sup.mobile',
                'sup.email',
                'sup.supplier_type',
                'sup.gst_number',
                'sup.credit_limit',
                'sup.credit_days',
                'sup.status',
                'st.name as state_name',
                'ct.name as city_name',
                'b.name as branch_name',
                DB::raw("COALESCE(COUNT(DISTINCT si.id), 0) as total_inwards"),
                DB::raw("COALESCE(SUM(sii.qty), 0) as total_qty"),
                DB::raw("COALESCE(SUM(sii.qty * COALESCE(sii.purchase_price, 0)), 0.00) as total_purchase_value"),
                DB::raw("MAX(si.invoice_date) as last_inward_date"),
            ])
            ->leftJoin('states as st', 'sup.state_id', '=', 'st.id')
            ->leftJoin('cities as ct', 'sup.city_id', '=', 'ct.id')
            ->leftJoin('branches as b', 'sup.branch_id', '=', 'b.id')
            ->leftJoin('stock_inwards as si', function ($join) use ($fromDate, $toDate, $branchId) {
                $join->on('sup.id', '=', 'si.supplier_id')
                    ->whereDate('si.invoice_date', '>=', $fromDate)
                    ->whereDate('si.invoice_date', '<=', $toDate);

                if ($branchId) {
                    $join->where('si.branch_id', '=', $branchId);
                }
            })
            ->leftJoin('stock_inward_items as sii', 'si.id', '=', 'sii.stock_inward_id');

        if ($branchId) {
            $query->where(function ($bq) use ($branchId) {
                $bq->where('sup.branch_id', $branchId)
                   ->orWhere('si.branch_id', $branchId);
            });
        }

        if ($supplierType) {
            $query->where('sup.supplier_type', $supplierType);
        }

        if ($stateId) {
            $query->where('sup.state_id', $stateId);
        }

        if ($cityId) {
            $query->where('sup.city_id', $cityId);
        }

        if ($status !== null) {
            $query->where('sup.status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('sup.supplier_name', 'like', "%{$search}%")
                  ->orWhere('sup.supplier_code', 'like', "%{$search}%")
                  ->orWhere('sup.mobile', 'like', "%{$search}%")
                  ->orWhere('sup.gst_number', 'like', "%{$search}%");
            });
        }

        $query->groupBy(
            'sup.id',
            'sup.supplier_name',
            'sup.supplier_code',
            'sup.contact_person',
            'sup.mobile',
            'sup.email',
            'sup.supplier_type',
            'sup.gst_number',
            'sup.credit_limit',
            'sup.credit_days',
            'sup.status',
            'st.name',
            'ct.name',
            'b.name'
        );

        $query->orderByDesc('total_purchase_value')->orderBy('sup.supplier_name', 'asc');

        $paginator = $query->paginate($perPage)->withQueryString();

        $summary = $this->calculateReportSummary($filters);

        return [
            'filters'   => $filters,
            'summary'   => $summary,
            'paginator' => $paginator,
            'items'     => $paginator->items(),
        ];
    }

    /**
     * Calculate header KPI summary values for supplier report.
     *
     * @param array $filters
     * @return array
     */
    protected function calculateReportSummary(array $filters): array
    {
        $fromDate = $filters['from_date'] ?? now()->startOfMonth()->toDateString();
        $toDate = $filters['to_date'] ?? now()->toDateString();
        $branchId = !empty($filters['branch_id']) ? (int) $filters['branch_id'] : null;
        $supplierType = !empty($filters['supplier_type']) ? $filters['supplier_type'] : null;
        $stateId = !empty($filters['state_id']) ? (int) $filters['state_id'] : null;
        $cityId = !empty($filters['city_id']) ? (int) $filters['city_id'] : null;
        $search = !empty($filters['search_text']) ? trim($filters['search_text']) : null;
        $status = isset($filters['status']) && $filters['status'] !== '' ? (int) $filters['status'] : null;

        $supQuery = DB::table('suppliers as sup');

        if ($branchId) {
            $supQuery->where('sup.branch_id', $branchId);
        }

        if ($supplierType) {
            $supQuery->where('sup.supplier_type', $supplierType);
        }

        if ($stateId) {
            $supQuery->where('sup.state_id', $stateId);
        }

        if ($cityId) {
            $supQuery->where('sup.city_id', $cityId);
        }

        if ($status !== null) {
            $supQuery->where('sup.status', $status);
        }

        if ($search) {
            $supQuery->where(function ($q) use ($search) {
                $q->where('sup.supplier_name', 'like', "%{$search}%")
                  ->orWhere('sup.supplier_code', 'like', "%{$search}%")
                  ->orWhere('sup.mobile', 'like', "%{$search}%")
                  ->orWhere('sup.gst_number', 'like', "%{$search}%");
            });
        }

        $totalSuppliers = $supQuery->count();

        // Calculate inward totals within date range for filtered suppliers
        $inwardQuery = DB::table('stock_inwards as si')
            ->join('suppliers as sup', 'si.supplier_id', '=', 'sup.id')
            ->leftJoin('stock_inward_items as sii', 'si.id', '=', 'sii.stock_inward_id')
            ->whereDate('si.invoice_date', '>=', $fromDate)
            ->whereDate('si.invoice_date', '<=', $toDate);

        if ($branchId) {
            $inwardQuery->where('si.branch_id', $branchId);
        }

        if ($supplierType) {
            $inwardQuery->where('sup.supplier_type', $supplierType);
        }

        if ($stateId) {
            $inwardQuery->where('sup.state_id', $stateId);
        }

        if ($cityId) {
            $inwardQuery->where('sup.city_id', $cityId);
        }

        if ($status !== null) {
            $inwardQuery->where('sup.status', $status);
        }

        if ($search) {
            $inwardQuery->where(function ($q) use ($search) {
                $q->where('sup.supplier_name', 'like', "%{$search}%")
                  ->orWhere('sup.supplier_code', 'like', "%{$search}%")
                  ->orWhere('sup.mobile', 'like', "%{$search}%")
                  ->orWhere('sup.gst_number', 'like', "%{$search}%");
            });
        }

        $summaryData = $inwardQuery->select([
            DB::raw("COUNT(DISTINCT si.id) as total_inward_bills"),
            DB::raw("COALESCE(SUM(sii.qty), 0) as total_quantity_procured"),
            DB::raw("COALESCE(SUM(sii.qty * COALESCE(sii.purchase_price, 0)), 0.00) as total_procurement_value"),
        ])->first();

        $totalValue = (float) ($summaryData->total_procurement_value ?? 0.00);
        $avgSpend = $totalSuppliers > 0 ? ($totalValue / $totalSuppliers) : 0.00;

        return [
            'total_suppliers'         => $totalSuppliers,
            'total_inward_bills'      => (int) ($summaryData->total_inward_bills ?? 0),
            'total_quantity_procured' => (float) ($summaryData->total_quantity_procured ?? 0),
            'total_procurement_value' => $totalValue,
            'avg_spend_per_supplier'  => $avgSpend,
        ];
    }
}
