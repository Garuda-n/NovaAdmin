<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockItemLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'stock_item_id',
        'transaction_type',
        'reference_type',
        'reference_id',
        'branch_id',
        'counter_id',
        'remarks',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'stock_item_id' => 'integer',
        'transaction_type' => 'integer',
        'reference_type' => 'integer',
        'reference_id' => 'integer',
        'branch_id' => 'integer',
        'counter_id' => 'integer',
        'created_by' => 'integer',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->created_at) {
                $model->created_at = now();
            }
        });

        static::updating(function ($model) {
            return false;
        });

        static::deleting(function ($model) {
            return false;
        });
    }

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
