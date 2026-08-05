<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Sale;
use App\Services\Sales\InvoicePdfService;
use App\Services\Sales\SalesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesController extends Controller
{
    protected SalesService $salesService;
    protected InvoicePdfService $pdfService;

    /**
     * SalesController constructor.
     *
     * @param SalesService $salesService
     * @param InvoicePdfService $pdfService
     */
    public function __construct(SalesService $salesService, InvoicePdfService $pdfService)
    {
        $this->salesService = $salesService;
        $this->pdfService = $pdfService;
    }

    /**
     * Display a paginated listing of sales invoices.
     *
     * @param Request $request
     * @return View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $sales = $this->salesService->getPaginatedSales($request);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('sales._table', compact('sales'))->render()
            ]);
        }

        return view('sales.index', compact('sales'));
    }

    /**
     * Show the conversion preview form for creating a Sales Invoice from a Quotation.
     *
     * @param Quotation $quotation
     * @return View
     */
    public function createFromQuotation(Quotation $quotation): View
    {
        $data = $this->salesService->getCreateFromQuotationData($quotation);

        return view('sales.create', $data);
    }

    /**
     * Convert quotation to sales invoice.
     *
     * @param Request $request
     * @param Quotation $quotation
     * @return RedirectResponse
     */
    public function convert(Request $request, Quotation $quotation): RedirectResponse
    {
        $validated = $request->validate([
            'sale_type'            => 'required|in:1,2',
            'gst_type'             => 'required|in:1,2',
            'due_date'             => 'nullable|required_if:sale_type,2|date|after_or_equal:today',
            'payments'             => 'nullable|array',
            'payments.*.payment_mode_id' => 'required_with:payments|exists:payment_modes,id',
            'payments.*.amount'    => 'required_with:payments|numeric|min:0',
            'payments.*.reference_no' => 'nullable|string|max:100',
            'payment_mode_id'      => 'nullable|exists:payment_modes,id',
            'paid_amount'          => 'nullable|numeric|min:0',
            'reference_no'         => 'nullable|string|max:100',
            'remarks'              => 'nullable|string',
            'invoice_discount'     => 'nullable|numeric|min:0',
            'round_off'            => 'nullable|numeric',
        ]);

        try {
            $sale = $this->salesService->convertQuotationToSale($quotation, $validated);
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Failed to convert quotation: " . $e->getMessage());
        }

        return redirect()
            ->route('sales.show', $sale->id)
            ->with('success', "Quotation #{$quotation->quotation_no} successfully converted to Sales Invoice #{$sale->invoice_no_display}.");
    }

    /**
     * Display the specified sales invoice details.
     *
     * @param Sale $sale
     * @return View
     */
    public function show(Sale $sale): View
    {
        $sale = $this->salesService->getShowData($sale);

        return view('sales.show', compact('sale'));
    }

    /**
     * Render the printable GST Tax Invoice using snapshot data.
     *
     * @param Sale $sale
     * @return View
     */
    public function print(Sale $sale): View
    {
        return $this->pdfService->generateInvoicePdf($sale);
    }

    /**
     * Generate or download Invoice PDF document.
     *
     * @param Sale $sale
     * @return View
     */
    public function downloadPdf(Sale $sale): View
    {
        return $this->pdfService->generateInvoicePdf($sale);
    }

    /**
     * Cancel the specified sales invoice.
     *
     * @param Request $request
     * @param Sale $sale
     * @return RedirectResponse
     */
    public function cancel(Request $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validate([
            'cancel_reason'  => 'required|string|max:255',
            'cancel_remarks' => 'nullable|string',
        ]);

        $this->salesService->cancelSale($sale, $validated);

        return redirect()
            ->route('sales.show', $sale->id)
            ->with('success', "Sales Invoice #{$sale->invoice_no_display} has been cancelled.");
    }
}
