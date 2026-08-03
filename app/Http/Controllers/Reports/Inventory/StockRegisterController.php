<?php

namespace App\Http\Controllers\Reports\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Counter;
use App\Models\Product;
use App\Services\Common\DateRangeService;
use App\Services\Common\ReportFilterService;
use App\Services\Reports\StockRegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockRegisterController extends Controller
{
    protected StockRegisterService $stockRegisterService;
    protected DateRangeService $dateRangeService;
    protected ReportFilterService $filterService;

    public function __construct(
        StockRegisterService $stockRegisterService,
        DateRangeService $dateRangeService,
        ReportFilterService $filterService
    ) {
        $this->stockRegisterService = $stockRegisterService;
        $this->dateRangeService = $dateRangeService;
        $this->filterService = $filterService;
    }

    /**
     * Display the Stock Register report page.
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
            'company_id',
            'branch_id',
            'counter_id',
            'category_id',
            'product_id',
            'show_zero_stock',
        ]);
        $filters['preset'] = $dateRange['preset'];
        $filters['from_date'] = $dateRange['from_date']->format('Y-m-d');
        $filters['to_date'] = $dateRange['to_date']->format('Y-m-d');

        $hasSearched = $request->has('search') || $request->boolean('search') || $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if ($hasSearched) {
            $reportData = $this->stockRegisterService->getReport($filters);
            $reportData['has_searched'] = true;
        } else {
            $reportData = [
                'items' => [],
                'summary' => [],
                'paginator' => null,
                'has_searched' => false,
            ];
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('reports.inventory._table', compact('reportData'))->render(),
                'reportData' => $reportData,
            ]);
        }

        $companies  = Company::where('status', 1)->orderBy('name')->get();
        $branches   = Branch::where('status', 1)->orderBy('name')->get();
        $counters   = Counter::where('status', 1)->orderBy('counter_name')->get();
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $products   = Product::where('status', 1)->orderBy('name')->get();

        return view('reports.inventory.index', compact(
            'dateRange',
            'presetOptions',
            'reportData',
            'companies',
            'branches',
            'counters',
            'categories',
            'products'
        ));
    }

    /**
     * Search and filter Stock Register report via AJAX.
     */
    public function search(Request $request): JsonResponse
    {
        $dateRange = $this->dateRangeService->resolve(
            $request->input('preset'),
            $request->input('from_date'),
            $request->input('to_date')
        );

        $filters = $this->filterService->sanitize($request, [
            'company_id',
            'branch_id',
            'counter_id',
            'category_id',
            'product_id',
            'show_zero_stock',
        ]);
        $filters['preset'] = $dateRange['preset'];
        $filters['from_date'] = $dateRange['from_date']->format('Y-m-d');
        $filters['to_date'] = $dateRange['to_date']->format('Y-m-d');

        $reportData = $this->stockRegisterService->getReport($filters);
        $reportData['has_searched'] = true;

        return response()->json([
            'html' => view('reports.inventory._table', compact('reportData'))->render(),
            'reportData' => $reportData,
        ]);
    }

    /**
     * Display the Allocated Item History report page.
     */
    public function allocatedItemHistory(Request $request)
    {
        $itemCode = trim($request->input('item_code', ''));
        $hasSearched = !empty($itemCode);

        $historyData = $hasSearched
            ? $this->stockRegisterService->getAllocatedItemHistory($itemCode)
            : ['found' => false, 'item_code' => '', 'summary' => null, 'timeline' => collect()];

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('reports.inventory._item_history_timeline', compact('historyData', 'hasSearched'))->render(),
                'historyData' => $historyData,
            ]);
        }

        return view('reports.inventory.allocated_item_history', compact('historyData', 'hasSearched', 'itemCode'));
    }

    /**
     * Search Allocated Item History via AJAX.
     */
    public function searchAllocatedItemHistory(Request $request): JsonResponse
    {
        $itemCode = trim($request->input('item_code', ''));
        $historyData = $this->stockRegisterService->getAllocatedItemHistory($itemCode);
        $hasSearched = true;

        return response()->json([
            'html' => view('reports.inventory._item_history_timeline', compact('historyData', 'hasSearched'))->render(),
            'historyData' => $historyData,
        ]);
    }
}
