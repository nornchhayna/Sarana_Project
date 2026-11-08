<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\StockMovementFactory::new();
    }

    /**
     * This table only has created_at (no updated_at) — movements are
     * an append-only ledger and are never edited in place.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Validate type values at application level for SQLite compatibility
            $validTypes = ['purchase_receipt', 'sale', 'adjustment_in', 'adjustment_out', 'sale_return', 'damage'];
            if (!in_array($model->type, $validTypes)) {
                throw new \InvalidArgumentException("Invalid stock movement type: {$model->type}");
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Polymorphic-style access to whatever record (stock_receiving, order,
     * etc.) triggered this movement, based on reference_type/reference_id.
     * Not a true Eloquent morph relation since reference_type stores a
     * short label rather than a class name — resolve manually if needed.
     */
    public function reference(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }
}
