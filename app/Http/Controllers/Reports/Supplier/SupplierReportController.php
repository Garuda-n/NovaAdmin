<?php

namespace App\Http\Controllers\Reports\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\City;
use App\Models\State;
use App\Models\StockInward;
use App\Models\Supplier;
use App\Services\Common\DateRangeService;
use App\Services\Common\ReportFilterService;
use App\Services\Reports\SupplierReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierReportController extends Controller
{
    protected SupplierReportService $supplierReportService;
    protected DateRangeService $dateRangeService;
    protected ReportFilterService $filterService;

    public function __construct(
        SupplierReportService $supplierReportService,
        DateRangeService $dateRangeService,
        ReportFilterService $filterService
    ) {
        $this->supplierReportService = $supplierReportService;
        $this->dateRangeService = $dateRangeService;
        $this->filterService = $filterService;
    }

    /**
     * Display the supplier report page.
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
            'supplier_type',
            'state_id',
            'city_id',
            'search_text',
            'status',
        ]);
        $filters['preset'] = $dateRange['preset'];
        $filters['from_date'] = $dateRange['from_date']->format('Y-m-d');
        $filters['to_date'] = $dateRange['to_date']->format('Y-m-d');

        $reportData = $this->supplierReportService->getReport($filters);
        $reportData['has_searched'] = true;

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('reports.supplier._table', compact('reportData'))->render(),
                'reportData' => $reportData,
            ]);
        }

        $branches = Branch::where('status', 1)->orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $supplierTypes = Supplier::SUPPLIER_TYPES;

        return view('reports.supplier.index', compact(
            'dateRange',
            'presetOptions',
            'reportData',
            'branches',
            'states',
            'cities',
            'supplierTypes'
        ));
    }

    /**
     * Search and filter Supplier Report via AJAX.
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
            'supplier_type',
            'state_id',
            'city_id',
            'search_text',
            'status',
        ]);
        $filters['preset'] = $dateRange['preset'];
        $filters['from_date'] = $dateRange['from_date']->format('Y-m-d');
        $filters['to_date'] = $dateRange['to_date']->format('Y-m-d');

        $reportData = $this->supplierReportService->getReport($filters);
        $reportData['has_searched'] = true;

        return response()->json([
            'html' => view('reports.supplier._table', compact('reportData'))->render(),
            'reportData' => $reportData,
        ]);
    }

    /**
     * Fetch supplier inward history modal content via AJAX.
     */
    public function inwardsModal(Supplier $supplier, Request $request): JsonResponse
    {
        $inwards = StockInward::with(['branch', 'counter', 'creator', 'items.product'])
            ->where('supplier_id', $supplier->id)
            ->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalInwardsCount = $inwards->count();
        $totalQtyProcured = 0;
        $totalProcurementValue = 0.00;

        foreach ($inwards as $inward) {
            foreach ($inward->items as $item) {
                $qty = (float) ($item->qty ?? 0);
                $price = (float) ($item->purchase_price ?? 0.00);
                $totalQtyProcured += $qty;
                $totalProcurementValue += ($qty * $price);
            }
        }

        $html = view('reports.supplier._inwards_modal', compact(
            'supplier',
            'inwards',
            'totalInwardsCount',
            'totalQtyProcured',
            'totalProcurementValue'
        ))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'supplier_name' => $supplier->supplier_name,
        ]);
    }
}
