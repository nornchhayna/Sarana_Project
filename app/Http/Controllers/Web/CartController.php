<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function resolveCart(Request $request): Cart
    {
        if (auth()->check()) {

            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }
        $sessionId = $request->session()->get('cart_session_id');

        if (! $sessionId) {

            $sessionId = \Str::uuid()->toString();
            $request->session()->put('cart_session_id', $sessionId);

        }

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function index(Request $request)
    {
        $cart = $this->resolveCart($request)
            ->load(['items.product.brand', 'items.color', 'items.storage', 'items.product.variantImages', 'items.product.gifts', 'promotion']);

        // Fetch related products (deals of the day)
        $related = collect();
        if ($cart->items->isNotEmpty()) {
            $categoryIds = $cart->items->pluck('product.category_id')->unique()->toArray();
            $related = Product::with(['brand', 'stock'])
                ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
                ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
                ->where('is_active', true)
                ->whereIn('category_id', $categoryIds)
                ->whereNotIn('id', $cart->items->pluck('product_id'))
                ->inRandomOrder()
                ->take(8)
                ->get();
        }

        return view('web.cart.index', compact('cart', 'related'));
    }

 public function add(Request $request)
{
    // 1. Mandatory Security Gate
    if (! auth()->check()) {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to your cart.',
                'redirect' => route('auth.login'),
            ], 401);
        }
        return redirect()->route('auth.login')
            ->with('error', 'Please login to add items to your cart.');
    }

    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'product_variant_id' => 'nullable|exists:product_variants,id',
        'color_id' => 'nullable|exists:colors,id',
        'phone_storage_id' => 'nullable|exists:phone_storages,id',
        'quantity' => 'required|integer|min:1|max:100',
    ]);

    $product = Product::with(['variants'])->findOrFail($validated['product_id']);

    if (! $product->is_active) {
        return $this->cartError($request, 'This product is no longer available.');
    }

    // 3. Variant resolution
    // If product_variant_id is provided, use it directly
    if (! empty($validated['product_variant_id'])) {
        $variant = ProductVariant::where('id', $validated['product_variant_id'])
            ->where('product_id', $product->id)
            ->first();
    } else {
        // Fallback to old color/storage method for backward compatibility
        $colorId = $validated['color_id'] ?? null;
        $storageId = $validated['phone_storage_id'] ?? null;

        $variant = ProductVariant::where('product_id', $product->id)
            ->where('color_id', $colorId)
            ->where('phone_storage_id', $storageId)
            ->first();

        if (! $variant && $colorId === null && $storageId === null) {
            if ($product->variants->count() === 1) {
                $variant = $product->variants->first();
            } elseif ($product->variants->count() > 1) {
                return $this->cartError(
                    $request,
                    'Please select a variant for this product before adding it to your cart.',
                    needsSelection: true,
                    product: $product
                );
            }
        }
    }

    if ($variant) {
        if ($variant->stock < $validated['quantity']) {
            return $this->cartError($request, 'Insufficient stock for the selected options.');
        }
        $availableStock = $variant->stock;
        $unitPrice = $variant->price ?? ($product->sale_price ?? $product->base_price);
    } else {
        if ($product->variants->isNotEmpty()) {
            return $this->cartError($request, 'Please select a variant for this product before adding it to your cart.', needsSelection: true, product: $product);
        }

        $availableStock = $product->total_stock;
        if ($availableStock < $validated['quantity']) {
            return $this->cartError($request, $availableStock <= 0 ? 'This product is out of stock.' : "Only {$availableStock} items available in stock.");
        }
        $unitPrice = $product->sale_price ?? $product->base_price;
    }

    // 4. Cart Resolution & Persistence
    $cart = $this->resolveCart($request);
    $existingQuery = $cart->items()->where('product_id', $product->id);
    if ($variant) {
        $existingQuery->where('product_variant_id', $variant->id);
    } else {
        $existingQuery->whereNull('product_variant_id');
    }
    $existingItem = $existingQuery->first();

    if ($existingItem) {
        $newQty = $existingItem->quantity + $validated['quantity'];
        if ($availableStock < $newQty) {
            return $this->cartError($request, 'Not enough stock to add more of this item.');
        }
        $existingItem->update(['quantity' => $newQty]);
        $cartItem = $existingItem;
    } else {
        $cartItem = $cart->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'color_id' => $variant?->color_id ?? ($validated['color_id'] ?? null),
            'phone_storage_id' => $variant?->phone_storage_id ?? ($validated['phone_storage_id'] ?? null),
            'phone_ram_id' => $variant?->phone_ram_id ?? null,
            'quantity' => $validated['quantity'],
            'unit_price' => $unitPrice,
        ]);
    }

    // 6. Redirect based on buy_now flag
    if ($request->boolean('buy_now')) {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('checkout.index'),
            ]);
        }
        return redirect()->route('checkout.index');
    }

    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Item added to cart.',
            'cart_item_id' => $cartItem->id,
            'cart_count' => $cart->items()->sum('quantity'),
        ]);
    }

    return back()->with('success', 'Item added to cart.');
}

/**
 * Return an error either as JSON (for fetch/AJAX callers) or as a
 * classic redirect-back-with-flash (for plain form submits).
 */
protected function cartError(Request $request, string $message, bool $needsSelection = false, ?Product $product = null)
{
    if ($request->wantsJson()) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'needs_selection' => $needsSelection,
            'product_url' => $product ? route('products.show', $product->slug) : null,
        ], 422);
    }

    return back()->with('error', $message);
}

    // ─── Update quantity ────────────────────────────────────────────────────

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($request, $cartItem);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        // Use product_variant_id if available, otherwise fallback to color/storage/product
        if ($cartItem->product_variant_id) {
            $variant = ProductVariant::where('id', $cartItem->product_variant_id)
                ->where('product_id', $cartItem->product_id)
                ->first();
            $availableStock = $variant?->stock ?? 0;
        } else {
            $variant = ProductVariant::where('product_id', $cartItem->product_id)
                ->where('color_id', $cartItem->color_id)
                ->where('phone_storage_id', $cartItem->phone_storage_id)
                ->first();
            if ($variant) {
                $availableStock = $variant->stock;
            } else {
                $availableStock = $cartItem->product ? $cartItem->product->total_stock : 0;
            }
        }

        if ($availableStock < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock.');
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($request, $cartItem);
        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    // ─── Apply promotion code ───────────────────────────────────────────────

    public function applyPromotion(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $promotion = Promotion::where('code', $request->code)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->first();

        if (! $promotion) {
            return back()->with('error', 'Invalid or expired promotion code.');
        }

        if ($promotion->usage_limit && $promotion->usage_count >= $promotion->usage_limit) {
            return back()->with('error', 'This promotion has reached its usage limit.');
        }

        $cart = $this->resolveCart($request);
        $cart->update(['promotion_id' => $promotion->id]);

        // Use single quotes on the outside so double quotes inside are treated as text
        return back()->with('success', 'Promotion "'.$promotion->name.'" applied.');
    }

    // ─── Remove promotion ───────────────────────────────────────────────────

    public function removePromotion(Request $request)
    {
        $cart = $this->resolveCart($request);
        $cart->update(['promotion_id' => null]);

        return back()->with('success', 'Promotion removed.');
    }

    // ─── Helper: ensure the cart item belongs to the current cart ───────────

    private function authorizeCartItem(Request $request, CartItem $item): void
    {
        $cart = $this->resolveCart($request);

        abort_if($item->cart_id !== $cart->id, 403);
    }
}
