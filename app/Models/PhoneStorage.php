<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhoneStorage extends Model
{
    protected $fillable = ['capacity', 'label', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function productStorages(): HasMany
    {
        return $this->hasMany(ProductStorage::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_storages')
            ->withPivot('price_adjustment', 'is_active')
            ->withTimestamps();
    }
}
