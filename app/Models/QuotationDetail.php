<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'product_id',
        'product_name',
        'uom_id',
        'uom_name',
        'qty',
        'rate',
        'tax_percent',
        'tax_amount',
        'line_total',
    ];

    protected $casts = [
        'quotation_id' => 'integer',
        'product_id' => 'integer',
        'uom_id' => 'integer',
        'qty' => 'decimal:3',
        'rate' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /**
     * Relationship: Quotation
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Relationship: Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship: UOM
     */
    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class);
    }
}
