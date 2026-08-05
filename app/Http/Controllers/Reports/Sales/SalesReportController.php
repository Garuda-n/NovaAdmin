<?php

namespace App\Http\Controllers\Reports\Sales;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Counter;
use App\Models\Customer;
use App\Models\PaymentMode;
use App\Services\Common\DateRangeService;
use App\Services\Common\ReportFilterService;
use App\Services\Reports\SalesReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    protected SalesReportService $salesReportService;
    protected DateRangeService $dateRangeService;
    protected ReportFilterService $filterService;

    public function __construct(
        SalesReportService $salesReportService,
        DateRangeService $dateRangeService,
        ReportFilterService $filterService
    ) {
        $this->salesReportService = $salesReportService;
        $this->dateRangeService = $dateRangeService;
        $this->filterService = $filterService;
    }

    /**
     * Display the sales report page.
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
            'branch_id',
            'counter_id',
            'customer_id',
            'customer_type',
            'payment_mode_id',
            'status',
        ]);
        $filters['preset'] = $dateRange['preset'];
        $filters['from_date'] = $dateRange['from_date']->format('Y-m-d');
        $filters['to_date'] = $dateRange['to_date']->format('Y-m-d');

        $reportData = $this->salesReportService->getReport($filters);
        $reportData['has_searched'] = true;

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('reports.sales._table', compact('reportData'))->render(),
                'reportData' => $reportData,
            ]);
        }

        $branches = Branch::where('status', 1)->orderBy('name')->get();
        $counters = Counter::where('status', 1)->orderBy('counter_name')->get();
        $customers = Customer::where('status', 1)->orderBy('customer_name')->get();
        $paymentModes = PaymentMode::where('status', 1)->orderBy('display_order')->get();

        return view('reports.sales.index', compact(
            'dateRange',
            'presetOptions',
            'reportData',
            'branches',
            'counters',
            'customers',
            'paymentModes'
        ));
    }

    /**
     * Search and filter Sales Report via AJAX.
     */
    public function search(Request $request): JsonResponse
    {
        $dateRange = $this->dateRangeService->resolve(
            $request->input('preset'),
            $request->input('from_date'),
            $request->input('to_date')
        );

        $filters = $this->filterService->sanitize($request, [
            'branch_id',
            'counter_id',
            'customer_id',
            'customer_type',
            'payment_mode_id',
            'status',
        ]);
        $filters['preset'] = $dateRange['preset'];
        $filters['from_date'] = $dateRange['from_date']->format('Y-m-d');
        $filters['to_date'] = $dateRange['to_date']->format('Y-m-d');

        $reportData = $this->salesReportService->getReport($filters);
        $reportData['has_searched'] = true;

        return response()->json([
            'html' => view('reports.sales._table', compact('reportData'))->render(),
            'reportData' => $reportData,
        ]);
    }
}
