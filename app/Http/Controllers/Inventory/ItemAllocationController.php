<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\AllocateItemsRequest;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Counter;
use App\Models\Product;
use App\Models\Size;
use App\Models\StockItem;
use App\Models\StockInwardItem;
use App\Models\SubProduct;
use App\Models\Supplier;
use App\Services\Inventory\ItemAllocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ItemAllocationController extends Controller
{
    protected ItemAllocationService $allocationService;

    public function __construct(ItemAllocationService $allocationService)
    {
        $this->allocationService = $allocationService;
    }

    /**
     * Display a listing of pending individual item allocations across all bulk inwards.
     */
    public function index(Request $request)
    {
        $query = StockInwardItem::with([
            'stockInward.supplier',
            'stockInward.branch',
            'product.category',
            'subProduct'
        ])
        ->whereHas('product', function ($q) {
            $q->where('tracking_type', Product::TRACKING_INDIVIDUAL);
        });

        if ($request->filled('search')) {
            $search = ltrim($request->search, '#');
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                })
                ->orWhereHas('stockInward', function ($sq) use ($search) {
                    $sq->where('invoice_no', 'like', "%{$search}%")
                       ->orWhereHas('supplier', function ($supq) use ($search) {
                           $supq->where('supplier_name', 'like', "%{$search}%");
                       });
                });
            });
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('stockInward', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('supplier_id')) {
            $query->whereHas('stockInward', function ($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $items = $query->latest('id')->paginate(15)->withQueryString();

        // Calculate allocated counts for each line
        $itemIds = $items->pluck('id');
        $allocatedCounts = StockItem::whereIn('stock_inward_item_id', $itemIds)
            ->selectRaw('stock_inward_item_id, count(*) as total')
            ->groupBy('stock_inward_item_id')
            ->pluck('total', 'stock_inward_item_id');

        foreach ($items as $item) {
            $item->allocated_qty = (int) ($allocatedCounts[$item->id] ?? 0);
            $item->pending_qty = max(0, (float) $item->qty - $item->allocated_qty);
        }

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('inventory.item_allocation._table', compact('items'))->render()
            ]);
        }

        $branches = Branch::where('status', 1)->orderBy('name')->get();
        $suppliers = Supplier::where('status', 1)->orderBy('supplier_name')->get();
        $categories = Category::where('status', 1)->orderBy('name')->get();

        return view('inventory.item_allocation.index', compact('items', 'branches', 'suppliers', 'categories'));
    }

    /**
     * Get pending allocation info for a bulk inward detail item.
     */
    public function pendingInfo(StockInwardItem $stockInwardItem): JsonResponse
    {
        $stockInwardItem->load([
            'product.category',
            'product.sizes',
            'product.subProducts',
            'subProduct',
            'stockInward.supplier',
        ]);

        $pendingQty = $this->allocationService->getPendingQuantity($stockInwardItem);
        $allocatedQty = (int) StockItem::where('stock_inward_item_id', $stockInwardItem->id)->count();

        $counters = Counter::where('status', 1)->orderBy('counter_name')->get();
        
        $sizes = $stockInwardItem->product->sizes->count() > 0 
            ? $stockInwardItem->product->sizes 
            : Size::where('status', 1)->orderBy('name')->get();

        $subProducts = $stockInwardItem->product->subProducts->count() > 0
            ? $stockInwardItem->product->subProducts
            : SubProduct::where('status', 1)->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stock_inward_item_id' => $stockInwardItem->id,
                'category_name' => $stockInwardItem->product->category->name ?? 'N/A',
                'product_name' => $stockInwardItem->product->name ?? 'N/A',
                'product_code' => $stockInwardItem->product->code ?? 'N/A',
                'sub_product_name' => $stockInwardItem->subProduct->name ?? null,
                'sub_product_id' => $stockInwardItem->sub_product_id,
                'tracking_type' => $stockInwardItem->product->tracking_type,
                'is_individual_tracking' => ($stockInwardItem->product->tracking_type === Product::TRACKING_INDIVIDUAL),
                'item_generation_mode' => $stockInwardItem->product->item_generation_mode,
                'invoice_no' => $stockInwardItem->stockInward->invoice_no ?? '—',
                'invoice_date' => $stockInwardItem->stockInward->invoice_date ? $stockInwardItem->stockInward->invoice_date->format('d M Y') : '—',
                'supplier_name' => $stockInwardItem->stockInward->supplier->supplier_name ?? '—',
                'received_qty' => (float) $stockInwardItem->qty,
                'allocated_qty' => $allocatedQty,
                'pending_qty' => $pendingQty,
                'is_completed' => ($pendingQty <= 0),
                'counters' => $counters->map(fn($c) => ['id' => $c->id, 'name' => $c->counter_name]),
                'sizes' => $sizes->map(fn($s) => ['id' => $s->id, 'name' => $s->name]),
                'sub_products' => $subProducts->map(fn($sp) => ['id' => $sp->id, 'name' => $sp->name, 'code' => $sp->code]),
            ],
        ]);
    }

    /**
     * Process item allocation request.
     */
    public function store(AllocateItemsRequest $request)
    {
        try {
            $result = $this->allocationService->allocateItems($request->validated());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully allocated {$result['allocated_count']} item(s).",
                    'data' => $result,
                ]);
            }

            return redirect()
                ->back()
                ->with('success', "Successfully allocated {$result['allocated_count']} item(s).");

        } catch (ValidationException $e) {
            $message = 'Pending quantity has changed. Please refresh and try again.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()->withInput()->with('error', $message);
        } catch (\Exception $e) {
            $message = 'Allocation failed: ' . $e->getMessage();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $message);
        }
    }
}
