<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use HasFactory;

    public const STATUS_CREATED = 1;
    public const STATUS_CONVERTED = 2;

    protected $fillable = [
        'quotation_no',
        'business_date',
        'branch_id',
        'counter_id',
        'customer_id',
        'customer_type',
        'status',
        'subtotal',
        'tax_amount',
        'grand_total',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'business_date' => 'date',
        'status' => 'integer',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'branch_id' => 'integer',
        'counter_id' => 'integer',
        'customer_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Relationship: Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
     * Relationship: Details
     */
    public function details(): HasMany
    {
        return $this->hasMany(QuotationDetail::class, 'quotation_id');
    }

    /**
     * Alias Relationship: QuotationDetails
     */
    public function quotationDetails(): HasMany
    {
        return $this->hasMany(QuotationDetail::class, 'quotation_id');
    }

    /**
     * Relationship: Logs
     */
    public function logs(): HasMany
    {
        return $this->hasMany(QuotationLog::class, 'quotation_id');
    }

    /**
     * Alias Relationship: QuotationLogs
     */
    public function quotationLogs(): HasMany
    {
        return $this->hasMany(QuotationLog::class, 'quotation_id');
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
