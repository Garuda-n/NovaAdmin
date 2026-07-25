<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMode extends Model
{
    use HasFactory;

    protected $table = 'payment_modes';

    // Mode Types
    public const TYPE_CASH = 1;
    public const TYPE_BANK = 2;
    public const TYPE_UPI = 3;
    public const TYPE_CARD = 4;
    public const TYPE_CHEQUE = 5;
    public const TYPE_WALLET = 6;
    public const TYPE_OTHER = 7;

    // Status
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 2;

    protected $fillable = [
        'company_id',
        'mode_name',
        'mode_code',
        'mode_type',
        'display_order',
        'is_default',
        'status',
        'configuration',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'mode_type' => 'integer',
        'display_order' => 'integer',
        'is_default' => 'boolean',
        'status' => 'integer',
        'configuration' => 'array',
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
     * Relationship: Sales Payments
     */
    public function salesPayments(): HasMany
    {
        return $this->hasMany(SalesPayment::class, 'payment_mode_id');
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
