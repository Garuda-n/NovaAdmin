<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'next_sequence',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'next_sequence' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
