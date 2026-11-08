<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrderItem;
use App\Models\StockReceivingItem;
use Illuminate\View\View;

class StockReceivingItemController extends Controller
{
    /**
     * Show every receiving record logged against a single purchase order
     * item — useful for auditing partial/split deliveries on one line.
     *
     * Not a full resource controller: receiving items are created only
     * as part of StockReceivingController@store and are never edited or
     * deleted directly, since they're part of an immutable receiving
     * history alongside StockMovement.
     */
    public function forPurchaseOrderItem(PurchaseOrderItem $purchaseOrderItem): View
    {
        $receivingItems = $purchaseOrderItem->receivingItems()
            ->with(['stockReceiving.receivedBy', 'variant'])
            ->latest('created_at')
            ->get();

        return view('admin.stock-receiving-items.for-purchase-order-item', [
            'purchaseOrderItem' => $purchaseOrderItem->load(['product', 'variant', 'purchaseOrder']),
            'receivingItems' => $receivingItems,
        ]);
    }

    /**
     * Show a single receiving item's detail.
     */
    public function show(StockReceivingItem $stockReceivingItem): View
    {
        $stockReceivingItem->load(['stockReceiving.purchaseOrder', 'purchaseOrderItem.product', 'variant']);

        return view('admin.stock-receiving-items.show', compact('stockReceivingItem'));
    }
}