<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Counter;

class Branch extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'branch_code',
        'gst_number',
        'phone',
        'email',
        'address',
        'is_head_office',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function counters()
    {
        return $this->belongsToMany(Counter::class, 'branch_counters')
            ->withPivot([
                'status',
                'created_by',
                'updated_by'
            ])
            ->withTimestamps();
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}