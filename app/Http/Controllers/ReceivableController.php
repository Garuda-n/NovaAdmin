<?php

namespace App\Http\Controllers;

use App\Models\CustomerReceivable;
use App\Models\Sale;
use App\Services\Sales\PaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceivableController extends Controller
{
    /**
     * Collect payment for a customer receivable against a sale.
     *
     * @param Request $request
     * @param Sale $sale
     * @param PaymentService $paymentService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function collectPayment(Request $request, Sale $sale, PaymentService $paymentService)
    {
        $receivable = $sale->customerReceivable;
        if (!$receivable) {
            return redirect()->back()->with('error', 'No outstanding receivable found for this sale.');
        }

        $validated = $request->validate([
            'payment_mode_id' => 'required|exists:payment_modes,id',
            'amount' => 'required|numeric|min:0.01|max:' . $receivable->balance_amount,
            'payment_date' => 'required|date',
            'reference_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            $paymentService->createPayment($sale, [
                'payment_mode_id' => $validated['payment_mode_id'],
                'payment_date' => $validated['payment_date'],
                'amount' => (float)$validated['amount'],
                'reference_no' => $validated['reference_no'],
                'remarks' => $validated['remarks'] ?? 'Receivable payment collection',
            ]);

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Payment collected and allocated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to collect payment: ' . $e->getMessage());
        }
    }
    /**
     * Display a listing of customer receivables.
     *
     * @param Request $request
     * @return View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = CustomerReceivable::with(['customer', 'sale', 'creator']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($cq) use ($search) {
                    $cq->where('customer_name', 'like', "%{$search}%")
                       ->orWhere('mobile', 'like', "%{$search}%");
                })->orWhereHas('sale', function ($sq) use ($search) {
                    $sq->where('invoice_no', 'like', "%{$search}%")
                       ->orWhere('invoice_no_display', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('due_from')) {
            $query->whereDate('due_date', '>=', $request->due_from);
        }

        if ($request->filled('due_to')) {
            $query->whereDate('due_date', '<=', $request->due_to);
        }

        $receivables = $query->latest('id')->paginate(15)->withQueryString();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('receivables._table', compact('receivables'))->render()
            ]);
        }

        return view('receivables.index', compact('receivables'));
    }
}
