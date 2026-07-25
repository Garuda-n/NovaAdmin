<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesDetail extends Model
{
    use HasFactory;

    protected $table = 'sales_details';

    // Item Type
    public const ITEM_ALLOCATED = 1;
    public const ITEM_UNALLOCATED = 2;

    protected $fillable = [
        'sales_id',
        'product_id',
        'uom_id',
        'allocated_item_id',
        'product_code',
        'product_name',
        'item_type',
        'quantity',
        'rate',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_percentage',
        'cgst_percentage',
        'cgst_amount',
        'sgst_percentage',
        'sgst_amount',
        'igst_percentage',
        'igst_amount',
        'tax_amount',
        'line_total',
    ];

    protected $casts = [
        'sales_id' => 'integer',
        'product_id' => 'integer',
        'uom_id' => 'integer',
        'allocated_item_id' => 'integer',
        'item_type' => 'integer',
        'discount_type' => 'integer',
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'cgst_percentage' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_percentage' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_percentage' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /**
     * Relationship: Sale
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sales_id');
    }

    /**
     * Relationship: Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relationship: Uom
     */
    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    /**
     * Relationship: StockItem (Allocated Inventory Item)
     */
    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'allocated_item_id');
    }

    /**
     * Alias Relationship: allocatedItem
     */
    public function allocatedItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'allocated_item_id');
    }

    /**
     * Helper: Check if line item is allocated
     */
    public function isAllocated(): bool
    {
        return $this->item_type === self::ITEM_ALLOCATED;
    }

    /**
     * Helper: Check if line item is unallocated
     */
    public function isUnallocated(): bool
    {
        return $this->item_type === self::ITEM_UNALLOCATED;
    }
}
