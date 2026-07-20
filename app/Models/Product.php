<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    // Tracking Types
    const TRACKING_QUANTITY = 1;
    const TRACKING_INDIVIDUAL = 2;
    const TRACKING_BATCH = 3;
    const TRACKING_SERIAL = 4;

    // Item Generation Modes
    const ITEM_GEN_INDIVIDUAL = 'individual';
    const ITEM_GEN_BULK = 'bulk';

    protected $fillable = [
        'code',
        'name',
        'hsn_code',
        'category_id',
        'brand_id',
        'uom_id',
        'tax_id',
        'tax_type',
        'sales_based_on',
        'purchase_based_on',
        'has_size',
        'has_sub_product',
        'calculation_based_on',
        'tracking_type',
        'item_generation_mode',
        'reorder_applicable',
        'min_stock_level',
        'image',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'has_size' => 'boolean',
        'has_sub_product' => 'boolean',
        'tracking_type' => 'integer',
        'reorder_applicable' => 'boolean',
        'min_stock_level' => 'decimal:2',
    ];

    /**
     * Get all tracking types map.
     */
    public static function getTrackingTypes(): array
    {
        return [
            self::TRACKING_QUANTITY => 'Quantity Based',
            self::TRACKING_INDIVIDUAL => 'Individual Item Based',
            self::TRACKING_BATCH => 'Batch Based (Future)',
            self::TRACKING_SERIAL => 'Serial Number Based (Future)',
        ];
    }

    /**
     * Get currently active tracking types for form options.
     */
    public static function getAvailableTrackingTypes(): array
    {
        return [
            self::TRACKING_QUANTITY => 'Quantity Based',
            self::TRACKING_INDIVIDUAL => 'Individual Item Based',
        ];
    }

    /**
     * Accessor for tracking type human label.
     */
    public function getTrackingTypeLabelAttribute(): string
    {
        return self::getTrackingTypes()[$this->tracking_type] ?? 'Individual Item Based';
    }

    /**
     * Get all item generation modes map.
     */
    public static function getItemGenerationModes(): array
    {
        return [
            self::ITEM_GEN_INDIVIDUAL => 'Individual',
            self::ITEM_GEN_BULK => 'Bulk',
        ];
    }

    /**
     * Accessor for item generation mode label.
     */
    public function getItemGenerationModeLabelAttribute(): string
    {
        return self::getItemGenerationModes()[$this->item_generation_mode] ?? 'Individual';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'product_size');
    }

    public function subProducts(): BelongsToMany
    {
        return $this->belongsToMany(SubProduct::class, 'product_sub_product', 'product_id', 'sub_product_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get full image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? Storage::url($this->image)
            : null;
    }
}
