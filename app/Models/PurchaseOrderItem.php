<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\PurchaseOrderItemFactory::new();
    }

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_variant_id',
        'quantity_ordered',
        'quantity_received',
        'unit_cost',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function receivingItems(): HasMany
    {
        return $this->hasMany(StockReceivingItem::class);
    }

    public function getQuantityOutstandingAttribute(): int
    {
        return max(0, $this->quantity_ordered - $this->quantity_received);
    }
}
