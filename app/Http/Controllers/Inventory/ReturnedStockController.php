<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Enums\StockItemStatus;
use App\Models\SalesReturnDetail;
use App\Services\Inventory\ReturnedInventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Exception;

class ReturnedStockController extends Controller
{
    protected ReturnedInventoryService $inventoryService;

    public function __construct(ReturnedInventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display the Returned Stock Register.
     *
     * @param Request $request
     * @return View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Query stock items in RETURNED status
        $query = StockItem::where('status', StockItemStatus::RETURNED->value)
            ->with([
                'product.category',
                'branch',
                'counter',
                'salesReturnAsOriginal.salesReturn'
            ]);

        // Scoped by branch if not admin
        if (!auth()->user()->is_admin && auth()->user()->branch_id) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        // Apply filters requested by the SFS
        if ($request->filled('company_id')) {
            $query->whereHas('branch', function ($q) use ($request) {
                $q->where('company_id', $request->get('company_id'));
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->get('branch_id'));
        }

        if ($request->filled('counter_id')) {
            $query->where('counter_id', $request->get('counter_id'));
        }

        if ($request->filled('category_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->get('category_id'));
            });
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->get('product_id'));
        }

        if ($request->filled('item_code')) {
            $query->where('item_code', 'like', "%{$request->get('item_code')}%");
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('salesReturnAsOriginal.salesReturn', function ($q) use ($request) {
                $q->whereBetween('return_date', [$request->get('start_date'), $request->get('end_date')]);
            });
        }

        // Current status filter: Pending vs Recreated (checks recreated_stock_item_id)
        if ($request->filled('current_status')) {
            $status = $request->get('current_status');
            if ($status === 'pending') {
                $query->whereHas('salesReturnAsOriginal', function ($q) {
                    $q->whereNull('recreated_stock_item_id');
                });
            } elseif ($status === 'recreated') {
                $query->whereHas('salesReturnAsOriginal', function ($q) {
                    $q->whereNotNull('recreated_stock_item_id');
                });
            }
        }

        $stockItems = $query->orderBy('id', 'desc')->paginate(20);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('inventory.returned_stock._table', compact('stockItems'))->render()
            ]);
        }

        return view('inventory.returned_stock.index', compact('stockItems'));
    }

    /**
     * Recreate inventory for returned serialized stock unit.
     *
     * @param Request $request
     * @param int $salesReturnDetailId
     * @return RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function recreate(Request $request, int $salesReturnDetailId)
    {
        try {
            $recreatedItem = $this->inventoryService->recreate($salesReturnDetailId, auth()->id());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Stock item successfully recreated with code '{$recreatedItem->item_code}'.",
                    'item' => $recreatedItem
                ]);
            }

            return redirect()
                ->back()
                ->with('success', "Stock item successfully recreated with code '{$recreatedItem->item_code}'.");
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->validator->errors()->first()
                ], 422);
            }
            return redirect()->back()->with('error', $e->validator->errors()->first());
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', "Failed to recreate stock item: " . $e->getMessage());
        }
    }
}
