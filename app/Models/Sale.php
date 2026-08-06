<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';

    // Status
    public const STATUS_COMPLETED = 1;
    public const STATUS_CANCELLED = 2;

    // Sale Type
    public const TYPE_CASH = 1;
    public const TYPE_CREDIT = 2;

    // GST Type
    public const GST_CGST_SGST = 1;
    public const GST_IGST = 2;

    protected $fillable = [
        'company_id',
        'branch_id',
        'counter_id',
        'quotation_id',
        'customer_id',
        'sales_person_id',
        'invoice_no',
        'invoice_no_display',
        'invoice_date',
        'gst_type',
        'subtotal',
        'item_discount',
        'invoice_discount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'tax_amount',
        'round_off',
        'grand_total',
        'sale_type',
        'due_date',
        'status',
        'remarks',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
        'cancel_remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'counter_id' => 'integer',
        'quotation_id' => 'integer',
        'customer_id' => 'integer',
        'sales_person_id' => 'integer',
        'invoice_no' => 'integer',
        'invoice_date' => 'date',
        'gst_type' => 'integer',
        'subtotal' => 'decimal:2',
        'item_discount' => 'decimal:2',
        'invoice_discount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'round_off' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'sale_type' => 'integer',
        'due_date' => 'date',
        'status' => 'integer',
        'cancelled_by' => 'integer',
        'cancelled_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Relationship: Company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relationship: Branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relationship: Counter
     */
    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    /**
     * Relationship: Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship: Quotation
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Relationship: Sales Person (User)
     */
    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_person_id');
    }

    /**
     * Relationship: Cancelled By (User)
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
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
     * Relationship: Sales Details (Line items)
     */
    public function salesDetails(): HasMany
    {
        return $this->hasMany(SalesDetail::class, 'sales_id');
    }

    /**
     * Alias Relationship: details
     */
    public function details(): HasMany
    {
        return $this->hasMany(SalesDetail::class, 'sales_id');
    }

    /**
     * Relationship: Sales Payments
     */
    public function salesPayments(): HasMany
    {
        return $this->hasMany(SalesPayment::class, 'sales_id');
    }

    /**
     * Alias Relationship: payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(SalesPayment::class, 'sales_id');
    }

    /**
     * Relationship: Customer Receivable
     */
    public function customerReceivable(): HasOne
    {
        return $this->hasOne(CustomerReceivable::class, 'sales_id');
    }

    /**
     * Alias Relationship: receivable
     */
    public function receivable(): HasOne
    {
        return $this->hasOne(CustomerReceivable::class, 'sales_id');
    }

    /**
     * Relationship: Sales Invoice Snapshot
     */
    public function salesInvoiceSnapshot(): HasOne
    {
        return $this->hasOne(SalesInvoiceSnapshot::class, 'sales_id');
    }

    /**
     * Alias Relationship: snapshot
     */
    public function snapshot(): HasOne
    {
        return $this->hasOne(SalesInvoiceSnapshot::class, 'sales_id');
    }

    /**
     * Scope: Completed sales
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope: Cancelled sales
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope: Cash sales
     */
    public function scopeCashSales(Builder $query): Builder
    {
        return $query->where('sale_type', self::TYPE_CASH);
    }

    /**
     * Scope: Credit sales
     */
    public function scopeCreditSales(Builder $query): Builder
    {
        return $query->where('sale_type', self::TYPE_CREDIT);
    }

    /**
     * Scope: Invoices created today
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('invoice_date', now()->toDateString());
    }

    /**
     * Helper: Check if sale is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Helper: Check if sale is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Helper: Check if cash sale
     */
    public function isCashSale(): bool
    {
        return $this->sale_type === self::TYPE_CASH;
    }

    /**
     * Helper: Check if credit sale
     */
    public function isCreditSale(): bool
    {
        return $this->sale_type === self::TYPE_CREDIT;
    }

    /**
     * Relationship: Sales Returns
     */
    public function salesReturns(): HasMany
    {
        return $this->hasMany(SalesReturn::class, 'sales_id');
    }
}
