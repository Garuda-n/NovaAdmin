<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    protected InventoryService $inventoryService;

    /**
     * InventoryApiController constructor.
     *
     * @param InventoryService $inventoryService
     */
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Search available stock items from inventory.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'search'    => 'required|string|min:2',
            'branch_id' => 'nullable|integer|exists:branches,id',
        ]);

        $search = $request->input('search');
        $branchId = $request->input('branch_id');

        $items = $this->inventoryService->search($search, $branchId);

        return response()->json([
            'status' => true,
            'data'   => $items,
        ]);
    }
}
