<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    /**
     * Movement types that can be recorded directly by a user (as opposed
     * to being generated automatically by receiving, sales, etc.).
     */
    private const MANUAL_TYPES = ['adjustment_in', 'adjustment_out', 'damage'];

    /**
     * Display the stock movement ledger, filterable by variant/type/date.
     */
    public function index(Request $request): View
    {
        $movements = StockMovement::query()
            ->with(['product', 'variant.product', 'variant.color', 'variant.phoneStorage', 'variant.phoneRam', 'createdBy'])
            ->when($request->filled('product_id'), fn ($q) => $q->where('product_id', $request->integer('product_id')))
            ->when($request->filled('product_variant_id'), fn ($q) => $q->where('product_variant_id', $request->integer('product_variant_id')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('reference_type'), fn ($q) => $q->where('reference_type', $request->string('reference_type')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"))
                        ->orWhereHas('variant.product', fn ($vpq) => $vpq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('variant', fn ($vq) => $vq->where('sku', 'like', "%{$search}%"))
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')))
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('admin.stock-movements.index', compact('movements'));
    }

    /**
     * Show the full ledger for a single variant, plus its running balance.
     */
    public function forVariant(ProductVariant $variant): View
    {
        $movements = $variant->stockMovements()
            ->with('createdBy')
            ->latest('created_at')
            ->paginate(50);

        return view('admin.stock-movements.variant', compact('variant', 'movements'));
    }

    /**
     * Show the form for a manual stock adjustment.
     */
    public function create(): View
    {
        $variants = ProductVariant::with('product')->orderBy('id')->get();

        return view('admin.stock-movements.create', compact('variants'));
    }

    /**
     * Record a manual stock adjustment (e.g. damage, recount, correction).
     * Automated movements (PO receiving, sales, etc.) should be created by
     * their own controllers/services, not through this endpoint.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'type' => ['required', 'in:' . implode(',', self::MANUAL_TYPES)],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        $variant = ProductVariant::find($validated['product_variant_id']);

        if ($variant) {
            if (in_array($validated['type'], ['adjustment_out', 'damage']) && $variant->stock < $validated['quantity']) {
                return back()->withInput()->with('error', "Cannot deduct {$validated['quantity']} units. Current stock is only {$variant->stock}.");
            }

            StockMovement::create([
                ...$validated,
                'reference_type' => 'manual_adjustment',
                'reference_id' => null,
                'created_by' => $request->user()->id,
            ]);
            $variant->adjustStock($validated['quantity'], $validated['type']);
        } else {
            return back()->with('error', 'Product variant not found.');
        }

        return redirect()
            ->route('admin.stock-movements.index')
            ->with('success', 'Stock adjustment recorded.');
    }

    /**
     * Show a single movement's detail, resolving its reference record
     * manually since reference_type is a short label, not a class name.
     */
    public function show(StockMovement $stockMovement): View
    {
        $stockMovement->load(['product', 'variant.product', 'variant.color', 'variant.phoneStorage', 'createdBy']);

        $reference = $this->resolveReference($stockMovement);

        return view('admin.stock-movements.show', [
            'movement' => $stockMovement,
            'reference' => $reference,
        ]);
    }

    /**
     * Map reference_type labels to their model class for manual lookup.
     * Movements are append-only, so there is no edit/destroy — history
     * must never be altered; corrections are made with an offsetting
     * movement instead.
     */
    private function resolveReference(StockMovement $movement): ?object
    {
        return match ($movement->reference_type) {
            'stock_receiving' => \App\Models\StockReceiving::find($movement->reference_id),
            'purchase_order' => \App\Models\PurchaseOrder::find($movement->reference_id),
            'order' => \App\Models\Order::find($movement->reference_id),
            default => null,
        };
    }
}