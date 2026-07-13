<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    protected $fillable = [
        'name',
        'short_code',
        'start_date',
        'end_date',
        'is_current',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'status' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}