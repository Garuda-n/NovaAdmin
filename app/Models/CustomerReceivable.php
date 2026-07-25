<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerReceivable extends Model
{
    use HasFactory;

    protected $table = 'customer_receivables';

    // Status
    public const STATUS_PENDING = 1;
    public const STATUS_PARTIALLY_PAID = 2;
    public const STATUS_PAID = 3;
    public const STATUS_CANCELLED = 4;

    protected $fillable = [
        'sales_id',
        'customer_id',
        'invoice_date',
        'due_date',
        'original_amount',
        'paid_amount',
        'balance_amount',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sales_id' => 'integer',
        'customer_id' => 'integer',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'original_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'status' => 'integer',
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
     * Relationship: Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Relationship: Payment Allocations
     */
    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class, 'customer_receivable_id');
    }

    /**
     * Alias Relationship: allocations
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class, 'customer_receivable_id');
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
     * Helper: Check if receivable is fully paid
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Helper: Check if receivable is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Helper: Check if receivable is partially paid
     */
    public function isPartiallyPaid(): bool
    {
        return $this->status === self::STATUS_PARTIALLY_PAID;
    }

    /**
     * Helper: Check if receivable is overdue
     */
    public function isOverdue(): bool
    {
        if ($this->balance_amount <= 0 || !$this->due_date) {
            return false;
        }

        return $this->due_date->isPast();
    }
}
