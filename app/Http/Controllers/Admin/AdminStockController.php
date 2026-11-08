<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminStockController extends Controller
{
    public function index(Request $request)
    {
        $productsQuery = Product::with([
            'variants.color',
            'variants.phoneStorage',
            'variants.phoneRam',
            'stock.color',
            'stock.phoneStorage',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhereHas('variants', function ($vq) use ($search) {
                        $vq->where('sku', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($request->filled('product')) {
            $productsQuery->where('id', $request->product);
        }

        $allProducts = $productsQuery->orderBy('name')->get();

        // Build groups for each product
        $groupedStocks = $allProducts->map(function ($product) {
            $hasVariants = $product->variants->isNotEmpty();
            $variants = $product->variants;
            $totalStock = $product->total_stock;
            $variantCount = $variants->count();
            $baseStock = $product->stock->first();

            return [
                'product' => $product,
                'variants' => $variants,
                'total_stock' => $totalStock,
                'variant_count' => $variantCount,
                'has_variants' => $hasVariants,
                'base_stock' => $baseStock,
            ];
        });

        // Apply stock status filter after calculating total stock
        if ($request->filled('stock_status')) {
            $groupedStocks = $groupedStocks->filter(function ($item) use ($request) {
                return match ($request->stock_status) {
                    'out' => $item['total_stock'] == 0,
                    'low' => $item['total_stock'] > 0 && $item['total_stock'] <= 10,
                    'ok' => $item['total_stock'] > 10,
                    default => true,
                };
            })->values();
        }

        // Create a paginator for the grouped results
        $perPage = 20;
        $currentPage = (int) $request->get('page', 1);
        $currentItems = $groupedStocks->forPage($currentPage, $perPage);
        $groupedStocksPaginated = new LengthAwarePaginator(
            $currentItems,
            $groupedStocks->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $products = Product::orderBy('name')->get(['id', 'name']);

        // Summary counters across all products
        $allTotals = $allProducts->map(fn ($p) => $p->total_stock);
        $summary = [
            'total_items' => $allTotals->sum(),
            'out_of_stock' => $allTotals->filter(fn ($s) => $s == 0)->count(),
            'low_stock' => $allTotals->filter(fn ($s) => $s > 0 && $s <= 10)->count(),
        ];

        return view('admin.stock.index', compact('groupedStocksPaginated', 'products', 'summary'));
    }

    protected function resolveStockItem($id, ?string $type = null)
    {
        if ($type === 'product') {
            $product = Product::findOrFail($id);
            $stock = Stock::firstOrCreate(
                ['product_id' => $product->id, 'color_id' => null, 'phone_storage_id' => null],
                ['quantity' => 0]
            );
            $stock->load(['product', 'color', 'phoneStorage']);
            return ['item' => $stock, 'is_variant' => false, 'product' => $product];
        }

        if ($type === 'stock') {
            $stock = Stock::with(['product', 'color', 'phoneStorage'])->findOrFail($id);
            return ['item' => $stock, 'is_variant' => false, 'product' => $stock->product];
        }

        // Auto-detect: try variant first, then stock, then product
        $variant = ProductVariant::with(['product', 'color', 'phoneStorage'])->find($id);
        if ($variant) {
            return ['item' => $variant, 'is_variant' => true, 'product' => $variant->product];
        }

        $stock = Stock::with(['product', 'color', 'phoneStorage'])->find($id);
        if ($stock) {
            return ['item' => $stock, 'is_variant' => false, 'product' => $stock->product];
        }

        $product = Product::findOrFail($id);
        $stock = Stock::firstOrCreate(
            ['product_id' => $product->id, 'color_id' => null, 'phone_storage_id' => null],
            ['quantity' => 0]
        );
        $stock->load(['product', 'color', 'phoneStorage']);
        return ['item' => $stock, 'is_variant' => false, 'product' => $product];
    }

    public function edit(Request $request, $id)
    {
        $resolved = $this->resolveStockItem($id, $request->query('type'));
        $stock = $resolved['item'];
        $isVariant = $resolved['is_variant'];
        $product = $resolved['product'];

        return view('admin.stock.edit', compact('stock', 'isVariant', 'product'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'stock' => 'nullable|integer|min:0',
            'quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'adjustment_note' => 'nullable|string',
            'sku' => 'nullable|string',
        ]);

        $resolved = $this->resolveStockItem($id, $request->query('type') ?? $request->input('stock_type'));
        $item = $resolved['item'];
        $isVariant = $resolved['is_variant'];
        $product = $resolved['product'];

        $newQty = $validated['stock'] ?? ($validated['quantity'] ?? 0);

        if ($isVariant) {
            $oldStock = $item->stock;
            $diff = $newQty - $oldStock;

            $item->update([
                'stock' => $newQty,
                'sku' => $validated['sku'] ?? $item->sku,
            ]);

            // Sync corresponding Stock table entry
            Stock::updateOrCreate(
                ['product_id' => $item->product_id, 'color_id' => $item->color_id, 'phone_storage_id' => $item->phone_storage_id],
                ['quantity' => $newQty]
            );

            if ($diff != 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $item->id,
                    'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                    'quantity' => abs($diff),
                    'reference_type' => 'manual_adjustment',
                    'reference_id' => null,
                    'notes' => (!empty($validated['adjustment_note'])) ? $validated['adjustment_note'] : ('Stock updated via admin stock panel (from ' . $oldStock . ' to ' . $newQty . ')'),
                    'created_by' => auth()->id(),
                ]);
            }
        } else {
            $oldStock = $item->quantity;
            $diff = $newQty - $oldStock;

            $item->update([
                'quantity' => $newQty,
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? $item->low_stock_threshold ?? 5,
            ]);

            if (!empty($validated['sku']) && $product) {
                $product->update(['sku' => $validated['sku']]);
            }

            if ($diff != 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                    'quantity' => abs($diff),
                    'reference_type' => 'manual_adjustment',
                    'reference_id' => null,
                    'notes' => (!empty($validated['adjustment_note'])) ? $validated['adjustment_note'] : ('Stock updated via admin stock panel (from ' . $oldStock . ' to ' . $newQty . ')'),
                    'created_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stock updated successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'stocks' => 'nullable|array',
            'stocks.*.stock' => 'required|integer|min:0',
            'stocks.*.sku' => 'nullable|string',
            'product_stocks' => 'nullable|array',
            'product_stocks.*.stock' => 'required|integer|min:0',
            'product_stocks.*.sku' => 'nullable|string',
        ]);

        // Bulk update variants
        if (! empty($validated['stocks'])) {
            foreach ($validated['stocks'] as $variantId => $data) {
                $variant = ProductVariant::find($variantId);
                if ($variant) {
                    $oldStock = $variant->stock;
                    $newStock = $data['stock'];
                    $diff = $newStock - $oldStock;

                    $variant->update([
                        'stock' => $newStock,
                        'sku' => $data['sku'] ?? $variant->sku,
                    ]);

                    Stock::updateOrCreate(
                        ['product_id' => $variant->product_id, 'color_id' => $variant->color_id, 'phone_storage_id' => $variant->phone_storage_id],
                        ['quantity' => $newStock]
                    );

                    if ($diff != 0) {
                        StockMovement::create([
                            'product_id' => $variant->product_id,
                            'product_variant_id' => $variant->id,
                            'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                            'quantity' => abs($diff),
                            'reference_type' => 'manual_adjustment',
                            'reference_id' => null,
                            'notes' => 'Bulk stock update (from ' . $oldStock . ' to ' . $newStock . ')',
                            'created_by' => auth()->id(),
                        ]);
                    }
                }
            }
        }

        // Bulk update non-variant products
        if (! empty($validated['product_stocks'])) {
            foreach ($validated['product_stocks'] as $productId => $data) {
                $product = Product::find($productId);
                if ($product) {
                    $stock = Stock::firstOrCreate(
                        ['product_id' => $product->id, 'color_id' => null, 'phone_storage_id' => null],
                        ['quantity' => 0]
                    );
                    $oldStock = $stock->quantity;
                    $newStock = $data['stock'];
                    $diff = $newStock - $oldStock;

                    $stock->update(['quantity' => $newStock]);

                    if (! empty($data['sku'])) {
                        $product->update(['sku' => $data['sku']]);
                    }

                    if ($diff != 0) {
                        StockMovement::create([
                            'product_id' => $product->id,
                            'product_variant_id' => null,
                            'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                            'quantity' => abs($diff),
                            'reference_type' => 'manual_adjustment',
                            'reference_id' => null,
                            'notes' => 'Bulk product stock update (from ' . $oldStock . ' to ' . $newStock . ')',
                            'created_by' => auth()->id(),
                        ]);
                    }
                }
            }
        }

        return back()->with('success', 'Stock updated in bulk successfully.');
    }

    public function adjust(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:add,subtract,set',
            'amount' => 'required|integer|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        $resolved = $this->resolveStockItem($id, $request->query('type') ?? $request->input('stock_type'));
        $item = $resolved['item'];
        $isVariant = $resolved['is_variant'];
        $product = $resolved['product'];

        $oldStock = $isVariant ? $item->stock : $item->quantity;
        $newQty = match ($validated['type']) {
            'add' => $oldStock + $validated['amount'],
            'subtract' => max(0, $oldStock - $validated['amount']),
            'set' => $validated['amount'],
        };

        $diff = $newQty - $oldStock;

        if ($isVariant) {
            $item->update(['stock' => $newQty]);
            Stock::updateOrCreate(
                ['product_id' => $item->product_id, 'color_id' => $item->color_id, 'phone_storage_id' => $item->phone_storage_id],
                ['quantity' => $newQty]
            );
            $variantId = $item->id;
        } else {
            $item->update(['quantity' => $newQty]);
            $variantId = null;
        }

        if ($diff != 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                'quantity' => abs($diff),
                'reference_type' => 'manual_adjustment',
                'reference_id' => null,
                'notes' => $validated['note'] ?: ('Stock adjusted via quick action: ' . $validated['type'] . ' ' . $validated['amount']),
                'created_by' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Stock adjusted successfully. New quantity: ' . $newQty);
    }
}
