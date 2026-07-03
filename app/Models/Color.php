<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    protected $fillable = ['name', 'hex_code', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function productColors(): HasMany
    {
        return $this->hasMany(ProductColor::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_colors')
            ->withPivot('images', 'price_adjustment', 'is_active')
            ->withTimestamps();
    }
}
