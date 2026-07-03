<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'promotion_id',
        'subtotal', 'discount_amount', 'total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function recalculate(): void
    {
        $subtotal = $this->items->sum('total_price');
        $discount = $this->promotion ? $this->promotion->calculateDiscount($subtotal) : 0;

        $this->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'total' => max(0, $subtotal - $discount),
        ]);
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
