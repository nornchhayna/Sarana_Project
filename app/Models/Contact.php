<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'phone',
        'subject', 'message', 'status',
        'reply', 'replied_at', 'is_seen_by_customer',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'is_seen_by_customer' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'new');
    }
}
