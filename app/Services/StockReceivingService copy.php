<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\StockReceiving;
use App\Models\StockReceivingItem;
use App\Models\StockMovement;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class StockReceivingService
{
    /**
     * Record a goods-received note and apply its effects in one transaction:
     *  - create the stock_receiving + stock_receiving_items rows
     *  - write a 'purchase_in' stock_movement per line
     *  - increment product_variants.stock per line
     *  - bump purchase_order_items.quantity_received (if linked to a PO)
     *  - refresh the parent purchase order's status
     *
     * @param  array  $data  ['purchase_order_id'?, 'supplier_id', 'reference_number'?,
     *                        'received_by'?, 'received_at'?, 'notes'?]
     * @param  array  $lines  Each: ['purchase_order_item_id'?, 'product_variant_id',
     *                               'quantity_received', 'unit_cost']
     */
    public function receive(array $data, array $lines): StockReceiving
    {
        return DB::transaction(function () use ($data, $lines) {
            $receiving = StockReceiving::create([
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'supplier_id' => $data['supplier_id'],
                'reference_number' => $data['reference_number'] ?? null,
                'received_by' => $data['received_by'] ?? null,
                'received_at' => $data['received_at'] ?? now(),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $item = StockReceivingItem::create([
                    'stock_receiving_id' => $receiving->id,
                    'purchase_order_item_id' => $line['purchase_order_item_id'] ?? null,
                    'product_variant_id' => $line['product_variant_id'],
                    'quantity_received' => $line['quantity_received'],
                    'unit_cost' => $line['unit_cost'],
                ]);

                StockMovement::create([
                    'product_variant_id' => $item->product_variant_id,
                    'type' => 'purchase_receipt',
                    'quantity' => $item->quantity_received,
                    'reference_type' => 'stock_receiving',
                    'reference_id' => $receiving->id,
                    'notes' => $data['notes'] ?? null,
                    'created_by' => $data['received_by'] ?? null,
                ]);

                ProductVariant::whereKey($item->product_variant_id)
                    ->increment('stock', $item->quantity_received);

                if ($item->purchase_order_item_id) {
                    $item->purchaseOrderItem()->increment(
                        'quantity_received',
                        $item->quantity_received
                    );
                }
            }

            if ($receiving->purchase_order_id) {
                $po = PurchaseOrder::find($receiving->purchase_order_id);
                $po?->refreshStatus();
            }

            return $receiving->load('items');
        });
    }
}
