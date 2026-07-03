<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'logo', 'description',
        'website', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function phoneModels(): HasMany
    {
        return $this->hasMany(PhoneModel::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
