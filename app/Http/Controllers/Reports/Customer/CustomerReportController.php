<?php

namespace App\Http\Controllers\Reports\Customer;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use App\Services\Common\DateRangeService;
use App\Services\Common\ReportFilterService;
use App\Services\Reports\CustomerReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerReportController extends Controller
{
    protected CustomerReportService $customerReportService;
    protected DateRangeService $dateRangeService;
    protected ReportFilterService $filterService;

    public function __construct(
        CustomerReportService $customerReportService,
        DateRangeService $dateRangeService,
        ReportFilterService $filterService
    ) {
        $this->customerReportService = $customerReportService;
        $this->dateRangeService = $dateRangeService;
        $this->filterService = $filterService;
    }

    /**
     * Display the customer report page.
     */
    public function index(Request $request)
    {
        $dateRange = $this->dateRangeService->resolve(
            $request->input('preset'),
            $request->input('from_date'),
            $request->input('to_date')
        );
        $presetOptions = $this->dateRangeService->getPresetOptions();

        $filters = $this->filterService->sanitize($request, [
            'customer_type',
            'state_id',
            'city_id',
            'search_text',
            'status',
        ]);
        $filters['preset'] = $dateRange['preset'];
        $filters['from_date'] = $dateRange['from_date']->format('Y-m-d');
        $filters['to_date'] = $dateRange['to_date']->format('Y-m-d');

        $reportData = $this->customerReportService->getReport($filters);
        $reportData['has_searched'] = true;

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('reports.customer._table', compact('reportData'))->render(),
                'reportData' => $reportData,
            ]);
        }

        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('reports.customer.index', compact(
            'dateRange',
            'presetOptions',
            'reportData',
            'states',
            'cities'
        ));
    }

    /**
     * Search and filter Customer Report via AJAX.
     */
    public function search(Request $request): JsonResponse
    {
        $dateRange = $this->dateRangeService->resolve(
            $request->input('preset'),
            $request->input('from_date'),
            $request->input('to_date')
        );

        $filters = $this->filterService->sanitize($request, [
            'customer_type',
            'state_id',
            'city_id',
            'search_text',
            'status',
        ]);
        $filters['preset'] = $dateRange['preset'];
        $filters['from_date'] = $dateRange['from_date']->format('Y-m-d');
        $filters['to_date'] = $dateRange['to_date']->format('Y-m-d');

        $reportData = $this->customerReportService->getReport($filters);
        $reportData['has_searched'] = true;

        return response()->json([
            'html' => view('reports.customer._table', compact('reportData'))->render(),
            'reportData' => $reportData,
        ]);
    }

    /**
     * Fetch customer sales modal content via AJAX.
     */
    public function salesModal(\App\Models\Customer $customer, Request $request): JsonResponse
    {
        $sales = \App\Models\Sale::with(['branch', 'counter', 'payments.paymentMode'])
            ->where('customer_id', $customer->id)
            ->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalRevenue = $sales->where('status', \App\Models\Sale::STATUS_COMPLETED)->sum('grand_total');
        $completedCount = $sales->where('status', \App\Models\Sale::STATUS_COMPLETED)->count();
        $totalTax = $sales->where('status', \App\Models\Sale::STATUS_COMPLETED)->sum('tax_amount');
        $totalDiscount = $sales->where('status', \App\Models\Sale::STATUS_COMPLETED)->sum(function ($s) {
            return ($s->item_discount ?? 0) + ($s->invoice_discount ?? 0);
        });

        $html = view('reports.customer._sales_modal', compact(
            'customer',
            'sales',
            'totalRevenue',
            'completedCount',
            'totalTax',
            'totalDiscount'
        ))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'customer_name' => $customer->customer_name,
        ]);
    }
}
