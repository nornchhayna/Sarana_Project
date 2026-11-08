<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use App\Models\StockReceiving;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockReceivingController extends Controller
{
    /**
     * List all stock receivings, most recent first.
     */
    public function index(Request $request): View
    {
        $receivings = StockReceiving::query()
            ->with(['purchaseOrder', 'supplier', 'receivedBy'])
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->integer('supplier_id')))
            ->when($request->filled('purchase_order_id'), fn ($q) => $q->where('purchase_order_id', $request->integer('purchase_order_id')))
            ->latest('received_at')
            ->paginate(20)
            ->withQueryString();

        // Only POs still open for receiving can be picked from the "Receive Stock" dropdown.
        $openPurchaseOrders = PurchaseOrder::query()
            ->whereIn('status', ['ordered', 'partially_received'])
            ->with('supplier')
            ->orderBy('po_number')
            ->get();

        // Also include draft POs for debugging
        $draftPurchaseOrders = PurchaseOrder::query()
            ->where('status', 'draft')
            ->with('supplier')
            ->orderBy('po_number')
            ->get();

        // Debug: Check total stock receivings count
        $totalCount = StockReceiving::count();

        return view('admin.stock-receivings.index', compact('receivings', 'openPurchaseOrders', 'draftPurchaseOrders', 'totalCount'));
    }

    /**
     * Show the form for recording receipt of stock against a purchase order.
     * Pre-fills outstanding quantities for each item still awaiting delivery.
     */
    public function create(PurchaseOrder $purchaseOrder): View
    {
        abort_if(
            in_array($purchaseOrder->status, ['draft', 'cancelled', 'received'], true),
            403,
            'This purchase order is not open for receiving.'
        );

        $purchaseOrder->load(['items.product', 'items.variant.color', 'items.variant.phoneStorage', 'items.variant.phoneRam', 'supplier']);

        $outstandingItems = $purchaseOrder->items->filter(
            fn (PurchaseOrderItem $item) => $item->quantity_outstanding > 0
        );

        return view('admin.stock-receivings.create', compact('purchaseOrder', 'outstandingItems'));
    }

    /**
     * Record a stock receiving: creates the receiving header + line items,
     * bumps quantity_received on each purchase order item, logs a
     * StockMovement per item, and refreshes the purchase order's status.
     */
    public function store(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        abort_if(
            in_array($purchaseOrder->status, ['draft', 'cancelled', 'received'], true),
            403,
            'This purchase order is not open for receiving.'
        );

        $validated = $request->validate([
            'reference_number' => ['nullable', 'string', 'max:100'],
            'received_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_order_item_id' => ['required', 'exists:purchase_order_items,id'],
            'items.*.quantity_received' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $items = collect($validated['items'])->filter(fn ($item) => $item['quantity_received'] > 0);

        abort_if($items->isEmpty(), 422, 'At least one item must have a received quantity greater than zero.');

        $stockReceiving = DB::transaction(function () use ($validated, $items, $purchaseOrder, $request) {
            $stockReceiving = $purchaseOrder->stockReceivings()->create([
                'supplier_id' => $purchaseOrder->supplier_id,
                'reference_number' => $validated['reference_number'] ?? null,
                'received_by' => $request->user()->id,
                'received_at' => $validated['received_at'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($items as $itemInput) {
                /** @var PurchaseOrderItem $poItem */
                $poItem = $purchaseOrder->items()
                    ->whereKey($itemInput['purchase_order_item_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = min($itemInput['quantity_received'], $poItem->quantity_outstanding);

                abort_if($quantity <= 0, 422, "Item #{$poItem->id} has no outstanding quantity left to receive.");

                $stockReceiving->items()->create([
                    'purchase_order_item_id' => $poItem->id,
                    'product_variant_id' => $poItem->product_variant_id,
                    'quantity_received' => $quantity,
                    // Defaults to the PO's agreed cost; overridable per receipt
                    // in case the supplier's invoice differs (price changes, etc).
                    'unit_cost' => $itemInput['unit_cost'] ?? $poItem->unit_cost,
                ]);

                $poItem->increment('quantity_received', $quantity);

                if ($poItem->product_variant_id) {
                    $variant = \App\Models\ProductVariant::find($poItem->product_variant_id);

                    if ($variant) {
                        StockMovement::create([
                            'product_id' => $variant->product_id,
                            'product_variant_id' => $poItem->product_variant_id,
                            'type' => 'purchase_receipt',
                            'quantity' => $quantity,
                            'reference_type' => 'stock_receiving',
                            'reference_id' => $stockReceiving->id,
                            'notes' => "Received against PO {$purchaseOrder->po_number}",
                            'created_by' => $request->user()->id,
                        ]);
                        $variant->adjustStock($quantity, 'purchase_receipt');

                        // Keep Stock table in sync
                        \App\Models\Stock::where('product_id', $variant->product_id)
                            ->when($variant->color_id, fn ($q) => $q->where('color_id', $variant->color_id))
                            ->when($variant->phone_storage_id, fn ($q) => $q->where('phone_storage_id', $variant->phone_storage_id))
                            ->increment('quantity', $quantity);
                    }
                } else {
                    StockMovement::create([
                        'product_id' => $poItem->product_id,
                        'product_variant_id' => null,
                        'type' => 'purchase_receipt',
                        'quantity' => $quantity,
                        'reference_type' => 'stock_receiving',
                        'reference_id' => $stockReceiving->id,
                        'notes' => "Received against PO {$purchaseOrder->po_number}",
                        'created_by' => $request->user()->id,
                    ]);

                    $baseStock = \App\Models\Stock::firstOrCreate(
                        ['product_id' => $poItem->product_id, 'color_id' => null, 'phone_storage_id' => null],
                        ['quantity' => 0]
                    );
                    $baseStock->increment('quantity', $quantity);
                }
            }

            $purchaseOrder->refreshStatus();

            return $stockReceiving;
        });

        return redirect()
            ->route('admin.purchase-orders.show', $purchaseOrder)
            ->with('success', "Stock receiving #{$stockReceiving->id} recorded.");
    }

    /**
     * Show a single stock receiving with its line items.
     */
    public function show(StockReceiving $stockReceiving): View
    {
        $stockReceiving->load([
            'purchaseOrder',
            'supplier',
            'receivedBy',
            'items.purchaseOrderItem.product',
            'items.variant.color',
            'items.variant.phoneStorage',
            'items.variant.phoneRam',
            'movements.product',
            'movements.variant.color',
            'movements.variant.phoneStorage',
            'movements.createdBy',
        ]);

        return view('admin.stock-receivings.show', compact('stockReceiving'));
    }
}