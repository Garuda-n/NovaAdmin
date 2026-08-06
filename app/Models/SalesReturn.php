<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesReturn extends Model
{
    use HasFactory;

    protected $table = 'sales_returns';

    // Status Constants
    public const STATUS_COMPLETED = 1;

    protected $fillable = [
        'company_id',
        'branch_id',
        'counter_id',
        'sales_id',
        'customer_id',
        'sales_person_id',
        'return_no',
        'return_no_display',
        'return_date',
        'business_date',
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
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'counter_id' => 'integer',
        'sales_id' => 'integer',
        'customer_id' => 'integer',
        'sales_person_id' => 'integer',
        'return_no' => 'integer',
        'return_date' => 'date',
        'business_date' => 'date',
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
        'status' => 'integer',
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
     * Relationship: Sale
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sales_id');
    }

    /**
     * Relationship: Sales Person (User)
     */
    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_person_id');
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
     * Relationship: Sales Return Details (Line Items)
     */
    public function salesReturnDetails(): HasMany
    {
        return $this->hasMany(SalesReturnDetail::class, 'sales_return_id');
    }

    /**
     * Relationship: Sales Payments (Refunds)
     */
    public function salesPayments(): HasMany
    {
        return $this->hasMany(SalesPayment::class, 'sales_return_id');
    }
}
