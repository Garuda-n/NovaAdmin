<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'logo',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get full logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo
            ? Storage::url($this->logo)
            : null;
    }
}