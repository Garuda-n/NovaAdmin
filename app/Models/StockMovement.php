<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';

    // Movement Types
    public const TYPE_OPENING = 1;
    public const TYPE_PURCHASE = 2;
    public const TYPE_SALE = 3;
    public const TYPE_TRANSFER = 4;
    public const TYPE_ADJUSTMENT = 5;
    public const TYPE_RETURN = 6;

    protected $fillable = [
        'company_id',
        'branch_id',
        'product_id',
        'stock_item_id',
        'movement_type',
        'quantity',
        'reference_type',
        'reference_id',
        'movement_date',
        'created_by',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'product_id' => 'integer',
        'stock_item_id' => 'integer',
        'movement_type' => 'integer',
        'quantity' => 'decimal:2',
        'reference_id' => 'integer',
        'movement_date' => 'date',
        'created_by' => 'integer',
    ];

    /**
     * Relationship: Company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relationship: Branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relationship: Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship: StockItem
     */
    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    /**
     * Relationship: Creator (User)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
