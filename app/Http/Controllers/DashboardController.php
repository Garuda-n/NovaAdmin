<?php

namespace App\Http\Controllers;

use App\Enums\StockItemStatus;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockInward;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display NovaAdmin main analytics dashboard with stock charts.
     */
    public function index(Request $request)
    {
        $businessDate = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('day_closings')) {
            $businessDate = DB::table('day_closings')
                ->where('status', 'open')
                ->value('business_date');
        }
        $businessDate = $businessDate ?: date('Y-m-d');

        $branchId = $request->query('branch_id');
        $branchesListForDropdown = Branch::where('status', 1)->get();

        $totalAvailableStockQuery = StockItem::where('status', StockItemStatus::AVAILABLE->value);
        if ($branchId) {
            $totalAvailableStockQuery->where('branch_id', $branchId);
        }
        $totalAvailableStock = $totalAvailableStockQuery->count();

        $totalStockInwardsQuery = StockInward::whereDate('invoice_date', $businessDate);
        if ($branchId) {
            $totalStockInwardsQuery->where('branch_id', $branchId);
        }
        $totalStockInwards = $totalStockInwardsQuery->count();

        $totalProducts = Product::where('status', 1)->count();
        $totalBranches = Branch::where('status', 1)->count();

        // 1. Stock Status Distribution
        $statusCountsQuery = StockItem::select('status', DB::raw('count(*) as count'));
        if ($branchId) {
            $statusCountsQuery->where('branch_id', $branchId);
        }
        $statusCountsRaw = $statusCountsQuery->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusLabels = [];
        $statusData = [];
        foreach (StockItemStatus::cases() as $case) {
            $count = (int) ($statusCountsRaw[$case->value] ?? 0);
            if ($count > 0 || empty($statusCountsRaw)) {
                $statusLabels[] = $case->label();
                $statusData[] = $count;
            }
        }

        if (empty($statusLabels)) {
            $statusLabels = ['Available', 'Counter Transferred', 'Branch Transferred', 'Reserved', 'Sold', 'Damaged'];
            $statusData = [0, 0, 0, 0, 0, 0];
        }

        // 2. Available Stock by Category
        $categoryStockQuery = DB::table('stock_items')
            ->join('products', 'stock_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('stock_items.status', StockItemStatus::AVAILABLE->value);
        if ($branchId) {
            $categoryStockQuery->where('stock_items.branch_id', $branchId);
        }
        $categoryStockRaw = $categoryStockQuery->select('categories.name as name', DB::raw('count(stock_items.id) as total'))
            ->groupBy('categories.name')
            ->pluck('total', 'name')
            ->toArray();

        $categoryLabels = array_keys($categoryStockRaw);
        $categoryData = array_values($categoryStockRaw);

        if (empty($categoryLabels)) {
            $categoriesList = Category::where('status', 1)->pluck('name')->toArray();
            $categoryLabels = !empty($categoriesList) ? $categoriesList : ['General'];
            $categoryData = array_fill(0, count($categoryLabels), 0);
        }

        // 3. Available Stock by Branch
        $branchStockQuery = DB::table('stock_items')
            ->join('branches', 'stock_items.branch_id', '=', 'branches.id')
            ->where('stock_items.status', StockItemStatus::AVAILABLE->value);
        if ($branchId) {
            $branchStockQuery->where('stock_items.branch_id', $branchId);
        }
        $branchStockRaw = $branchStockQuery->select('branches.name as name', DB::raw('count(stock_items.id) as total'))
            ->groupBy('branches.name')
            ->pluck('total', 'name')
            ->toArray();

        $branchLabels = array_keys($branchStockRaw);
        $branchData = array_values($branchStockRaw);

        if (empty($branchLabels)) {
            $branchesList = Branch::where('status', 1)->pluck('name')->toArray();
            $branchLabels = !empty($branchesList) ? $branchesList : ['Main Branch'];
            $branchData = array_fill(0, count($branchLabels), 0);
        }

        // 4. Recent Stock Inwards for the active business date
        $recentInwardsQuery = StockInward::with(['supplier', 'branch', 'items'])
            ->whereDate('invoice_date', $businessDate);
        if ($branchId) {
            $recentInwardsQuery->where('branch_id', $branchId);
        }
        $recentInwards = $recentInwardsQuery->latest('id')
            ->take(5)
            ->get();

        // 5. CRM Metrics
        $totalCustomers = \App\Models\Customer::count();
        $b2bCustomers = \App\Models\Customer::where('customer_type', 'B2B')->count();
        $b2cCustomers = \App\Models\Customer::where('customer_type', 'B2C')->count();
        $totalSuppliers = \App\Models\Supplier::count();
        $recentCustomers = \App\Models\Customer::latest()->take(5)->get();

        // 6. Sales Metrics for the active business date
        $totalSalesCountQuery = \App\Models\Sale::whereDate('invoice_date', $businessDate);
        if ($branchId) {
            $totalSalesCountQuery->where('branch_id', $branchId);
        }
        $totalSalesCount = $totalSalesCountQuery->count();

        $totalSalesRevenueQuery = \App\Models\Sale::where('status', \App\Models\Sale::STATUS_COMPLETED)
            ->whereDate('invoice_date', $businessDate);
        if ($branchId) {
            $totalSalesRevenueQuery->where('branch_id', $branchId);
        }
        $totalSalesRevenue = (float)$totalSalesRevenueQuery->sum('grand_total');

        $cancelledSalesCountQuery = \App\Models\Sale::where('status', \App\Models\Sale::STATUS_CANCELLED)
            ->whereDate('invoice_date', $businessDate);
        if ($branchId) {
            $cancelledSalesCountQuery->where('branch_id', $branchId);
        }
        $cancelledSalesCount = $cancelledSalesCountQuery->count();

        $avgInvoiceValue = $totalSalesCount > 0 ? $totalSalesRevenue / $totalSalesCount : 0.00;

        $recentSalesQuery = \App\Models\Sale::with(['customer', 'branch'])
            ->whereDate('invoice_date', $businessDate);
        if ($branchId) {
            $recentSalesQuery->where('branch_id', $branchId);
        }
        $recentSales = $recentSalesQuery->latest()->take(5)->get();

        // 7. Stock transfers from logs for the active business date
        $recentTransfersQuery = \App\Models\StockItemLog::with(['stockItem.product', 'branch', 'counter'])
            ->whereDate('created_at', $businessDate);
        if ($branchId) {
            $recentTransfersQuery->where('branch_id', $branchId);
        }
        $recentTransfers = $recentTransfersQuery->latest('id')
            ->take(10)
            ->get();

        // 8. Order Management (Quotations) for the active business date
        $totalQuotationsQuery = \App\Models\Quotation::whereDate('business_date', $businessDate);
        if ($branchId) {
            $totalQuotationsQuery->where('branch_id', $branchId);
        }
        $totalQuotations = $totalQuotationsQuery->count();

        $convertedQuotationsQuery = \App\Models\Quotation::where('status', \App\Models\Quotation::STATUS_CONVERTED)
            ->whereDate('business_date', $businessDate);
        if ($branchId) {
            $convertedQuotationsQuery->where('branch_id', $branchId);
        }
        $convertedQuotations = $convertedQuotationsQuery->count();

        $activeQuotationsQuery = \App\Models\Quotation::where('status', \App\Models\Quotation::STATUS_CREATED)
            ->whereDate('business_date', $businessDate);
        if ($branchId) {
            $activeQuotationsQuery->where('branch_id', $branchId);
        }
        $activeQuotations = $activeQuotationsQuery->count();

        $recentQuotationsQuery = \App\Models\Quotation::with(['customer', 'branch'])
            ->whereDate('business_date', $businessDate);
        if ($branchId) {
            $recentQuotationsQuery->where('branch_id', $branchId);
        }
        $recentQuotations = $recentQuotationsQuery->latest()->take(5)->get();

        // 9. Sales Chart Data (Monthly)
        $monthlySalesQuery = \App\Models\Sale::where('status', \App\Models\Sale::STATUS_COMPLETED);
        if ($branchId) {
            $monthlySalesQuery->where('branch_id', $branchId);
        }
        $monthlySalesRaw = $monthlySalesQuery->select(
                DB::raw("DATE_FORMAT(invoice_date, '%Y-%m') as month"),
                DB::raw('SUM(grand_total) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();
        
        if (empty($monthlySalesRaw)) {
            $monthlySalesLabels = [date('Y-m')];
            $monthlySalesData = [0];
        } else {
            $monthlySalesLabels = array_keys($monthlySalesRaw);
            $monthlySalesData = array_values($monthlySalesRaw);
        }

        // 10. Contract Pricing Products Reference
        $contractProducts = Product::with(['uom', 'tax'])->where('status', 1)->take(10)->get();

        // 11. Gross Profit calculations for the active business date
        $salesDetails = \App\Models\SalesDetail::whereHas('sale', function($q) use ($businessDate, $branchId) {
            $q->where('status', \App\Models\Sale::STATUS_COMPLETED)
              ->whereDate('invoice_date', $businessDate);
            if ($branchId) {
                $q->where('branch_id', $branchId);
            }
        })->get();

        $totalRevenue = 0.00;
        $totalCogs = 0.00;

        foreach ($salesDetails as $detail) {
            $lineRevenue = (float)$detail->line_total - (float)$detail->tax_amount;
            $totalRevenue += $lineRevenue;

            $costPrice = 0.00;
            if ($detail->allocated_item_id) {
                $stockItem = \App\Models\StockItem::with('stockInwardItem')->find($detail->allocated_item_id);
                if ($stockItem && $stockItem->stockInwardItem) {
                    $costPrice = (float)$stockItem->stockInwardItem->purchase_price;
                }
            }
            if ($costPrice <= 0) {
                $costPrice = (float)\App\Models\StockInwardItem::where('product_id', $detail->product_id)
                    ->latest('id')
                    ->value('purchase_price') ?? 0.00;
            }

            $totalCogs += $costPrice * (float)$detail->quantity;
        }

        $grossProfit = $totalRevenue - $totalCogs;
        $grossProfitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0.00;

        // 12. Purchase Analytics for the active business date
        $totalPurchaseValueQuery = \App\Models\StockInwardItem::whereHas('stockInward', function($q) use ($businessDate, $branchId) {
            $q->whereDate('invoice_date', $businessDate);
            if ($branchId) {
                $q->where('branch_id', $branchId);
            }
        });
        $totalPurchaseValue = (float)$totalPurchaseValueQuery->select(DB::raw('SUM(qty * purchase_price) as total'))->value('total') ?? 0.00;

        // 13. Top Selling Products for the active business date
        $topProductsQuery = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->leftJoin('uoms as u', 'p.uom_id', '=', 'u.id')
            ->where('s.status', \App\Models\Sale::STATUS_COMPLETED)
            ->whereDate('s.invoice_date', $businessDate);
        if ($branchId) {
            $topProductsQuery->where('s.branch_id', $branchId);
        }
        $topSellingProducts = $topProductsQuery->select(
                'p.id as product_id',
                'p.name as product_name',
                'p.code as product_code',
                'u.name as uom_name',
                DB::raw('SUM(sd.quantity) as total_qty'),
                DB::raw('SUM(sd.line_total) as total_amount')
            )
            ->groupBy('p.id', 'p.name', 'p.code', 'u.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 14. Payment Mode Breakdown for the active business date
        $paymentModeQuery = DB::table('sales_payments as sp')
            ->join('payment_modes as pm', 'sp.payment_mode_id', '=', 'pm.id')
            ->join('sales as s', 'sp.sales_id', '=', 's.id')
            ->where('sp.status', \App\Models\SalesPayment::STATUS_COMPLETED)
            ->where('s.status', \App\Models\Sale::STATUS_COMPLETED)
            ->whereDate('s.invoice_date', $businessDate);
        if ($branchId) {
            $paymentModeQuery->where('s.branch_id', $branchId);
        }
        $paymentModeData = $paymentModeQuery->select(
                'pm.mode_name',
                'pm.mode_type',
                DB::raw('SUM(sp.amount) as total_amount')
            )
            ->groupBy('pm.id', 'pm.mode_name', 'pm.mode_type')
            ->orderByDesc('total_amount')
            ->get();

        return view('dashboard', compact(
            'businessDate',
            'branchesListForDropdown',
            'totalAvailableStock',
            'totalStockInwards',
            'totalProducts',
            'totalBranches',
            'statusLabels',
            'statusData',
            'categoryLabels',
            'categoryData',
            'branchLabels',
            'branchData',
            'recentInwards',
            'totalCustomers',
            'b2bCustomers',
            'b2cCustomers',
            'totalSuppliers',
            'recentCustomers',
            'totalSalesCount',
            'totalSalesRevenue',
            'cancelledSalesCount',
            'avgInvoiceValue',
            'recentSales',
            'recentTransfers',
            'totalQuotations',
            'convertedQuotations',
            'activeQuotations',
            'recentQuotations',
            'monthlySalesLabels',
            'monthlySalesData',
            'contractProducts',
            'totalRevenue',
            'totalCogs',
            'grossProfit',
            'grossProfitMargin',
            'totalPurchaseValue',
            'topSellingProducts',
            'paymentModeData'
        ));
    }
}
