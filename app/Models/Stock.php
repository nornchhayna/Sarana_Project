<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $table = 'stock';

    protected $fillable = [
        'product_id', 'color_id', 'phone_storage_id',
        'quantity', 'reserved_quantity',
        'low_stock_threshold', 'warehouse_location',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function phoneStorage(): BelongsTo
    {
        return $this->belongsTo(PhoneStorage::class);
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->available_quantity <= $this->low_stock_threshold && $this->available_quantity > 0;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->available_quantity <= 0;
    }
}
