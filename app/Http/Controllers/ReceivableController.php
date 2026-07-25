<?php

namespace App\Http\Controllers;

use App\Models\CustomerReceivable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceivableController extends Controller
{
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
