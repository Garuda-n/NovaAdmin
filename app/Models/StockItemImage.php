<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StockItemImage extends Model
{
    use HasFactory;

    protected $table = 'stock_item_images';

    protected $fillable = [
        'stock_item_id',
        'image_path',
        'is_default',
    ];

    protected $casts = [
        'stock_item_id' => 'integer',
        'is_default' => 'boolean',
    ];

    /**
     * Relationship: Stock Item
     */
    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    /**
     * Accessor: Absolute Image URL
     */
    public function getUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}
