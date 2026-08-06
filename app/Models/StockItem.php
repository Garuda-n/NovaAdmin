<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_inward_id',
        'stock_inward_item_id',
        'product_id',
        'branch_id',
        'counter_id',
        'sub_product_id',
        'size_id',
        'item_code',
        'status',
        'allocated_by',
        'allocated_at',
    ];

    protected $casts = [
        'stock_inward_id' => 'integer',
        'stock_inward_item_id' => 'integer',
        'product_id' => 'integer',
        'branch_id' => 'integer',
        'counter_id' => 'integer',
        'sub_product_id' => 'integer',
        'size_id' => 'integer',
        'status' => 'integer',
        'allocated_by' => 'integer',
        'allocated_at' => 'datetime',
    ];

    public function stockInward(): BelongsTo
    {
        return $this->belongsTo(StockInward::class, 'stock_inward_id');
    }

    public function stockInwardItem(): BelongsTo
    {
        return $this->belongsTo(StockInwardItem::class, 'stock_inward_item_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    public function subProduct(): BelongsTo
    {
        return $this->belongsTo(SubProduct::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function allocatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(StockItemLog::class, 'stock_item_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(StockItemImage::class, 'stock_item_id');
    }

    public function defaultImage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StockItemImage::class, 'stock_item_id')->where('is_default', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        // Access loaded collection to avoid N+1 query overhead
        $defaultImg = $this->images->first(fn($img) => $img->is_default);
        if ($defaultImg) {
            return $defaultImg->url;
        }

        $firstImg = $this->images->first();
        if ($firstImg) {
            return $firstImg->url;
        }

        return $this->product?->image_url;
    }

    /**
     * Relationship: Sales Return Details (where this item was the original returned item)
     */
    public function salesReturnAsOriginal(): HasMany
    {
        return $this->hasMany(SalesReturnDetail::class, 'original_stock_item_id');
    }

    /**
     * Relationship: Sales Return Details (where this item was the recreated item)
     */
    public function salesReturnAsRecreated(): HasMany
    {
        return $this->hasMany(SalesReturnDetail::class, 'recreated_stock_item_id');
    }
}
