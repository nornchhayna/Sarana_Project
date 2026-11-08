<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\PurchaseOrderFactory::new();
    }

    protected $fillable = [
        'supplier_id',
        'po_number',
        'status',
        'order_date',
        'expected_date',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'currency',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function stockReceivings(): HasMany
    {
        return $this->hasMany(StockReceiving::class);
    }

    /**
     * Recalculate and persist status based on how much of each line has
     * been received. Call this after any stock receiving is recorded.
     */
    public function refreshStatus(): void
    {
        $items = $this->items()->get();

        if ($items->isEmpty()) {
            return;
        }

        $fullyReceived = $items->every(
            fn (PurchaseOrderItem $item) => $item->quantity_received >= $item->quantity_ordered
        );

        $partiallyReceived = $items->contains(
            fn (PurchaseOrderItem $item) => $item->quantity_received > 0
        );

        if ($fullyReceived) {
            $this->status = 'received';
        } elseif ($partiallyReceived) {
            $this->status = 'partially_received';
        }

        $this->save();
    }
}
