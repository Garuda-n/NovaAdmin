<?php

namespace App\Services\Reports;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class CustomerReportService
{
    /**
     * Get customer performance and transactions report data based on applied filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function getReport(array $filters, int $perPage = 15): array
    {
        $fromDate = $filters['from_date'] ?? now()->startOfMonth()->toDateString();
        $toDate = $filters['to_date'] ?? now()->toDateString();
        $customerType = !empty($filters['customer_type']) ? $filters['customer_type'] : null;
        $stateId = !empty($filters['state_id']) ? (int) $filters['state_id'] : null;
        $cityId = !empty($filters['city_id']) ? (int) $filters['city_id'] : null;
        $search = !empty($filters['search_text']) ? trim($filters['search_text']) : null;
        $status = isset($filters['status']) && $filters['status'] !== '' ? (int) $filters['status'] : null;

        $query = DB::table('customers as c')
            ->select([
                'c.id as customer_id',
                'c.customer_name',
                'c.mobile',
                'c.email',
                'c.customer_type',
                'c.gst_number',
                'c.status',
                'st.name as state_name',
                'ct.name as city_name',
                DB::raw("COALESCE(COUNT(s.id), 0) as total_invoices"),
                DB::raw("COALESCE(SUM(CASE WHEN s.status = 1 THEN 1 ELSE 0 END), 0) as completed_invoices"),
                DB::raw("COALESCE(SUM(CASE WHEN s.status = 1 THEN s.grand_total ELSE 0 END), 0.00) as total_revenue"),
                DB::raw("COALESCE(SUM(CASE WHEN s.status = 1 THEN s.tax_amount ELSE 0 END), 0.00) as total_tax"),
                DB::raw("COALESCE(SUM(CASE WHEN s.status = 1 THEN (s.item_discount + s.invoice_discount) ELSE 0 END), 0.00) as total_discount"),
                DB::raw("MAX(CASE WHEN s.status = 1 THEN s.invoice_date ELSE NULL END) as last_purchase_date"),
            ])
            ->leftJoin('states as st', 'c.state_id', '=', 'st.id')
            ->leftJoin('cities as ct', 'c.city_id', '=', 'ct.id')
            ->leftJoin('sales as s', function ($join) use ($fromDate, $toDate) {
                $join->on('c.id', '=', 's.customer_id')
                    ->whereDate('s.invoice_date', '>=', $fromDate)
                    ->whereDate('s.invoice_date', '<=', $toDate);
            });

        if ($customerType) {
            $query->where('c.customer_type', $customerType);
        }

        if ($stateId) {
            $query->where('c.state_id', $stateId);
        }

        if ($cityId) {
            $query->where('c.city_id', $cityId);
        }

        if ($status !== null) {
            $query->where('c.status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('c.customer_name', 'like', "%{$search}%")
                  ->orWhere('c.mobile', 'like', "%{$search}%")
                  ->orWhere('c.gst_number', 'like', "%{$search}%");
            });
        }

        $query->groupBy(
            'c.id',
            'c.customer_name',
            'c.mobile',
            'c.email',
            'c.customer_type',
            'c.gst_number',
            'c.status',
            'st.name',
            'ct.name'
        );

        $query->orderByDesc('total_revenue')->orderBy('c.customer_name', 'asc');

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
     * Calculate header KPI summary values for customer report.
     *
     * @param array $filters
     * @return array
     */
    protected function calculateReportSummary(array $filters): array
    {
        $fromDate = $filters['from_date'] ?? now()->startOfMonth()->toDateString();
        $toDate = $filters['to_date'] ?? now()->toDateString();
        $customerType = !empty($filters['customer_type']) ? $filters['customer_type'] : null;
        $stateId = !empty($filters['state_id']) ? (int) $filters['state_id'] : null;
        $cityId = !empty($filters['city_id']) ? (int) $filters['city_id'] : null;
        $search = !empty($filters['search_text']) ? trim($filters['search_text']) : null;
        $status = isset($filters['status']) && $filters['status'] !== '' ? (int) $filters['status'] : null;

        $customerQuery = DB::table('customers as c');

        if ($customerType) {
            $customerQuery->where('c.customer_type', $customerType);
        }

        if ($stateId) {
            $customerQuery->where('c.state_id', $stateId);
        }

        if ($cityId) {
            $customerQuery->where('c.city_id', $cityId);
        }

        if ($status !== null) {
            $customerQuery->where('c.status', $status);
        }

        if ($search) {
            $customerQuery->where(function ($q) use ($search) {
                $q->where('c.customer_name', 'like', "%{$search}%")
                  ->orWhere('c.mobile', 'like', "%{$search}%")
                  ->orWhere('c.gst_number', 'like', "%{$search}%");
            });
        }

        $custSummary = $customerQuery->select([
            DB::raw("COUNT(c.id) as total_customers"),
            DB::raw("COALESCE(SUM(CASE WHEN c.customer_type = 'B2B' THEN 1 ELSE 0 END), 0) as b2b_count"),
            DB::raw("COALESCE(SUM(CASE WHEN c.customer_type = 'B2C' THEN 1 ELSE 0 END), 0) as b2c_count"),
        ])->first();

        // Calculate sales totals within date range for filtered customers
        $salesQuery = DB::table('sales as s')
            ->join('customers as c', 's.customer_id', '=', 'c.id')
            ->whereDate('s.invoice_date', '>=', $fromDate)
            ->whereDate('s.invoice_date', '<=', $toDate)
            ->where('s.status', Sale::STATUS_COMPLETED);

        if ($customerType) {
            $salesQuery->where('c.customer_type', $customerType);
        }

        if ($stateId) {
            $salesQuery->where('c.state_id', $stateId);
        }

        if ($cityId) {
            $salesQuery->where('c.city_id', $cityId);
        }

        if ($status !== null) {
            $salesQuery->where('c.status', $status);
        }

        if ($search) {
            $salesQuery->where(function ($q) use ($search) {
                $q->where('c.customer_name', 'like', "%{$search}%")
                  ->orWhere('c.mobile', 'like', "%{$search}%")
                  ->orWhere('c.gst_number', 'like', "%{$search}%");
            });
        }

        $salesSummary = $salesQuery->select([
            DB::raw("COUNT(s.id) as total_invoices"),
            DB::raw("COALESCE(SUM(s.grand_total), 0.00) as total_revenue"),
            DB::raw("COALESCE(SUM(s.tax_amount), 0.00) as total_tax"),
            DB::raw("COALESCE(SUM(s.item_discount + s.invoice_discount), 0.00) as total_discount"),
        ])->first();

        $totalCustomers = (int) ($custSummary->total_customers ?? 0);
        $totalRevenue = (float) ($salesSummary->total_revenue ?? 0.00);
        $totalInvoices = (int) ($salesSummary->total_invoices ?? 0);
        $avgCustomerSpend = $totalCustomers > 0 ? ($totalRevenue / $totalCustomers) : 0.00;

        return [
            'total_customers'    => $totalCustomers,
            'b2b_count'          => (int) ($custSummary->b2b_count ?? 0),
            'b2c_count'          => (int) ($custSummary->b2c_count ?? 0),
            'total_invoices'     => $totalInvoices,
            'total_revenue'      => $totalRevenue,
            'total_tax'          => (float) ($salesSummary->total_tax ?? 0.00),
            'total_discount'     => (float) ($salesSummary->total_discount ?? 0.00),
            'avg_customer_spend' => $avgCustomerSpend,
        ];
    }
}
