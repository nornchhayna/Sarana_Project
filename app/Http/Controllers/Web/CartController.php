<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Stock;
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
            return redirect()->route('login')
                ->with('error', 'Please login to add items to your cart.');
        }
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'nullable|exists:colors,id',
            'phone_storage_id' => 'nullable|exists:phone_storages,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $product = Product::with(['colors', 'storages'])->findOrFail($validated['product_id']);

        if (! $product->is_active) {

            return back()->with('error', 'This product is no longer available.');
        }

        $stock = Stock::where('product_id', $product->id)
            ->where('color_id', $validated['color_id'] ?? null)
            ->where('phone_storage_id', $validated['phone_storage_id'] ?? null)
            ->first();

        if (! $stock || $stock->quantity < $validated['quantity']) {

            return back()->with('error', 'Insufficient stock for the selected options.');
        }

        // 4. Price Calculation
        $unitPrice = $product->sale_price ?? $product->base_price;
        if (! empty($validated['color_id'])) {
            $colorAdj = $product->colors()->where('color_id', $validated['color_id'])->first()?->pivot->price_adjustment ?? 0;
            $unitPrice += $colorAdj;
        }
        if (! empty($validated['phone_storage_id'])) {
            $storageAdj = $product->storages()->where('phone_storage_id', $validated['phone_storage_id'])->first()?->pivot->price_adjustment ?? 0;
            $unitPrice += $storageAdj;
        }

        // 5. Cart Resolution & Persistence
        $cart = $this->resolveCart($request);
        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('color_id', $validated['color_id'] ?? null)
            ->where('phone_storage_id', $validated['phone_storage_id'] ?? null)
            ->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $validated['quantity'];
            if ($stock->quantity < $newQty) {
                return back()->with('error', 'Not enough stock to add more of this item.');
            }
            $existingItem->update(['quantity' => $newQty]);
        } else {
            // Note: 'total_price' is removed here to prevent SQL errors
            $cart->items()->create([
                'product_id' => $product->id,
                'color_id' => $validated['color_id'] ?? null,
                'phone_storage_id' => $validated['phone_storage_id'] ?? null,
                'quantity' => $validated['quantity'],
                'unit_price' => $unitPrice,
            ]);
        }
        // 6. Redirect based on buy_now flag
        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', 'Item added to cart.');
    }

    // ─── Update quantity ────────────────────────────────────────────────────

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($request, $cartItem);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $stock = Stock::where('product_id', $cartItem->product_id)
            ->where('color_id', $cartItem->color_id)
            ->where('phone_storage_id', $cartItem->phone_storage_id)
            ->first();

        if (! $stock || $stock->quantity < $validated['quantity']) {
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
