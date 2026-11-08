<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return \Database\Factories\ProductFactory::new();
    }

    protected $fillable = [
        'name', 'slug', 'sku', 'description', 'details', 'specifications',
        'category_id', 'brand_id', 'phone_model_id',
        'base_price', 'sale_price', 'sale_start_date', 'sale_end_date', 'thumbnail', 'images',
        'is_active', 'is_featured', 'is_new', 'is_flash_sale', 'sort_order',
        'status', 'is_pre_order', 'discount', 'token', 'data_from',
        'total_sold', 'avg_rating',
    ];

    protected $appends = [
        'image_url',
        'main_image_url',
        'total_stock',
        'in_stock',
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
        'is_flash_sale' => 'boolean',
        'is_pre_order' => 'boolean',
        'sort_order' => 'integer',
        'total_sold' => 'integer',
        'data_from' => 'date',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
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

    public function scopeWithSoldCount($query)
    {
        return $query->withSum(['orderItems as actual_sold' => function ($query) {
            $query->whereHas('order', fn ($q) => $q->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']));
        }], 'quantity');
    }

    public function gifts(): BelongsToMany
    {
        return $this->belongsToMany(Gift::class, 'product_gift')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function rams(): BelongsToMany
    {
        return $this->belongsToMany(PhoneRam::class, 'product_rams')
            ->withPivot('price', 'is_active')
            ->withTimestamps();
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getImageUrlAttribute(): ?string
    {
        $img = $this->thumbnail;
        if (empty($img) && is_array($this->images) && ! empty($this->images)) {
            $img = $this->images[0];
        }
        if (empty($img) && $this->relationLoaded('variantImages') && $this->variantImages->isNotEmpty()) {
            $img = $this->variantImages->first()->image;
        }
        if (empty($img)) {
            return null;
        }
        if (str_starts_with($img, 'http')) {
            return $img;
        }

        return url('storage/' . ltrim($img, '/'));
    }

    public function getMainImageUrlAttribute(): ?string
    {
        return $this->image_url;
    }

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
        return round($this->reviews()->where('is_approved', true)->avg('rating') ?? 0, 1);
    }

    public function getTotalStockAttribute(): int
    {
        // 1. If variants are loaded or exist, sum variant stock
        if ($this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
            return (int) $this->variants->sum('stock');
        }

        $variantStock = (int) $this->variants()->sum('stock');
        if ($variantStock > 0 || $this->variants()->exists()) {
            return $variantStock;
        }

        // 2. Otherwise check stock table
        if ($this->relationLoaded('stock') && $this->stock->isNotEmpty()) {
            return (int) $this->stock->sum('quantity');
        }

        return (int) $this->stock()->sum('quantity');
    }

    public function getInStockAttribute(): bool
    {
        return $this->total_stock > 0;
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

    public function toArray(): array
    {
        $array = parent::toArray();
        $stockValue = $this->total_stock;
        $array['stock'] = $stockValue;
        $array['total_stock'] = $stockValue;
        $array['stock_quantity'] = $stockValue;
        $array['in_stock'] = $stockValue > 0;
        return $array;
    }
}
