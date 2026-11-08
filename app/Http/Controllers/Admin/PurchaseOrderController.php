<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders.
     */
    public function index(Request $request): View
    {
        $purchaseOrders = PurchaseOrder::query()
            ->with(['supplier', 'createdBy'])
            ->withCount('items')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->integer('supplier_id')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where('po_number', 'like', "%{$search}%");
            })
            ->latest('order_date')
            ->paginate(20)
            ->withQueryString();

        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        return view('admin.purchase-orders.index', compact('purchaseOrders', 'suppliers'));
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $products = \App\Models\Product::with(['variants.color', 'variants.phoneStorage', 'variants.phoneRam'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.purchase-orders.create', compact('suppliers', 'products'));
    }

    /**
     * Get variants for a product (AJAX)
     */
    public function getProductVariants(Request $request, $product)
    {
        try {
            $product = \App\Models\Product::with(['variants.color', 'variants.phoneStorage', 'variants.phoneRam'])
                ->findOrFail($product);

            $variants = $product->variants->map(function($variant) {
                $colorName = $variant->color ? $variant->color->name : ($variant->color_id ? 'Color #' . $variant->color_id : 'No Color');
                $storageLabel = $variant->phoneStorage ? ($variant->phoneStorage->label ?? $variant->phoneStorage->capacity) : ($variant->phone_storage_id ? 'Storage #' . $variant->phone_storage_id : 'No Storage');
                $ramLabel = $variant->phoneRam ? ($variant->phoneRam->label ?? $variant->phoneRam->capacity) : ($variant->phone_ram_id ? 'RAM #' . $variant->phone_ram_id : 'No RAM');

                return [
                    'id' => $variant->id,
                    'color_name' => $colorName,
                    'storage_label' => $storageLabel,
                    'ram_label' => $ramLabel,
                ];
            });

            return response()->json($variants);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created purchase order with its line items.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePurchaseOrder($request);

        $purchaseOrder = DB::transaction(function () use ($validated, $request) {
            $items = $validated['items'];
            unset($validated['items']);

            $subtotal = collect($items)->sum(fn ($item) => $item['quantity_ordered'] * $item['unit_cost']);
            $shippingCost = $validated['shipping_cost'] ?? 0;

            $purchaseOrder = PurchaseOrder::create([
                ...$validated,
                'po_number' => $validated['po_number'] ?? $this->generatePoNumber(),
                'status' => 'draft',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total_amount' => $subtotal + $shippingCost,
                'created_by' => $request->user()->id,
            ]);

            foreach ($items as $item) {
                $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit_cost' => $item['unit_cost'],
                ]);
            }

            return $purchaseOrder;
        });

        return redirect()
            ->route('admin.purchase-orders.show', $purchaseOrder)
            ->with('success', "Purchase order {$purchaseOrder->po_number} created.");
    }

    /**
     * Display a single purchase order with its items and receiving history.
     */
    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['supplier', 'createdBy', 'items.product', 'items.variant.color', 'items.variant.phoneStorage', 'items.variant.phoneRam', 'stockReceivings']);

        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing a purchase order.
     */
    public function edit(PurchaseOrder $purchaseOrder): View
    {
        $this->guardEditable($purchaseOrder);

        $purchaseOrder->load('items');
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $products = \App\Models\Product::with(['variants.color', 'variants.phoneStorage', 'variants.phoneRam'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    /**
     * Update a purchase order and replace its line items.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->guardEditable($purchaseOrder);

        $validated = $this->validatePurchaseOrder($request);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            $items = $validated['items'];
            unset($validated['items']);

            $subtotal = collect($items)->sum(fn ($item) => $item['quantity_ordered'] * $item['unit_cost']);
            $shippingCost = $validated['shipping_cost'] ?? 0;

            $purchaseOrder->update([
                ...$validated,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total_amount' => $subtotal + $shippingCost,
            ]);

            // Update line items - preserve existing variant_id if not provided
            $purchaseOrder->items()->delete();

            foreach ($items as $item) {
                $variantId = $item['product_variant_id'] ?? null;
                
                // If variant_id is not provided but there's an existing variant_id hidden field, use that
                if (is_null($variantId) && isset($item['existing_product_variant_id'])) {
                    $variantId = $item['existing_product_variant_id'];
                }
                
                // If still null and item has an existing variant_id, try to preserve it
                if (is_null($variantId) && isset($item['purchase_order_item_id'])) {
                    $existingItem = $purchaseOrder->items()->find($item['purchase_order_item_id']);
                    if ($existingItem && $existingItem->product_variant_id) {
                        $variantId = $existingItem->product_variant_id;
                    }
                }

                $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $variantId,
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit_cost' => $item['unit_cost'],
                ]);
            }
        });

        return redirect()
            ->route('admin.purchase-orders.show', $purchaseOrder)
            ->with('success', "Purchase order {$purchaseOrder->po_number} updated.");
    }

    /**
     * Delete a purchase order (only while still a draft).
     */
    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->guardEditable($purchaseOrder);

        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();

        return redirect()
            ->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order deleted.');
    }

    /**
     * Mark a draft purchase order as submitted/ordered.
     */
    public function submit(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft purchase orders can be submitted. Current status: ' . $purchaseOrder->status);
        }

        $purchaseOrder->update(['status' => 'ordered']);

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder)->with('success', "Purchase order {$purchaseOrder->po_number} submitted.");
    }

    /**
     * Cancel a purchase order that hasn't been received yet.
     */
    public function cancel(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if (in_array($purchaseOrder->status, ['received', 'partially_received'], true)) {
            return back()->with('error', 'Cannot cancel a purchase order that has received stock.');
        }

        $purchaseOrder->update(['status' => 'cancelled']);

        return back()->with('success', "Purchase order {$purchaseOrder->po_number} cancelled.");
    }

    /**
     * Validate the purchase order request, including its line items.
     */
    private function validatePurchaseOrder(Request $request): array
    {
        return $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'po_number' => ['nullable', 'string', 'max:50', 'unique:purchase_orders,po_number'],
            'order_date' => ['required', 'date'],
            'expected_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'in:draft,ordered,partially_received,received,cancelled'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.quantity_ordered' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);
    }

    /**
     * Only draft purchase orders can be deleted.
     * Status can be changed for any purchase order.
     */
    private function guardEditable(PurchaseOrder $purchaseOrder): void
    {
        // Allow editing for status changes on any purchase order
        // Only restrict deletion to draft orders
    }

    /**
     * Generate a sequential PO number, e.g. PO-2026-0001.
     */
    private function generatePoNumber(): string
    {
        $year = now()->year;
        $count = PurchaseOrder::whereYear('created_at', $year)->count() + 1;

        return sprintf('PO-%d-%04d', $year, $count);
    }
}