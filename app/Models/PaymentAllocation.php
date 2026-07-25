<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    use HasFactory;

    protected $table = 'payment_allocations';

    // Allocation Types
    public const TYPE_MANUAL = 1;
    public const TYPE_FIFO = 2;
    public const TYPE_ADJUSTMENT = 3;

    protected $fillable = [
        'sales_payment_id',
        'customer_receivable_id',
        'allocated_amount',
        'allocation_date',
        'allocation_type',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sales_payment_id' => 'integer',
        'customer_receivable_id' => 'integer',
        'allocation_date' => 'date',
        'allocated_amount' => 'decimal:2',
        'allocation_type' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Relationship: SalesPayment
     */
    public function salesPayment(): BelongsTo
    {
        return $this->belongsTo(SalesPayment::class, 'sales_payment_id');
    }

    /**
     * Alias Relationship: payment
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(SalesPayment::class, 'sales_payment_id');
    }

    /**
     * Relationship: CustomerReceivable
     */
    public function customerReceivable(): BelongsTo
    {
        return $this->belongsTo(CustomerReceivable::class, 'customer_receivable_id');
    }

    /**
     * Alias Relationship: receivable
     */
    public function receivable(): BelongsTo
    {
        return $this->belongsTo(CustomerReceivable::class, 'customer_receivable_id');
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
}
