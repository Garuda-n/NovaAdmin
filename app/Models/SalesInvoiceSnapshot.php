<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoiceSnapshot extends Model
{
    use HasFactory;

    protected $table = 'sales_invoice_snapshots';

    protected $fillable = [
        'sales_id',
        'customer_name',
        'customer_mobile',
        'customer_email',
        'customer_address',
        'customer_gst_number',
        'company_name',
        'company_gst_number',
        'company_address',
        'branch_name',
        'branch_gst_number',
        'branch_address',
        'gst_type',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sales_id' => 'integer',
        'gst_type' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Relationship: Sale
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sales_id');
    }

    /**
     * Relationship: Creator (User)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Updater (User)
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
