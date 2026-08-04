<?php

namespace App\Models;

use App\Enums\StockTransferStatus;
use App\Enums\StockTransferType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'transfer_no',
        'transfer_type',
        'source_branch_id',
        'source_counter_id',
        'destination_branch_id',
        'destination_counter_id',
        'transfer_date',
        'status',
        'remarks',
        'cancellation_reason',
        'created_by',
        'approved_by',
        'approved_at',
        'dispatched_by',
        'dispatched_at',
        'received_by',
        'received_at',
        'cancelled_by',
        'cancelled_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'transfer_type' => StockTransferType::class,
        'source_branch_id' => 'integer',
        'source_counter_id' => 'integer',
        'destination_branch_id' => 'integer',
        'destination_counter_id' => 'integer',
        'transfer_date' => 'date',
        'status' => StockTransferStatus::class,
        'created_by' => 'integer',
        'approved_by' => 'integer',
        'approved_at' => 'datetime',
        'dispatched_by' => 'integer',
        'dispatched_at' => 'datetime',
        'received_by' => 'integer',
        'received_at' => 'datetime',
        'cancelled_by' => 'integer',
        'cancelled_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sourceBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'source_branch_id');
    }

    public function sourceCounter(): BelongsTo
    {
        return $this->belongsTo(Counter::class, 'source_counter_id');
    }

    public function destinationBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function destinationCounter(): BelongsTo
    {
        return $this->belongsTo(Counter::class, 'destination_counter_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(StockTransferDetail::class, 'stock_transfer_id');
    }

    public function isDraft(): bool
    {
        return $this->status === StockTransferStatus::DRAFT;
    }

    public function isDispatched(): bool
    {
        return $this->status === StockTransferStatus::DISPATCHED;
    }

    public function isReceived(): bool
    {
        return $this->status === StockTransferStatus::RECEIVED;
    }

    public function isCancelled(): bool
    {
        return $this->status === StockTransferStatus::CANCELLED;
    }
}
