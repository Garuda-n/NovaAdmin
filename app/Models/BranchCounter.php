<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchCounter extends Model
{
    protected $fillable = [
        'branch_id',
        'counter_id',
        'status',
        'created_by',
        'updated_by',
    ];
}