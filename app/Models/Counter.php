<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;

class Counter extends Model
{
    protected $fillable = [
        'counter_name',
        'counter_code',
        'status',
        'created_by',
        'updated_by',
    ];
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_counters')
            ->withPivot([
                'status',
                'created_by',
                'updated_by'
            ])
            ->withTimestamps();
    }
}