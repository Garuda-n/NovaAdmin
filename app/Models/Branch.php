<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}