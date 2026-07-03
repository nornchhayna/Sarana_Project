<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'transaction_id', 'method', 'status',
        'amount', 'currency', 'proof_image',
        'khqr_string', 'khqr_md5', 'khqr_expires_at',
        'gateway_response', 'notes', 'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'khqr_expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
