<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Counter;
use App\Models\Product;
use App\Models\StockInward;
use App\Models\SubProduct;
use App\Models\Supplier;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockInwardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockInward::with(['company', 'branch', 'counter', 'supplier'])
            ->withCount('items')
            ->latest();

        if ($request->filled('search')) {
            $search = ltrim($request->search, '#');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('supplier_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $stockInwards = $query->paginate(15)->withQueryString();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('inventory.stock_inwards._table', compact('stockInwards'))->render()
            ]);
        }

        $companies = Company::where('status', 1)->orderBy('name')->get();
        $branches = Branch::where('status', 1)->orderBy('name')->get();
        $suppliers = Supplier::where('status', 1)->orderBy('supplier_name')->get();

        return view('inventory.stock_inwards.index', compact('stockInwards', 'companies', 'branches', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::where('status', 1)->orderBy('name')->get();
        $branches = Branch::where('status', 1)->orderBy('name')->get();
        $counters = Counter::where('status', 1)->orderBy('counter_name')->get();
        $suppliers = Supplier::where('status', 1)->orderBy('supplier_name')->get();
        $products = Product::with('subProducts')->where('status', 1)->orderBy('name')->get();
        $subProducts = SubProduct::where('status', 1)->orderBy('name')->get();

        return view('inventory.stock_inwards.create', compact(
            'companies',
            'branches',
            'counters',
            'suppliers',
            'products',
            'subProducts'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'counter_id' => 'nullable|exists:counters,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_no' => 'required|string|max:100',
            'invoice_date' => 'required|date',
            'remarks' => 'nullable|string',
            'status' => 'nullable|boolean',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.sub_product_id' => 'nullable|exists:sub_products,id',
            'items.*.qty' => 'required|numeric|gt:0',
            'items.*.weight' => 'nullable|numeric|min:0',
            'items.*.purchase_price' => 'nullable|numeric|min:0',
            'items.*.selling_price' => 'nullable|numeric|min:0',
            'items.*.mrp' => 'nullable|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $headerData = [
                'company_id' => $validated['company_id'],
                'branch_id' => $validated['branch_id'],
                'counter_id' => !empty($validated['counter_id']) ? $validated['counter_id'] : null,
                'supplier_id' => $validated['supplier_id'],
                'invoice_no' => $validated['invoice_no'],
                'invoice_date' => $validated['invoice_date'],
                'remarks' => $validated['remarks'] ?? null,
                'status' => $request->has('status') ? (bool) $request->status : true,
                'created_by' => auth()->id(),
            ];

            $stockInward = StockInward::create($headerData);

            $itemsData = array_map(function ($item) {
                return [
                    'product_id' => $item['product_id'],
                    'sub_product_id' => !empty($item['sub_product_id']) ? $item['sub_product_id'] : null,
                    'qty' => $item['qty'],
                    'weight' => isset($item['weight']) && $item['weight'] !== '' ? $item['weight'] : null,
                    'purchase_price' => isset($item['purchase_price']) && $item['purchase_price'] !== '' ? $item['purchase_price'] : null,
                    'selling_price' => isset($item['selling_price']) && $item['selling_price'] !== '' ? $item['selling_price'] : null,
                    'mrp' => isset($item['mrp']) && $item['mrp'] !== '' ? $item['mrp'] : null,
                    'remarks' => $item['remarks'] ?? null,
                ];
            }, $validated['items']);

            $stockInward->items()->createMany($itemsData);

            ActivityLogService::log(
                'StockInward',
                'CREATED',
                $stockInward->id,
                "Created Stock Inward Invoice '{$stockInward->invoice_no}'"
            );

            DB::commit();

            return redirect()
                ->route('stock-inwards.index')
                ->with('success', 'Bulk Stock Inward recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while saving stock inward: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockInward $stockInward, Request $request)
    {
        $stockInward->load([
            'company',
            'branch',
            'counter',
            'supplier',
            'items.product.category',
            'items.product.sizes',
            'items.subProduct',
            'creator'
        ]);

        $itemIds = $stockInward->items->pluck('id');
        $allocatedCounts = \App\Models\StockItem::whereIn('stock_inward_item_id', $itemIds)
            ->selectRaw('stock_inward_item_id, count(*) as total')
            ->groupBy('stock_inward_item_id')
            ->pluck('total', 'stock_inward_item_id');

        foreach ($stockInward->items as $item) {
            $item->allocated_qty = (int) ($allocatedCounts[$item->id] ?? 0);
            $item->pending_qty = max(0, (float) $item->qty - $item->allocated_qty);
        }

        $counters = \App\Models\Counter::where('status', 1)->orderBy('counter_name')->get();
        $sizes = \App\Models\Size::where('status', 1)->orderBy('name')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('inventory.stock_inwards._modal_show', compact('stockInward', 'counters', 'sizes'))->render(),
            ]);
        }

        return view('inventory.stock_inwards.show', compact('stockInward', 'counters', 'sizes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockInward $stockInward)
    {
        $stockInward->load(['items']);

        $companies = Company::where('status', 1)->orderBy('name')->get();
        $branches = Branch::where('status', 1)->orderBy('name')->get();
        $counters = Counter::where('status', 1)->orderBy('counter_name')->get();
        $suppliers = Supplier::where('status', 1)->orderBy('supplier_name')->get();
        $products = Product::with('subProducts')->where('status', 1)->orderBy('name')->get();
        $subProducts = SubProduct::where('status', 1)->orderBy('name')->get();

        return view('inventory.stock_inwards.edit', compact(
            'stockInward',
            'companies',
            'branches',
            'counters',
            'suppliers',
            'products',
            'subProducts'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockInward $stockInward)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'counter_id' => 'nullable|exists:counters,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_no' => 'required|string|max:100',
            'invoice_date' => 'required|date',
            'remarks' => 'nullable|string',
            'status' => 'nullable|boolean',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.sub_product_id' => 'nullable|exists:sub_products,id',
            'items.*.qty' => 'required|numeric|gt:0',
            'items.*.weight' => 'nullable|numeric|min:0',
            'items.*.purchase_price' => 'nullable|numeric|min:0',
            'items.*.selling_price' => 'nullable|numeric|min:0',
            'items.*.mrp' => 'nullable|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $headerData = [
                'company_id' => $validated['company_id'],
                'branch_id' => $validated['branch_id'],
                'counter_id' => !empty($validated['counter_id']) ? $validated['counter_id'] : null,
                'supplier_id' => $validated['supplier_id'],
                'invoice_no' => $validated['invoice_no'],
                'invoice_date' => $validated['invoice_date'],
                'remarks' => $validated['remarks'] ?? null,
                'status' => $request->has('status') ? (bool) $request->status : true,
                'updated_by' => auth()->id(),
            ];

            $stockInward->update($headerData);

            // Re-create items
            $stockInward->items()->delete();

            $itemsData = array_map(function ($item) {
                return [
                    'product_id' => $item['product_id'],
                    'sub_product_id' => !empty($item['sub_product_id']) ? $item['sub_product_id'] : null,
                    'qty' => $item['qty'],
                    'weight' => isset($item['weight']) && $item['weight'] !== '' ? $item['weight'] : null,
                    'purchase_price' => isset($item['purchase_price']) && $item['purchase_price'] !== '' ? $item['purchase_price'] : null,
                    'selling_price' => isset($item['selling_price']) && $item['selling_price'] !== '' ? $item['selling_price'] : null,
                    'mrp' => isset($item['mrp']) && $item['mrp'] !== '' ? $item['mrp'] : null,
                    'remarks' => $item['remarks'] ?? null,
                ];
            }, $validated['items']);

            $stockInward->items()->createMany($itemsData);

            ActivityLogService::log(
                'StockInward',
                'UPDATED',
                $stockInward->id,
                "Updated Stock Inward Invoice '{$stockInward->invoice_no}'"
            );

            DB::commit();

            return redirect()
                ->route('stock-inwards.index')
                ->with('success', 'Stock Inward updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating stock inward: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockInward $stockInward)
    {
        DB::beginTransaction();

        try {
            $invoiceNo = $stockInward->invoice_no;

            $stockInward->delete();

            ActivityLogService::log(
                'StockInward',
                'DELETED',
                $stockInward->id,
                "Deleted Stock Inward Invoice '{$invoiceNo}'"
            );

            DB::commit();

            return redirect()
                ->route('stock-inwards.index')
                ->with('success', 'Stock Inward deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Something went wrong while deleting stock inward.');
        }
    }
}
