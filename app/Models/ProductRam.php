<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRam extends Model
{
    protected $fillable = [
        'product_id', 'phone_ram_id',
        'price', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function phoneRam(): BelongsTo
    {
        return $this->belongsTo(PhoneRam::class);
    }
}
