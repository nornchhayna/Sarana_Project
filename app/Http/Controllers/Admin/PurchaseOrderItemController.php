<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PurchaseOrderItemController extends Controller
{
    /**
     * Add a single line item to a draft purchase order.
     */
    public function store(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse|JsonResponse
    {
        $this->guardEditable($purchaseOrder);

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity_ordered' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $item = $purchaseOrder->items()->create([
            ...$validated,
            'quantity_received' => 0,
        ]);

        $this->recalculateTotals($purchaseOrder);

        if ($request->wantsJson()) {
            return response()->json($item->load(['product', 'variant']), 201);
        }

        return back()->with('success', 'Item added to purchase order.');
    }

    /**
     * Update a line item's quantity or cost on a draft purchase order.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder, PurchaseOrderItem $item): RedirectResponse|JsonResponse
    {
        $this->guardEditable($purchaseOrder);
        $this->guardBelongsToOrder($purchaseOrder, $item);

        $validated = $request->validate([
            'product_id' => ['sometimes', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity_ordered' => ['sometimes', 'integer', 'min:1'],
            'unit_cost' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $item->update($validated);

        $this->recalculateTotals($purchaseOrder);

        if ($request->wantsJson()) {
            return response()->json($item->fresh(['product', 'variant']));
        }

        return back()->with('success', 'Item updated.');
    }

    /**
     * Remove a line item from a draft purchase order.
     */
    public function destroy(Request $request, PurchaseOrder $purchaseOrder, PurchaseOrderItem $item): RedirectResponse|JsonResponse
    {
        $this->guardEditable($purchaseOrder);
        $this->guardBelongsToOrder($purchaseOrder, $item);

        abort_if($item->quantity_received > 0, 403, 'Cannot remove an item that already has received stock.');

        $item->delete();

        $this->recalculateTotals($purchaseOrder);

        if ($request->wantsJson()) {
            return response()->json(status: 204);
        }

        return back()->with('success', 'Item removed from purchase order.');
    }

    /**
     * Recalculate and persist the parent purchase order's subtotal/total.
     */
    private function recalculateTotals(PurchaseOrder $purchaseOrder): void
    {
        $subtotal = $purchaseOrder->items()
            ->get()
            ->sum(fn (PurchaseOrderItem $item) => $item->quantity_ordered * $item->unit_cost);

        $purchaseOrder->update([
            'subtotal' => $subtotal,
            'total_amount' => $subtotal + $purchaseOrder->shipping_cost,
        ]);
    }

    /**
     * Only draft purchase orders can have their items modified.
     */
    private function guardEditable(PurchaseOrder $purchaseOrder): void
    {
        abort_if($purchaseOrder->status !== 'draft', 403, 'Only draft purchase orders can be modified.');
    }

    /**
     * Ensure the item actually belongs to the given purchase order (route-model binding safety).
     */
    private function guardBelongsToOrder(PurchaseOrder $purchaseOrder, PurchaseOrderItem $item): void
    {
        abort_unless($item->purchase_order_id === $purchaseOrder->id, 404);
    }
}