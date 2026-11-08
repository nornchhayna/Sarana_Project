<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'color_id',
        'image',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        return str_starts_with($this->image, 'http')
            ? $this->image
            : url('storage/' . ltrim($this->image, '/'));
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
}