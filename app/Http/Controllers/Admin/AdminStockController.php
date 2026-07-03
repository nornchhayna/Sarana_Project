<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class AdminStockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::with(['product', 'color', 'phoneStorage']);

        if ($request->filled('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('sku', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'out' => $query->where('quantity', 0),
                'low' => $query->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'low_stock_threshold'),
                'ok' => $query->whereColumn('quantity', '>', 'low_stock_threshold'),
                default => null,
            };
        }

        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        $stocks = $query->paginate(30)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name']);

        $summary = [
            'total_items' => Stock::sum('quantity'),
            'out_of_stock' => Stock::where('quantity', 0)->count(),
            'low_stock' => Stock::where('quantity', '>', 0)->whereColumn('quantity', '<=', 'low_stock_threshold')->count(),
        ];

        return view('admin.stock.index', compact('stocks', 'products', 'summary'));
    }

    public function edit(Stock $stock)
    {
        $stock->load(['product', 'color', 'phoneStorage']);

        return view('admin.stock.edit', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'adjustment_note' => 'nullable|string|max:500',
        ]);

        $stock->update([
            'quantity' => $validated['quantity'],
            'low_stock_threshold' => $validated['low_stock_threshold'],
        ]);

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stock updated successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'stocks' => 'required|array',
            'stocks.*.id' => 'required|exists:stock,id',
            'stocks.*.quantity' => 'required|integer|min:0',
            'stocks.*.low_stock_threshold' => 'required|integer|min:0',
        ]);

        foreach ($validated['stocks'] as $item) {
            Stock::where('id', $item['id'])->update([
                'quantity' => $item['quantity'],
                'low_stock_threshold' => $item['low_stock_threshold'],
            ]);
        }

        return back()->with('success', 'Stock updated in bulk successfully.');
    }

    public function adjust(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'type' => 'required|in:add,subtract,set',
            'amount' => 'required|integer|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        $newQty = match ($validated['type']) {
            'add' => $stock->quantity + $validated['amount'],
            'subtract' => max(0, $stock->quantity - $validated['amount']),
            'set' => $validated['amount'],
        };

        $stock->update(['quantity' => $newQty]);

        return back()->with('success', 'Stock adjusted successfully. New quantity: '.$newQty);
    }
}
