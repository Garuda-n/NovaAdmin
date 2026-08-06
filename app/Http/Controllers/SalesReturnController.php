<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesReturnRequest;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\PaymentMode;
use App\Models\SalesReturnDetail;
use App\Services\Sales\SalesReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Exception;

class SalesReturnController extends Controller
{
    protected SalesReturnService $returnService;

    public function __construct(SalesReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    /**
     * Fetch completed invoices for AJAX customer selection.
     */
    public function getInvoices(int $customerId)
    {
        $invoices = Sale::where('customer_id', $customerId)
            ->where('status', Sale::STATUS_COMPLETED)
            ->orderBy('id', 'desc')
            ->get(['id', 'invoice_no_display', 'invoice_date', 'grand_total']);

        return response()->json($invoices);
    }

    /**
     * Fetch detail lines and pro-rata returns history for AJAX selection.
     */
    public function getInvoiceDetails(int $saleId)
    {
        $sale = Sale::with(['salesDetails.product.category'])->findOrFail($saleId);

        $details = $sale->salesDetails->map(function ($detail) use ($sale) {
            $returnedQty = (float) SalesReturnDetail::whereHas('salesReturn', function ($q) use ($sale) {
                $q->where('sales_id', $sale->id)
                  ->where('status', SalesReturn::STATUS_COMPLETED);
            })->where('sales_detail_id', $detail->id)->sum('returned_quantity');

            $remainingQty = $detail->quantity - $returnedQty;

            return [
                'id' => $detail->id,
                'product_name' => $detail->product_name,
                'product_code' => $detail->product_code,
                'item_type' => $detail->item_type,
                'original_qty' => (float) $detail->quantity,
                'returned_qty' => $returnedQty,
                'remaining_qty' => $remainingQty,
                'rate' => (float) $detail->rate,
                'discount_amount' => (float) $detail->discount_amount,
                'tax_percentage' => (float) $detail->tax_percentage,
                'cgst_percentage' => (float) $detail->cgst_percentage,
                'sgst_percentage' => (float) $detail->sgst_percentage,
                'igst_percentage' => (float) $detail->igst_percentage,
                'is_returnable' => (bool) ($detail->product->category->is_returnable ?? true),
                'return_window_days' => (int) ($detail->product->category->return_window_days ?? 30),
                'invoice_date' => $sale->invoice_date->toDateString(),
            ];
        });

        return response()->json([
            'sale_type' => $sale->sale_type,
            'gst_type' => $sale->gst_type,
            'subtotal' => (float) $sale->subtotal,
            'invoice_discount' => (float) $sale->invoice_discount,
            'details' => $details
        ]);
    }

    /**
     * Display a listing of sales returns.
     *
     * @param Request $request
     * @return View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Scopes and paginates returns based on request parameters
        $query = SalesReturn::with(['customer', 'sale', 'branch']);

        // Scoped by active user branch if not administrator
        if (!auth()->user()->is_admin && auth()->user()->branch_id) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('return_no_display', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('sale', function ($sq) use ($search) {
                      $sq->where('invoice_no_display', 'like', "%{$search}%");
                  });
            });
        }

        $salesReturns = $query->orderBy('id', 'desc')->paginate(20);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('sales_returns._table', compact('salesReturns'))->render()
            ]);
        }

        return view('sales_returns.index', compact('salesReturns'));
    }

    /**
     * Show return composition screen.
     *
     * @param Request $request
     * @param int|null $saleId
     * @return View|RedirectResponse
     */
    public function create(Request $request, ?int $saleId = null)
    {
        $sale = null;
        $paymentModes = [];

        if ($saleId) {
            $sale = Sale::with(['salesDetails.product.category', 'customer'])->find($saleId);

            if (!$sale) {
                return redirect()->route('sales-returns.index')->with('error', 'Original sales invoice not found.');
            }

            if ($sale->status !== Sale::STATUS_COMPLETED) {
                return redirect()->route('sales-returns.index')->with('error', 'Sales invoice is not in completed state.');
            }

            // Expose active payment modes for cash refunds
            if ($sale->isCashSale()) {
                $paymentModes = PaymentMode::where('status', 1)->get();
            }
        }

        return view('sales_returns.create', compact('sale', 'paymentModes'));
    }

    /**
     * Commit return transaction.
     *
     * @param StoreSalesReturnRequest $request
     * @return RedirectResponse
     */
    public function store(StoreSalesReturnRequest $request): RedirectResponse
    {
        try {
            $salesReturn = $this->returnService->createReturn($request->validated(), auth()->id());

            return redirect()
                ->route('sales-returns.show', $salesReturn->id)
                ->with('success', "Sales Return #{$salesReturn->return_no_display} has been successfully completed and restocked.");
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Failed to complete sales return: " . $e->getMessage());
        }
    }

    /**
     * Show return invoice page.
     *
     * @param SalesReturn $salesReturn
     * @return View
     */
    public function show(SalesReturn $salesReturn): View
    {
        $salesReturn->load([
            'company',
            'branch',
            'counter',
            'customer',
            'sale',
            'salesReturnDetails.product.category',
            'salesPayments.paymentMode'
        ]);

        return view('sales_returns.show', compact('salesReturn'));
    }
}
