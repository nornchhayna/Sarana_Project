<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStorage extends Model
{
    protected $fillable = [
        'product_id', 'phone_storage_id',
        'price_adjustment', 'is_active',
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function phoneStorage(): BelongsTo
    {
        return $this->belongsTo(PhoneStorage::class);
    }
}
