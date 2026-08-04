<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_transfer_id',
        'product_id',
        'tracking_type',
        'stock_item_id',
        'item_code',
        'transferred_qty',
        'received_qty',
        'damaged_qty',
        'unit_cost',
        'remarks',
    ];

    protected $casts = [
        'stock_transfer_id' => 'integer',
        'product_id' => 'integer',
        'tracking_type' => 'integer',
        'stock_item_id' => 'integer',
        'transferred_qty' => 'decimal:2',
        'received_qty' => 'decimal:2',
        'damaged_qty' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function stockTransfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class, 'stock_transfer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }
}
