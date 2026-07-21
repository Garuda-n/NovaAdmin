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
    public function index()
    {
        $totalAvailableStock = StockItem::where('status', StockItemStatus::AVAILABLE->value)->count();
        $totalStockInwards = StockInward::count();
        $totalProducts = Product::where('status', 1)->count();
        $totalBranches = Branch::where('status', 1)->count();

        // 1. Stock Status Distribution
        $statusCountsRaw = StockItem::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
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
        $categoryStockRaw = DB::table('stock_items')
            ->join('products', 'stock_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('stock_items.status', StockItemStatus::AVAILABLE->value)
            ->select('categories.name as name', DB::raw('count(stock_items.id) as total'))
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
        $branchStockRaw = DB::table('stock_items')
            ->join('branches', 'stock_items.branch_id', '=', 'branches.id')
            ->where('stock_items.status', StockItemStatus::AVAILABLE->value)
            ->select('branches.name as name', DB::raw('count(stock_items.id) as total'))
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

        // 4. Recent Stock Inwards
        $recentInwards = StockInward::with(['supplier', 'branch', 'items'])
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard', compact(
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
            'recentInwards'
        ));
    }
}
