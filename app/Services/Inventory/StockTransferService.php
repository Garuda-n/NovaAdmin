<?php

namespace App\Services\Inventory;

use App\Enums\StockItemStatus;
use App\Enums\StockTransferStatus;
use App\Enums\StockTransferType;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\StockTransfer;
use App\Models\StockTransferDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransferService
{
    protected InventoryService $inventoryService;
    protected AvailableStockService $availableStockService;

    public function __construct(
        InventoryService $inventoryService,
        AvailableStockService $availableStockService
    ) {
        $this->inventoryService = $inventoryService;
        $this->availableStockService = $availableStockService;
    }

    /**
     * Generate continuous sequence Transfer Number (ST000001).
     * Does NOT reset daily.
     *
     * @return string
     */
    public function generateTransferNo(): string
    {
        return DB::transaction(function () {
            $lastId = DB::table('stock_transfers')->max('id') ?? 0;
            $nextNumber = $lastId + 1;
            return 'ST' . str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Create a new Stock Transfer (Draft).
     *
     * @param array $data
     * @return StockTransfer
     * @throws ValidationException
     */
    public function createTransfer(array $data): StockTransfer
    {
        return DB::transaction(function () use ($data) {
            $this->validateLocations($data);

            $transferNo = $this->generateTransferNo();

            $transfer = StockTransfer::create([
                'company_id'             => $data['company_id'],
                'transfer_no'            => $transferNo,
                'transfer_type'          => $data['transfer_type'],
                'source_branch_id'       => $data['source_branch_id'],
                'source_counter_id'      => $data['source_counter_id'] ?? null,
                'destination_branch_id'  => $data['destination_branch_id'],
                'destination_counter_id' => $data['destination_counter_id'] ?? null,
                'transfer_date'          => $data['transfer_date'] ?? now()->toDateString(),
                'status'                 => StockTransferStatus::DRAFT->value,
                'remarks'                => $data['remarks'] ?? null,
                'created_by'             => Auth::id() ?? 1,
            ]);

            $this->saveTransferDetails($transfer, $data['items'] ?? []);

            return $transfer->load(['details.product', 'details.stockItem']);
        });
    }

    /**
     * Update an existing Draft Stock Transfer.
     *
     * @param StockTransfer $transfer
     * @param array $data
     * @return StockTransfer
     * @throws ValidationException
     */
    public function updateTransfer(StockTransfer $transfer, array $data): StockTransfer
    {
        if (!$transfer->isDraft()) {
            throw ValidationException::withMessages([
                'transfer' => ["Only Draft transfers can be edited. Current status: {$transfer->status->label()}"],
            ]);
        }

        return DB::transaction(function () use ($transfer, $data) {
            $this->validateLocations($data);

            $transfer->update([
                'company_id'             => $data['company_id'],
                'transfer_type'          => $data['transfer_type'],
                'source_branch_id'       => $data['source_branch_id'],
                'source_counter_id'      => $data['source_counter_id'] ?? null,
                'destination_branch_id'  => $data['destination_branch_id'],
                'destination_counter_id' => $data['destination_counter_id'] ?? null,
                'transfer_date'          => $data['transfer_date'] ?? $transfer->transfer_date,
                'remarks'                => $data['remarks'] ?? $transfer->remarks,
            ]);

            // Replace existing details
            $transfer->details()->delete();
            $this->saveTransferDetails($transfer, $data['items'] ?? []);

            return $transfer->fresh(['details.product', 'details.stockItem']);
        });
    }

    /**
     * Dispatch stock transfer (stock leaves source).
     *
     * @param StockTransfer $transfer
     * @return StockTransfer
     * @throws ValidationException
     */
    public function dispatchTransfer(StockTransfer $transfer): StockTransfer
    {
        if (!$transfer->isDraft()) {
            throw ValidationException::withMessages([
                'transfer' => ["Transfer #{$transfer->transfer_no} is not in Draft status and cannot be dispatched."],
            ]);
        }

        return DB::transaction(function () use ($transfer) {
            $transfer->loadMissing('details');

            if ($transfer->details->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => ["Cannot dispatch transfer with no line items."],
                ]);
            }

            // Verify stock availability prior to dispatch
            $this->validateAvailableStockForDispatch($transfer);

            // Record outward movement from source location via InventoryService
            $this->inventoryService->recordTransferOut($transfer);

            // Transition status to DISPATCHED
            $transfer->update([
                'status'        => StockTransferStatus::DISPATCHED->value,
                'dispatched_by' => Auth::id() ?? 1,
                'dispatched_at' => now(),
            ]);

            return $transfer->fresh();
        });
    }

    /**
     * Confirm receipt of transfer (stock becomes available at destination).
     * Supports optional partial receipts data ($receiveData).
     *
     * @param StockTransfer $transfer
     * @param array $receiveData Optional map of detail_id => ['received_qty' => x, 'damaged_qty' => y]
     * @return StockTransfer
     * @throws ValidationException
     */
    public function receiveTransfer(StockTransfer $transfer, array $receiveData = []): StockTransfer
    {
        if (!$transfer->isDispatched()) {
            throw ValidationException::withMessages([
                'transfer' => ["Transfer #{$transfer->transfer_no} is not in Dispatched status and cannot be received."],
            ]);
        }

        return DB::transaction(function () use ($transfer, $receiveData) {
            $transfer->loadMissing('details');

            foreach ($transfer->details as $detail) {
                $detailId = $detail->id;
                $recvQty = isset($receiveData[$detailId]['received_qty'])
                    ? (float) $receiveData[$detailId]['received_qty']
                    : (float) $detail->transferred_qty;

                $dmgQty = isset($receiveData[$detailId]['damaged_qty'])
                    ? (float) $receiveData[$detailId]['damaged_qty']
                    : 0.00;

                $detail->update([
                    'received_qty' => $recvQty,
                    'damaged_qty'  => $dmgQty,
                ]);
            }

            // Record inward movement to destination location via InventoryService
            $this->inventoryService->recordTransferIn($transfer);

            // Transition status to RECEIVED
            $transfer->update([
                'status'      => StockTransferStatus::RECEIVED->value,
                'received_by' => Auth::id() ?? 1,
                'received_at' => now(),
            ]);

            return $transfer->fresh();
        });
    }

    /**
     * Cancel a Stock Transfer.
     * Reverses source stock movement if already dispatched.
     *
     * @param StockTransfer $transfer
     * @param string|null $reason
     * @return StockTransfer
     * @throws ValidationException
     */
    public function cancelTransfer(StockTransfer $transfer, ?string $reason = null): StockTransfer
    {
        if (!$transfer->isDraft()) {
            throw ValidationException::withMessages([
                'transfer' => ["Dispatched or Received transfer #{$transfer->transfer_no} cannot be cancelled."],
            ]);
        }

        return DB::transaction(function () use ($transfer, $reason) {
            $transfer->update([
                'status'              => StockTransferStatus::CANCELLED->value,
                'cancellation_reason' => $reason,
                'cancelled_by'        => Auth::id() ?? 1,
                'cancelled_at'        => now(),
            ]);

            return $transfer->fresh();
        });
    }

    /**
     * Validate source and destination location requirements.
     *
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    protected function validateLocations(array $data): void
    {
        $transferType = (int) ($data['transfer_type'] ?? StockTransferType::BRANCH->value);
        $srcBranchId = (int) ($data['source_branch_id'] ?? 0);
        $dstBranchId = (int) ($data['destination_branch_id'] ?? 0);
        $srcCounterId = !empty($data['source_counter_id']) ? (int) $data['source_counter_id'] : null;
        $dstCounterId = !empty($data['destination_counter_id']) ? (int) $data['destination_counter_id'] : null;

        if ($transferType === StockTransferType::BRANCH->value) {
            if ($srcBranchId === $dstBranchId) {
                throw ValidationException::withMessages([
                    'destination_branch_id' => ["Source and Destination Branch cannot be identical."],
                ]);
            }
        } elseif ($transferType === StockTransferType::COUNTER->value) {
            if (!$srcCounterId) {
                throw ValidationException::withMessages([
                    'source_counter_id' => ["Source Counter is required for Counter Transfers."],
                ]);
            }
            if (!$dstCounterId) {
                throw ValidationException::withMessages([
                    'destination_counter_id' => ["Destination Counter is required for Counter Transfers."],
                ]);
            }
            if ($srcBranchId === $dstBranchId && $srcCounterId === $dstCounterId) {
                throw ValidationException::withMessages([
                    'destination_counter_id' => ["Source and Destination Counter cannot be identical."],
                ]);
            }
        }
    }

    /**
     * Save line items for a stock transfer.
     *
     * @param StockTransfer $transfer
     * @param array $items
     * @return void
     * @throws ValidationException
     */
    protected function saveTransferDetails(StockTransfer $transfer, array $items): void
    {
        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => ["At least one item is required for transfer."],
            ]);
        }

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $trackingType = (int) ($item['tracking_type'] ?? Product::TRACKING_QUANTITY);
            $stockItemId = !empty($item['stock_item_id']) ? (int) $item['stock_item_id'] : null;
            $itemCode = $item['item_code'] ?? null;
            $qty = $trackingType === Product::TRACKING_INDIVIDUAL ? 1.00 : (float) ($item['transferred_qty'] ?? $item['quantity'] ?? 1);
            $unitCost = isset($item['unit_cost']) ? (float) $item['unit_cost'] : null;

            if ($trackingType === Product::TRACKING_INDIVIDUAL) {
                if (!$stockItemId) {
                    throw ValidationException::withMessages([
                        'items' => ["Serialized stock item selection is missing for product ID #{$productId}."],
                    ]);
                }

                $stockItem = StockItem::find($stockItemId);
                if (!$stockItem) {
                    throw ValidationException::withMessages([
                        'items' => ["Stock item #{$stockItemId} does not exist."],
                    ]);
                }

                if ($stockItem->status !== StockItemStatus::AVAILABLE->value) {
                    throw ValidationException::withMessages([
                        'items' => ["Item code '{$stockItem->item_code}' is not in Available status and cannot be transferred."],
                    ]);
                }

                $itemCode = $stockItem->item_code;
            } else {
                if ($qty <= 0) {
                    throw ValidationException::withMessages([
                        'items' => ["Transfer quantity must be greater than zero."],
                    ]);
                }
            }

            StockTransferDetail::create([
                'stock_transfer_id' => $transfer->id,
                'product_id'        => $productId,
                'tracking_type'     => $trackingType,
                'stock_item_id'     => $stockItemId,
                'item_code'         => $itemCode,
                'transferred_qty'   => $qty,
                'received_qty'      => null,
                'damaged_qty'       => 0.00,
                'unit_cost'         => $unitCost,
                'remarks'           => $item['remarks'] ?? null,
            ]);
        }
    }

    /**
     * Validate that stock is available for dispatch.
     *
     * @param StockTransfer $transfer
     * @return void
     * @throws ValidationException
     */
    protected function validateAvailableStockForDispatch(StockTransfer $transfer): void
    {
        $transfer->loadMissing('details.product');

        foreach ($transfer->details as $detail) {
            if ($detail->tracking_type === Product::TRACKING_INDIVIDUAL && $detail->stock_item_id) {
                $stockItem = StockItem::find($detail->stock_item_id);
                if (!$stockItem || $stockItem->status !== StockItemStatus::AVAILABLE->value) {
                    $code = $stockItem ? $stockItem->item_code : "#{$detail->stock_item_id}";
                    throw ValidationException::withMessages([
                        'items' => ["Item '{$code}' is no longer AVAILABLE at source location."],
                    ]);
                }
            } else {
                $available = $this->availableStockService->getAvailableQuantity(
                    $detail->product_id,
                    $transfer->source_branch_id,
                    $transfer->source_counter_id
                );

                if ($available < (float) $detail->transferred_qty) {
                    $pName = $detail->product ? $detail->product->name : "Product #{$detail->product_id}";
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for '{$pName}'. Available: {$available}, Requested: {$detail->transferred_qty}."],
                    ]);
                }
            }
        }
    }
}
