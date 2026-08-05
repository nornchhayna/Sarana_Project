<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'color_id',
        'phone_storage_id',
        'sku',
        'price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Color
     */
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * Phone Storage
     */
    public function phoneStorage()
    {
        return $this->belongsTo(PhoneStorage::class);
    }

    /**
     * Find variant by color and storage
     */
    public function scopeFindByColorAndStorage($query, $productId, $colorId, $storageId)
    {
        return $query->where('product_id', $productId)
            ->where('color_id', $colorId)
            ->where('phone_storage_id', $storageId)
            ->where('is_active', true);
    }

    /**
     * Check if variant is in stock
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}