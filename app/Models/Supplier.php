<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Supplier extends Model
{
    use HasFactory;

    /**
     * Configurable supplier types.
     * Modify this array if business requirements change.
     */
    const SUPPLIER_TYPES = [
        'Manufacturer',
        'Distributor',
        'Wholesaler',
        'Testing Center',
        'Others',
    ];

    protected $fillable = [
        'supplier_name',
        'supplier_code',
        'contact_person',
        'mobile',
        'alternate_mobile',
        'email',
        'supplier_type',
        'gst_number',
        'pan_number',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'branch_id',
        'opening_balance',
        'credit_limit',
        'credit_days',
        'created_through',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'opening_balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'credit_days' => 'integer',
        'country_id' => 'integer',
        'state_id' => 'integer',
        'city_id' => 'integer',
        'branch_id' => 'integer',
    ];

    /**
     * Relationship: Country
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Relationship: State
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Relationship: City
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Relationship: Branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relationship: Created By User
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Updated By User
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Filter by Active status
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Filter by Branch ID
     */
    public function scopeForBranch(Builder $query, ?int $branchId): Builder
    {
        if ($branchId === null) {
            return $query;
        }

        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope: Filter by Supplier Type
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('supplier_type', $type);
    }
}
