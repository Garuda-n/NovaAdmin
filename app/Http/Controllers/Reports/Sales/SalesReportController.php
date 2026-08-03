<?php

namespace App\Http\Controllers\Reports\Sales;

use App\Http\Controllers\Controller;
use App\Services\Common\DateRangeService;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    protected DateRangeService $dateRangeService;

    public function __construct(DateRangeService $dateRangeService)
    {
        $this->dateRangeService = $dateRangeService;
    }

    /**
     * Display the sales report page.
     */
    public function index(Request $request)
    {
        $dateRange     = $this->dateRangeService->resolve(
            $request->input('preset'),
            $request->input('from_date'),
            $request->input('to_date')
        );
        $presetOptions = $this->dateRangeService->getPresetOptions();

        return view('reports.sales.index', compact('dateRange', 'presetOptions'));
    }
}
