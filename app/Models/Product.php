<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'sku', 'description', 'details', 'specifications',
        'category_id', 'brand_id', 'phone_model_id',
        'base_price', 'sale_price', 'thumbnail', 'images',
        'is_active', 'is_featured', 'is_new', 'sort_order',
        'status', 'is_pre_order', 'discount', 'token', 'data_from',
        'total_sold', 'avg_rating',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'avg_rating' => 'decimal:2',
        'images' => 'array',
        'details' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_pre_order' => 'boolean',
        'sort_order' => 'integer',
        'total_sold' => 'integer',
        'data_from' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function phoneModel(): BelongsTo
    {
        return $this->belongsTo(PhoneModel::class);
    }

    public function productColors(): HasMany
    {
        return $this->hasMany(ProductColor::class);
    }

    //   public function colors(): BelongsToMany
    //     {
    //         // Removed 'images' and 'is_active' so it perfectly matches your database
    //         return $this->belongsToMany(Color::class, 'product_colors')
    //                     ->withPivot('price_adjustment')
    //                     ->withTimestamps();
    //     }

    public function productStorages(): HasMany
    {
        return $this->hasMany(ProductStorage::class);
    }

    public function storages(): BelongsToMany
    {
        return $this->belongsToMany(PhoneStorage::class, 'product_storages')
            ->withPivot('price_adjustment')
            ->withTimestamps();
    }
// Product.php
public function variantImages()
{
    return $this->hasMany(ProductImage::class);
}
public function variants()
{
    return $this->hasMany(ProductVariant::class);
}
    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->base_price;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return ! is_null($this->sale_price) && $this->sale_price < $this->base_price;
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->is_on_sale) {
            return null;
        }

        return (int) round((($this->base_price - $this->sale_price) / $this->base_price) * 100);
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->where('status', 'approved')->avg('rating') ?? 0, 1);
    }

    public function getTotalStockAttribute(): int
    {
        return $this->stock()->sum('quantity');
    }

    // Field mapping for backward compatibility
    public function getPriceAttribute(): float
    {
        return $this->base_price;
    }

    public function setPriceAttribute(float $value): void
    {
        $this->attributes['base_price'] = $value;
    }

    public function getDiscountedPriceAttribute(): float
    {
        if ($this->discount > 0) {
            return $this->base_price - ($this->base_price * ($this->discount / 100));
        }

        return $this->base_price;
    }

    public function getThumbnailsAttribute(): array
    {
        return $this->images ?? [];
    }

    public function setThumbnailsAttribute(array $value): void
    {
        $this->attributes['images'] = json_encode($value);
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price');
    }

    public function scopeInStock($query)
    {
        return $query->whereHas('stock', fn ($q) => $q->where('quantity', '>', 0));
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->orWhere('sku', 'like', "%{$search}%");
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, int $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where('status', 'active');
    }

    public function scopePreOrder($query)
    {
        return $query->where('is_pre_order', true);
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_colors')
            ->withPivot('price_adjustment')
            ->withTimestamps();
    }
}
