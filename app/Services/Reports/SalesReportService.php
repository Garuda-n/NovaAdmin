<?php

namespace App\Services\Reports;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    /**
     * Get sales report data based on applied filters.
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
        $counterId = !empty($filters['counter_id']) ? (int) $filters['counter_id'] : null;
        $customerId = !empty($filters['customer_id']) ? (int) $filters['customer_id'] : null;
        $customerType = !empty($filters['customer_type']) ? $filters['customer_type'] : null;
        $paymentModeId = !empty($filters['payment_mode_id']) ? (int) $filters['payment_mode_id'] : null;
        $status = isset($filters['status']) && $filters['status'] !== '' ? (int) $filters['status'] : null;

        $query = Sale::with(['customer', 'branch', 'counter', 'payments.paymentMode'])
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($counterId) {
            $query->where('counter_id', $counterId);
        }

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($customerType) {
            $query->whereHas('customer', function ($cq) use ($customerType) {
                $cq->where('customer_type', $customerType);
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($paymentModeId) {
            $query->whereHas('payments', function ($pq) use ($paymentModeId) {
                $pq->where('payment_mode_id', $paymentModeId);
            });
        }

        $query->orderBy('invoice_date', 'desc')->orderBy('id', 'desc');

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
     * Calculate global header summary KPIs across all sales matching filters.
     *
     * @param array $filters
     * @return array
     */
    protected function calculateReportSummary(array $filters): array
    {
        $fromDate = $filters['from_date'] ?? now()->startOfMonth()->toDateString();
        $toDate = $filters['to_date'] ?? now()->toDateString();
        $branchId = !empty($filters['branch_id']) ? (int) $filters['branch_id'] : null;
        $counterId = !empty($filters['counter_id']) ? (int) $filters['counter_id'] : null;
        $customerId = !empty($filters['customer_id']) ? (int) $filters['customer_id'] : null;
        $customerType = !empty($filters['customer_type']) ? $filters['customer_type'] : null;
        $paymentModeId = !empty($filters['payment_mode_id']) ? (int) $filters['payment_mode_id'] : null;
        $status = isset($filters['status']) && $filters['status'] !== '' ? (int) $filters['status'] : null;

        $query = DB::table('sales as s')
            ->whereDate('s.invoice_date', '>=', $fromDate)
            ->whereDate('s.invoice_date', '<=', $toDate);

        if ($branchId) {
            $query->where('s.branch_id', $branchId);
        }

        if ($counterId) {
            $query->where('s.counter_id', $counterId);
        }

        if ($customerId) {
            $query->where('s.customer_id', $customerId);
        }

        if ($customerType) {
            $query->whereExists(function ($sub) use ($customerType) {
                $sub->select(DB::raw(1))
                    ->from('customers as c')
                    ->whereColumn('c.id', 's.customer_id')
                    ->where('c.customer_type', $customerType);
            });
        }

        if ($status !== null) {
            $query->where('s.status', $status);
        }

        if ($paymentModeId) {
            $query->whereExists(function ($sub) use ($paymentModeId) {
                $sub->select(DB::raw(1))
                    ->from('sales_payments as sp')
                    ->whereColumn('sp.sales_id', 's.id')
                    ->where('sp.payment_mode_id', $paymentModeId);
            });
        }

        $summary = $query->select([
            DB::raw("COUNT(s.id) as total_invoices"),
            DB::raw("COALESCE(SUM(CASE WHEN s.status = 1 THEN 1 ELSE 0 END), 0) as completed_invoices"),
            DB::raw("COALESCE(SUM(CASE WHEN s.status = 2 THEN 1 ELSE 0 END), 0) as cancelled_invoices"),
            DB::raw("COALESCE(SUM(CASE WHEN s.status = 1 THEN s.grand_total ELSE 0 END), 0.00) as gross_revenue"),
            DB::raw("COALESCE(SUM(CASE WHEN s.status = 1 THEN s.tax_amount ELSE 0 END), 0.00) as total_tax"),
            DB::raw("COALESCE(SUM(CASE WHEN s.status = 1 THEN (s.item_discount + s.invoice_discount) ELSE 0 END), 0.00) as total_discount"),
        ])->first();

        $completedInvoices = (int) ($summary->completed_invoices ?? 0);
        $grossRevenue = (float) ($summary->gross_revenue ?? 0.00);
        $avgTicketSize = $completedInvoices > 0 ? ($grossRevenue / $completedInvoices) : 0.00;

        return [
            'total_invoices'     => (int) ($summary->total_invoices ?? 0),
            'completed_invoices' => $completedInvoices,
            'cancelled_invoices' => (int) ($summary->cancelled_invoices ?? 0),
            'gross_revenue'      => $grossRevenue,
            'total_tax'          => (float) ($summary->total_tax ?? 0.00),
            'total_discount'     => (float) ($summary->total_discount ?? 0.00),
            'avg_ticket_size'    => $avgTicketSize,
        ];
    }
}
