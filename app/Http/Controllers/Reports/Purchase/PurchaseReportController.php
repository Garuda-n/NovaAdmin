<?php

namespace App\Http\Controllers\Reports\Purchase;

use App\Http\Controllers\Controller;
use App\Services\Common\DateRangeService;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    protected DateRangeService $dateRangeService;

    public function __construct(DateRangeService $dateRangeService)
    {
        $this->dateRangeService = $dateRangeService;
    }

    /**
     * Display the purchase report page.
     */
    public function index(Request $request)
    {
        $dateRange     = $this->dateRangeService->resolve(
            $request->input('preset'),
            $request->input('from_date'),
            $request->input('to_date')
        );
        $presetOptions = $this->dateRangeService->getPresetOptions();

        return view('reports.purchase.index', compact('dateRange', 'presetOptions'));
    }
}
