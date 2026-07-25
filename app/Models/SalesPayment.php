<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesPayment extends Model
{
    use HasFactory;

    protected $table = 'sales_payments';

    // Status
    public const STATUS_COMPLETED = 1;
    public const STATUS_CANCELLED = 2;

    protected $fillable = [
        'sales_id',
        'payment_mode_id',
        'payment_date',
        'amount',
        'reference_no',
        'remarks',
        'status',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sales_id' => 'integer',
        'payment_mode_id' => 'integer',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'status' => 'integer',
        'cancelled_by' => 'integer',
        'cancelled_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Relationship: Sale
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sales_id');
    }

    /**
     * Relationship: PaymentMode
     */
    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    }

    /**
     * Relationship: Payment Allocations
     */
    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class, 'sales_payment_id');
    }

    /**
     * Alias Relationship: allocations
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class, 'sales_payment_id');
    }

    /**
     * Relationship: Creator (User)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Updater (User)
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship: Cancelled By (User)
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Helper: Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Helper: Check if payment is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
}
