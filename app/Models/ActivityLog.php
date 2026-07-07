<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'module',
        'action',
        'record_id',
        'description',
        'ip_address',
        'user_agent',
        'meta',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}