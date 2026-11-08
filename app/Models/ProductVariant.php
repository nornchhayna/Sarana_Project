<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\ProductVariantFactory::new();
    }

    protected $fillable = [
        'product_id',
        'color_id',
        'phone_storage_id',
        'phone_ram_id',
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

    protected $appends = [
        'is_in_stock',
        'storage_id',
        'ram_id',
    ];

    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function getStorageIdAttribute(): ?int
    {
        return $this->phone_storage_id;
    }

    public function getRamIdAttribute(): ?int
    {
        return $this->phone_ram_id;
    }

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
        return $this->belongsTo(PhoneStorage::class, 'phone_storage_id');
    }

    /**
     * Alias for phoneStorage so eager-loading variants.storage works
     */
    public function storage()
    {
        return $this->belongsTo(PhoneStorage::class, 'phone_storage_id');
    }

    /**
     * Phone RAM
     */
    public function phoneRam()
    {
        return $this->belongsTo(PhoneRam::class, 'phone_ram_id');
    }

    /**
     * Alias for phoneRam
     */
    public function ram()
    {
        return $this->belongsTo(PhoneRam::class, 'phone_ram_id');
    }

    /**
     * Find variant by color, storage, and RAM
     */
    public function scopeFindByColorStorageAndRam($query, $productId, $colorId, $storageId, $ramId)
    {
        return $query->where('product_id', $productId)
            ->where('color_id', $colorId)
            ->where('phone_storage_id', $storageId)
            ->where('phone_ram_id', $ramId)
            ->where('is_active', true);
    }

    /**
     * Check if variant is in stock
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Stock movements for this variant
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_variant_id');
    }

    /**
     * Order items associated with this variant's product
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    /**
     * Update stock based on movement type
     */
    public function adjustStock(int $quantity, string $type): void
    {
        if (in_array($type, ['purchase_receipt', 'adjustment_in', 'sale_return'])) {
            $this->increment('stock', $quantity);
        } elseif (in_array($type, ['sale', 'adjustment_out', 'damage'])) {
            $decrementAmount = min((int) $this->stock, $quantity);
            if ($decrementAmount > 0) {
                $this->decrement('stock', $decrementAmount);
            }
        }
    }
}