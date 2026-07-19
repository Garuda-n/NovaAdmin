<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockInwardItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_inward_id',
        'product_id',
        'sub_product_id',
        'qty',
        'weight',
        'purchase_price',
        'selling_price',
        'mrp',
        'remarks',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'weight' => 'decimal:3',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'mrp' => 'decimal:2',
    ];

    public function stockInward(): BelongsTo
    {
        return $this->belongsTo(StockInward::class, 'stock_inward_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function subProduct(): BelongsTo
    {
        return $this->belongsTo(SubProduct::class);
    }
}
