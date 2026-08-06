<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReturnDetail extends Model
{
    use HasFactory;

    protected $table = 'sales_return_details';

    protected $fillable = [
        'sales_return_id',
        'sales_detail_id',
        'product_id',
        'uom_id',
        'original_stock_item_id',
        'recreated_stock_item_id',
        'item_type',
        'returned_quantity',
        'rate',
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
        'sales_return_id' => 'integer',
        'sales_detail_id' => 'integer',
        'product_id' => 'integer',
        'uom_id' => 'integer',
        'original_stock_item_id' => 'integer',
        'recreated_stock_item_id' => 'integer',
        'item_type' => 'integer',
        'returned_quantity' => 'decimal:2',
        'rate' => 'decimal:2',
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
     * Relationship: Sales Return Header
     */
    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(SalesReturn::class, 'sales_return_id');
    }

    /**
     * Relationship: Sales Detail Line
     */
    public function salesDetail(): BelongsTo
    {
        return $this->belongsTo(SalesDetail::class, 'sales_detail_id');
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
     * Relationship: Original Stock Item (the serialized item returned)
     */
    public function originalStockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'original_stock_item_id');
    }

    /**
     * Relationship: Recreated Stock Item (the new item generated upon warehouse re-entry)
     */
    public function recreatedStockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'recreated_stock_item_id');
    }
}
