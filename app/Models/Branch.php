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
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'is_head_office',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
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