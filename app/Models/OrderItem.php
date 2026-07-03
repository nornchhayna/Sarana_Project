<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'color_id',
        'phone_storage_id',
        'product_name',
        'product_sku',
        'color_name',
        'storage_label',
        'storage_capacity',
        'quantity',
        'unit_price',
        'subtotal',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

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
}
