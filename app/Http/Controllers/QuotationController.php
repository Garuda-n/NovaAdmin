<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Quotation;
use App\Services\QuotationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QuotationController extends Controller
{
    protected QuotationService $quotationService;

    /**
     * QuotationController constructor.
     *
     * @param QuotationService $quotationService
     */
    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    /**
     * Display a listing of the quotations.
     */
    public function index(Request $request)
    {
        $quotations = $this->quotationService->getPaginatedQuotations($request);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('quotations._table', compact('quotations'))->render()
            ]);
        }

        return view('quotations.index', compact('quotations'));
    }

    /**
     * Show the form for creating a new quotation.
     */
    public function create()
    {
        $formData = $this->quotationService->getCreateFormData();

        return view('quotations.create', $formData);
    }

    /**
     * Store a newly created quotation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id'          => 'required|exists:branches,id',
            'counter_id'         => 'required|exists:counters,id',
            'customer_id'        => 'required|exists:customers,id',
            'customer_type'      => 'required|in:B2B,B2C',
            'remarks'            => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|numeric|gt:0',
            'items.*.rate'       => 'required|numeric|min:0',
        ]);

        // Validate GST number for B2B customers
        if ($validated['customer_type'] === 'B2B') {
            $customer = Customer::find($validated['customer_id']);
            if (!$customer || empty(trim($customer->gst_number ?? ''))) {
                throw ValidationException::withMessages([
                    'customer_id' => 'Selected customer is B2B but does not have a registered GST number.',
                ]);
            }
        }

        $quotation = $this->quotationService->store($validated);

        return redirect()
            ->route('quotations.index')
            ->with('success', "Quotation #{$quotation->quotation_no} created successfully.");
    }

    /**
     * Display the specified quotation.
     */
    public function show(Quotation $quotation)
    {
        $quotation = $this->quotationService->getShowData($quotation);

        return view('quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified quotation.
     */
    public function edit(Quotation $quotation)
    {
        $formData = $this->quotationService->getEditFormData($quotation);

        return view('quotations.edit', $formData);
    }

    /**
     * Update the specified quotation in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'branch_id'          => 'required|exists:branches,id',
            'counter_id'         => 'required|exists:counters,id',
            'customer_id'        => 'required|exists:customers,id',
            'customer_type'      => 'required|in:B2B,B2C',
            'remarks'            => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|numeric|gt:0',
            'items.*.rate'       => 'required|numeric|min:0',
        ]);

        // Validate GST number for B2B customers
        if ($validated['customer_type'] === 'B2B') {
            $customer = Customer::find($validated['customer_id']);
            if (!$customer || empty(trim($customer->gst_number ?? ''))) {
                throw ValidationException::withMessages([
                    'customer_id' => 'Selected customer is B2B but does not have a registered GST number.',
                ]);
            }
        }

        $updatedQuotation = $this->quotationService->update($quotation, $validated);

        return redirect()
            ->route('quotations.index')
            ->with('success', "Quotation #{$updatedQuotation->quotation_no} updated successfully.");
    }

    /**
     * Download or view PDF document.
     */
    public function pdf(Quotation $quotation)
    {
        $quotation = $this->quotationService->getShowData($quotation);

        return view('quotations.pdf', compact('quotation'));
    }
}
