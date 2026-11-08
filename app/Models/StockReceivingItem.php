<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReceivingItem extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\StockReceivingItemFactory::new();
    }

    protected $fillable = [
        'stock_receiving_id',
        'purchase_order_item_id',
        'product_variant_id',
        'quantity_received',
        'unit_cost',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function stockReceiving(): BelongsTo
    {
        return $this->belongsTo(StockReceiving::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
