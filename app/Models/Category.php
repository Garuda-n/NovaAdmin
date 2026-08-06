<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'tax_id',
        'image',
        'status',
        'is_returnable',
        'return_window_days',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_returnable' => 'boolean',
        'return_window_days' => 'integer',
    ];

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
    /**
     * Get full image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? Storage::url($this->image)
            : null;
    }
}