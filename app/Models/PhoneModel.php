<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhoneModel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'brand_id', 'name', 'slug',
        'description', 'image', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
