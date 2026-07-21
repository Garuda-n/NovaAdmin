<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\StockItemStatus;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Counter;
use App\Models\Product;
use App\Models\Size;
use App\Models\StockItem;
use App\Models\SubProduct;
use Illuminate\Http\Request;

class AvailableStockController extends Controller
{
    /**
     * Display a listing of available stock items with filtering and server-side pagination.
     */
    public function index(Request $request)
    {
        $query = StockItem::with([
            'branch',
            'counter',
            'product.category',
            'subProduct',
            'size'
        ])
        ->where('status', StockItemStatus::AVAILABLE->value);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('counter_id')) {
            $query->where('counter_id', $request->counter_id);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('sub_product_id')) {
            $query->where('sub_product_id', $request->sub_product_id);
        }

        if ($request->filled('size_id')) {
            $query->where('size_id', $request->size_id);
        }

        if ($request->filled('item_code')) {
            $itemCode = trim($request->item_code);
            $query->where('item_code', 'like', "%{$itemCode}%");
        }

        $items = $query->latest('allocated_at')->latest('id')->paginate(15)->withQueryString();

        if ($request->ajax() || $request->wantsJson() || $request->isMethod('post') || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('inventory.available_stock._table', compact('items'))->render()
            ]);
        }

        $branches = Branch::where('status', 1)->orderBy('name')->get();
        $counters = Counter::where('status', 1)->orderBy('counter_name')->get();
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $products = Product::where('status', 1)->orderBy('name')->get();
        $subProducts = SubProduct::where('status', 1)->orderBy('name')->get();
        $sizes = Size::where('status', 1)->orderBy('name')->get();

        return view('inventory.available_stock.index', compact(
            'items',
            'branches',
            'counters',
            'categories',
            'products',
            'subProducts',
            'sizes'
        ));
    }
}
