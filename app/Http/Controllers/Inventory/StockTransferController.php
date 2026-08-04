<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\StockTransferStatus;
use App\Enums\StockTransferType;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Counter;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Services\ActivityLogService;
use App\Services\Inventory\StockTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockTransferController extends Controller
{
    protected StockTransferService $stockTransferService;

    public function __construct(StockTransferService $stockTransferService)
    {
        $this->stockTransferService = $stockTransferService;
    }

    /**
     * Display a listing of stock transfers.
     */
    public function index(Request $request)
    {
        $query = StockTransfer::with([
            'company',
            'sourceBranch',
            'sourceCounter',
            'destinationBranch',
            'destinationCounter',
            'creator',
        ])
        ->withCount('details')
        ->latest('id');

        if ($request->filled('search')) {
            $search = ltrim($request->search, '#');
            $query->where(function ($q) use ($search) {
                $q->where('transfer_no', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('transfer_type')) {
            $query->where('transfer_type', $request->transfer_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source_branch_id')) {
            $query->where('source_branch_id', $request->source_branch_id);
        }

        if ($request->filled('destination_branch_id')) {
            $query->where('destination_branch_id', $request->destination_branch_id);
        }

        if ($request->filled('source_counter_id')) {
            $query->where('source_counter_id', $request->source_counter_id);
        }

        if ($request->filled('destination_counter_id')) {
            $query->where('destination_counter_id', $request->destination_counter_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('transfer_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('transfer_date', '<=', $request->to_date);
        }

        $transfers = $query->paginate(15)->withQueryString();

        if ($request->ajax() || $request->wantsJson() || $request->isMethod('post') || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('inventory.stock_transfers._table', compact('transfers'))->render()
            ]);
        }

        $companies = Company::where('status', 1)->orderBy('name')->get();
        $branches = Branch::where('status', 1)->orderBy('name')->get();
        $counters = Counter::where('status', 1)->orderBy('counter_name')->get();
        $statuses = StockTransferStatus::cases();
        $transferTypes = StockTransferType::cases();

        return view('inventory.stock_transfers.index', compact(
            'transfers',
            'companies',
            'branches',
            'counters',
            'statuses',
            'transferTypes'
        ));
    }

    /**
     * Show form to create a stock transfer.
     */
    public function create(): View
    {
        $companies = Company::where('status', 1)->orderBy('name')->get();
        $branches = Branch::where('status', 1)->orderBy('name')->get();
        $counters = Counter::where('status', 1)->orderBy('counter_name')->get();
        $defaultCompanyId = $companies->first()?->id;

        return view('inventory.stock_transfers.create', compact(
            'companies',
            'branches',
            'counters',
            'defaultCompanyId'
        ));
    }

    /**
     * Store a newly created stock transfer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id'             => 'required|exists:companies,id',
            'transfer_type'          => 'required|integer|in:1,2',
            'source_branch_id'       => 'required|exists:branches,id',
            'source_counter_id'      => 'nullable|exists:counters,id',
            'destination_branch_id'  => 'required|exists:branches,id',
            'destination_counter_id' => 'nullable|exists:counters,id',
            'transfer_date'          => 'required|date',
            'remarks'                => 'nullable|string|max:1000',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.tracking_type'  => 'required|integer|in:1,2',
            'items.*.stock_item_id'  => 'nullable|exists:stock_items,id',
            'items.*.transferred_qty'=> 'required_if:items.*.tracking_type,1|numeric|min:0.01',
        ]);

        $transfer = $this->stockTransferService->createTransfer($validated);

        ActivityLogService::log('Stock Transfer Created', "Created Transfer #{$transfer->transfer_no}");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Stock Transfer #{$transfer->transfer_no} saved as Draft.",
                'redirect' => route('stock-transfers.show', $transfer->id),
            ]);
        }

        return redirect()->route('stock-transfers.show', $transfer->id)
            ->with('success', "Stock Transfer #{$transfer->transfer_no} created successfully as Draft.");
    }

    /**
     * Display the specified stock transfer.
     */
    public function show(StockTransfer $stockTransfer): View
    {
        $stockTransfer->load([
            'company',
            'sourceBranch',
            'sourceCounter',
            'destinationBranch',
            'destinationCounter',
            'details.product.uom',
            'details.stockItem',
            'creator',
            'approver',
            'dispatcher',
            'receiver',
            'canceller',
        ]);

        return view('inventory.stock_transfers.show', compact('stockTransfer'));
    }

    /**
     * Show form to edit a draft stock transfer.
     */
    public function edit(StockTransfer $stockTransfer)
    {
        if (!$stockTransfer->isDraft()) {
            return redirect()->route('stock-transfers.show', $stockTransfer->id)
                ->with('error', "Only Draft transfers can be edited.");
        }

        $stockTransfer->load(['details.product.uom', 'details.stockItem']);

        $companies = Company::where('status', 1)->orderBy('name')->get();
        $branches = Branch::where('status', 1)->orderBy('name')->get();
        $counters = Counter::where('status', 1)->orderBy('counter_name')->get();

        return view('inventory.stock_transfers.edit', compact(
            'stockTransfer',
            'companies',
            'branches',
            'counters'
        ));
    }

    /**
     * Update the specified draft stock transfer in storage.
     */
    public function update(Request $request, StockTransfer $stockTransfer)
    {
        $validated = $request->validate([
            'company_id'             => 'required|exists:companies,id',
            'transfer_type'          => 'required|integer|in:1,2',
            'source_branch_id'       => 'required|exists:branches,id',
            'source_counter_id'      => 'nullable|exists:counters,id',
            'destination_branch_id'  => 'required|exists:branches,id',
            'destination_counter_id' => 'nullable|exists:counters,id',
            'transfer_date'          => 'required|date',
            'remarks'                => 'nullable|string|max:1000',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.tracking_type'  => 'required|integer|in:1,2',
            'items.*.stock_item_id'  => 'nullable|exists:stock_items,id',
            'items.*.transferred_qty'=> 'required_if:items.*.tracking_type,1|numeric|min:0.01',
        ]);

        $transfer = $this->stockTransferService->updateTransfer($stockTransfer, $validated);

        ActivityLogService::log('Stock Transfer Updated', "Updated Draft Transfer #{$transfer->transfer_no}");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Stock Transfer #{$transfer->transfer_no} updated successfully.",
                'redirect' => route('stock-transfers.show', $transfer->id),
            ]);
        }

        return redirect()->route('stock-transfers.show', $transfer->id)
            ->with('success', "Stock Transfer #{$transfer->transfer_no} updated successfully.");
    }

    /**
     * Dispatch stock transfer.
     */
    public function dispatch(StockTransfer $stockTransfer)
    {
        $transfer = $this->stockTransferService->dispatchTransfer($stockTransfer);

        ActivityLogService::log('Stock Transfer Dispatched', "Dispatched Transfer #{$transfer->transfer_no}");

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Stock Transfer #{$transfer->transfer_no} dispatched successfully.",
                'redirect' => route('stock-transfers.show', $transfer->id),
            ]);
        }

        return redirect()->route('stock-transfers.show', $transfer->id)
            ->with('success', "Stock Transfer #{$transfer->transfer_no} dispatched successfully.");
    }

    /**
     * Show form/modal to receive stock transfer.
     */
    public function receiveForm(StockTransfer $stockTransfer): View
    {
        $stockTransfer->load(['details.product.uom', 'details.stockItem']);

        return view('inventory.stock_transfers.receive', compact('stockTransfer'));
    }

    /**
     * Confirm receipt of stock transfer.
     */
    public function receive(Request $request, StockTransfer $stockTransfer)
    {
        $receiveData = $request->input('receive_data', []);

        $transfer = $this->stockTransferService->receiveTransfer($stockTransfer, $receiveData);

        ActivityLogService::log('Stock Transfer Received', "Received Transfer #{$transfer->transfer_no}");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Stock Transfer #{$transfer->transfer_no} received successfully.",
                'redirect' => route('stock-transfers.show', $transfer->id),
            ]);
        }

        return redirect()->route('stock-transfers.show', $transfer->id)
            ->with('success', "Stock Transfer #{$transfer->transfer_no} received successfully into destination inventory.");
    }

    /**
     * Cancel stock transfer.
     */
    public function cancel(Request $request, StockTransfer $stockTransfer)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $transfer = $this->stockTransferService->cancelTransfer($stockTransfer, $request->cancellation_reason);

        ActivityLogService::log('Stock Transfer Cancelled', "Cancelled Transfer #{$transfer->transfer_no}");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Stock Transfer #{$transfer->transfer_no} cancelled successfully.",
                'redirect' => route('stock-transfers.show', $transfer->id),
            ]);
        }

        return redirect()->route('stock-transfers.show', $transfer->id)
            ->with('success', "Stock Transfer #{$transfer->transfer_no} cancelled successfully.");
    }

    /**
     * Print stock transfer invoice / manifest.
     */
    public function print(StockTransfer $stockTransfer): View
    {
        $stockTransfer->load([
            'company',
            'sourceBranch',
            'sourceCounter',
            'destinationBranch',
            'destinationCounter',
            'details.product.uom',
            'details.stockItem',
            'creator',
            'dispatcher',
            'receiver',
        ]);

        return view('inventory.stock_transfers.print', compact('stockTransfer'));
    }
}
