<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\ColorFactory::new();
    }

    protected $fillable = ['name', 'hex_code', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function productColors(): HasMany
    {
        return $this->hasMany(ProductColor::class);
    }
public function productImages()
{
    return $this->hasMany(ProductImage::class);
}
public function variants()
{
    return $this->hasMany(ProductVariant::class);
}
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_colors')
            ->withPivot('images', 'price_adjustment', 'is_active')
            ->withTimestamps();
    }
}
