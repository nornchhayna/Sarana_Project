<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class AdminStockController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with(['product', 'color', 'phoneStorage']);

        if ($request->filled('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('sku', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'out' => $query->where('stock', 0),
                'low' => $query->where('stock', '>', 0)->where('stock', '<=', 10),
                'ok' => $query->where('stock', '>', 10),
                default => null,
            };
        }

        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        $stocks = $query->paginate(30)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name']);

        $summary = [
            'total_items' => ProductVariant::sum('stock'),
            'out_of_stock' => ProductVariant::where('stock', 0)->count(),
            'low_stock' => ProductVariant::where('stock', '>', 0)->where('stock', '<=', 10)->count(),
        ];

        return view('admin.stock.index', compact('stocks', 'products', 'summary'));
    }

    public function edit(ProductVariant $stock)
    {
        $stock->load(['product', 'color', 'phoneStorage']);

        return view('admin.stock.edit', compact('stock'));
    }

    public function update(Request $request, ProductVariant $stock)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string',
        ]);

        $stock->update([
            'stock' => $validated['stock'],
            'sku' => $validated['sku'] ?? $stock->sku,
        ]);

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stock updated successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'stocks' => 'required|array',
            'stocks.*.id' => 'required|exists:product_variants,id',
            'stocks.*.stock' => 'required|integer|min:0',
        ]);

        foreach ($validated['stocks'] as $item) {
            ProductVariant::where('id', $item['id'])->update([
                'stock' => $item['stock'],
            ]);
        }

        return back()->with('success', 'Stock updated in bulk successfully.');
    }

    public function adjust(Request $request, ProductVariant $stock)
    {
        $validated = $request->validate([
            'type' => 'required|in:add,subtract,set',
            'amount' => 'required|integer|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        $newQty = match ($validated['type']) {
            'add' => $stock->stock + $validated['amount'],
            'subtract' => max(0, $stock->stock - $validated['amount']),
            'set' => $validated['amount'],
        };

        $stock->update(['stock' => $newQty]);

        return back()->with('success', 'Stock adjusted successfully. New quantity: '.$newQty);
    }
}
