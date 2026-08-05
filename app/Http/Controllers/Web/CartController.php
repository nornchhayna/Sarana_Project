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
            ->load(['items.product.brand', 'items.color', 'items.storage', 'promotion']);

        return view('web.cart.index', compact('cart'));
    }

 public function add(Request $request)
{
    // 1. Mandatory Security Gate
    if (! auth()->check()) {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to your cart.',
                'redirect' => route('login'),
            ], 401);
        }
        return redirect()->route('login')
            ->with('error', 'Please login to add items to your cart.');
    }

    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'color_id' => 'nullable|exists:colors,id',
        'phone_storage_id' => 'nullable|exists:phone_storages,id',
        'quantity' => 'required|integer|min:1|max:100',
    ]);

    $product = Product::with(['variants'])->findOrFail($validated['product_id']);

    if (! $product->is_active) {
        return $this->cartError($request, 'This product is no longer available.');
    }

    $colorId = $validated['color_id'] ?? null;
    $storageId = $validated['phone_storage_id'] ?? null;

    // 3. Variant resolution
    // If the caller didn't specify color/storage (e.g. a one-click "Add to
    // cart" from a listing/wishlist page), and this product only has a
    // single variant, use that variant automatically. If it has more than
    // one, we can't guess which one they want.
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
                'Please select a color/storage option for this product before adding it to your cart.',
                needsSelection: true,
                product: $product
            );
        }
    }

    if (! $variant || $variant->stock < $validated['quantity']) {
        return $this->cartError($request, 'Insufficient stock for the selected options.');
    }

    // 4. Price Calculation
    $unitPrice = $variant->price ?? ($product->sale_price ?? $product->base_price);

    // 5. Cart Resolution & Persistence
    $cart = $this->resolveCart($request);
    $existingItem = $cart->items()
        ->where('product_id', $product->id)
        ->where('color_id', $variant->color_id)
        ->where('phone_storage_id', $variant->phone_storage_id)
        ->first();

    if ($existingItem) {
        $newQty = $existingItem->quantity + $validated['quantity'];
        if ($variant->stock < $newQty) {
            return $this->cartError($request, 'Not enough stock to add more of this item.');
        }
        $existingItem->update(['quantity' => $newQty]);
        $cartItem = $existingItem;
    } else {
        $cartItem = $cart->items()->create([
            'product_id' => $product->id,
            'color_id' => $variant->color_id,
            'phone_storage_id' => $variant->phone_storage_id,
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

        $variant = ProductVariant::where('product_id', $cartItem->product_id)
            ->where('color_id', $cartItem->color_id)
            ->where('phone_storage_id', $cartItem->phone_storage_id)
            ->first();

        if (! $variant || $variant->stock < $validated['quantity']) {
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
