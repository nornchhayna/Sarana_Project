<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\CartItemFactory::new();
    }

    protected $fillable = [
        'cart_id', 'product_id', 'product_variant_id', 'color_id', 'phone_storage_id', 'phone_ram_id',
        'quantity', 'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',

    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(PhoneStorage::class);
    }

    public function phoneStorage(): BelongsTo
    {
        return $this->belongsTo(PhoneStorage::class);
    }

    public function phoneRam(): BelongsTo
    {
        return $this->belongsTo(PhoneRam::class, 'phone_ram_id');
    }

    public function ram(): BelongsTo
    {
        return $this->belongsTo(PhoneRam::class, 'phone_ram_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function setQuantityAttribute(int $value): void
    {
        $this->attributes['quantity'] = $value;

    }

    public function getTotalPriceAttribute()
    {
        return $this->unit_price * $this->quantity;
    }
}
