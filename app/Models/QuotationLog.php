<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'old_data',
        'new_data',
        'changed_by',
    ];

    protected $casts = [
        'quotation_id' => 'integer',
        'old_data' => 'array',
        'new_data' => 'array',
        'changed_by' => 'integer',
    ];

    /**
     * Relationship: Quotation
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Relationship: User (changed_by)
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Alias Relationship: User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
