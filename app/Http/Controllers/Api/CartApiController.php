<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    /**
     * Resolve the cart for this request.
     */
    private function resolveCart(Request $request): Cart
    {
        if ($request->user()) {
            return Cart::firstOrCreate(['user_id' => $request->user()->id]);
        }

        $deviceId = $request->header('X-Device-Id');

        abort_if(
            ! $deviceId,
            428,
            'Missing X-Device-Id header. Generate a UUID on first app launch and send it with every request.'
        );

        return Cart::firstOrCreate(['session_id' => $deviceId]);
    }

    /**
     * GET /api/v1/cart
     */
    public function index(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request)
            ->load([
                'items.product.brand',
                'items.product.category',
                'items.color',
                'items.storage',
                'items.ram',
                'items.product.variantImages',
                'items.product.gifts',
                'promotion',
            ]);

        $related = collect();
        if ($cart->items->isNotEmpty()) {
            $categoryIds = $cart->items->pluck('product.category_id')->filter()->unique()->toArray();
            $related = Product::with(['brand', 'variants', 'stock'])
                ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
                ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
                ->where('is_active', true)
                ->whereIn('category_id', $categoryIds)
                ->whereNotIn('id', $cart->items->pluck('product_id'))
                ->inRandomOrder()
                ->take(8)
                ->get();
        }

        $cartData = $this->transformCart($cart);

        return response()->json([
            'success' => true,
            'cart' => $cartData,
            'data' => $cartData,
            'related_products' => $related->map(fn ($p) => $this->transformProductSummary($p)),
        ]);
    }

    /**
     * POST /api/v1/cart/add
     */
    public function add(Request $request): JsonResponse
    {
        if (! $request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to your cart.',
                'error_code' => 'UNAUTHENTICATED',
            ], 401);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'color_id' => 'nullable|exists:colors,id',
            'phone_storage_id' => 'nullable|exists:phone_storages,id',
            'storage_id' => 'nullable|exists:phone_storages,id',
            'phone_ram_id' => 'nullable|exists:phone_rams,id',
            'ram_id' => 'nullable|exists:phone_rams,id',
            'quantity' => 'nullable|integer|min:1|max:100',
        ]);

        $quantity = (int) ($validated['quantity'] ?? 1);
        $product = Product::with(['variants', 'stock'])->findOrFail($validated['product_id']);

        if (! $product->is_active) {
            return $this->cartError('This product is no longer available.');
        }

        $colorId = $validated['color_id'] ?? null;
        $storageId = $validated['phone_storage_id'] ?? ($validated['storage_id'] ?? null);
        $ramId = $validated['phone_ram_id'] ?? ($validated['ram_id'] ?? null);

        $variant = null;
        if (! empty($validated['product_variant_id'])) {
            $variant = ProductVariant::where('id', $validated['product_variant_id'])
                ->where('product_id', $product->id)
                ->first();
        } else {
            if ($colorId !== null || $storageId !== null || $ramId !== null) {
                $variantQuery = ProductVariant::where('product_id', $product->id);
                if ($colorId !== null) {
                    $variantQuery->where('color_id', $colorId);
                }
                if ($storageId !== null) {
                    $variantQuery->where('phone_storage_id', $storageId);
                }
                if ($ramId !== null) {
                    $variantQuery->where('phone_ram_id', $ramId);
                }
                $variant = $variantQuery->first();
            }

            if (! $variant && $product->variants->isNotEmpty() && $colorId === null && $storageId === null && $ramId === null) {
                $variant = $product->variants->firstWhere('stock', '>', 0)
                    ?? $product->variants->first();
            }
        }

        if ($variant) {
            if ($variant->stock <= 0) {
                return $this->cartError('The selected option is out of stock.');
            }
            if ($variant->stock < $quantity) {
                return $this->cartError("Only {$variant->stock} items available in stock for this selection.");
            }
            $unitPrice = (float) ($variant->price ?? ($product->sale_price ?? $product->base_price));
        } else {
            if ($product->variants->isNotEmpty() && ($colorId !== null || $storageId !== null || $ramId !== null)) {
                return $this->cartError('The selected combination is unavailable.');
            }

            // Check stock table if specific color/storage was selected
            if ($colorId !== null || $storageId !== null) {
                $specificStock = $product->stock()
                    ->when($colorId, fn ($q) => $q->where('color_id', $colorId))
                    ->when($storageId, fn ($q) => $q->where('phone_storage_id', $storageId))
                    ->sum('quantity');

                if ($product->stock()->whereNotNull('color_id')->exists() && $specificStock < $quantity) {
                    return $this->cartError($specificStock <= 0
                        ? 'The selected option is out of stock.'
                        : "Only {$specificStock} items available in stock for this selection.");
                }
            }

            $totalStock = $product->total_stock;
            if ($totalStock <= 0) {
                return $this->cartError('This product is out of stock.');
            }
            if ($totalStock < $quantity) {
                return $this->cartError("Only {$totalStock} items available in stock.");
            }
            $unitPrice = (float) ($product->sale_price ?? $product->base_price);
        }

        $cart = $this->resolveCart($request);
        $existingQuery = $cart->items()->where('product_id', $product->id);
        if ($variant) {
            $existingQuery->where('product_variant_id', $variant->id);
        } else {
            $existingQuery->whereNull('product_variant_id');
            if ($colorId !== null) {
                $existingQuery->where('color_id', $colorId);
            }
            if ($storageId !== null) {
                $existingQuery->where('phone_storage_id', $storageId);
            }
        }
        $existingItem = $existingQuery->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $quantity;
            $maxStock = $variant ? $variant->stock : $product->total_stock;
            if ($maxStock < $newQty) {
                return $this->cartError("Cannot add more. Only {$maxStock} items available in stock.");
            }
            $existingItem->update(['quantity' => $newQty]);
            $cartItem = $existingItem;
        } else {
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'color_id' => $variant?->color_id ?? $colorId,
                'phone_storage_id' => $variant?->phone_storage_id ?? $storageId,
                'phone_ram_id' => $variant?->phone_ram_id ?? $ramId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        $cart->load([
            'items.product.brand',
            'items.product.category',
            'items.color',
            'items.storage',
            'promotion',
        ]);
        $cartData = $this->transformCart($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart.',
            'cart_item_id' => $cartItem->id,
            'cart_count' => $cart->items->sum('quantity'),
            'buy_now' => $request->boolean('buy_now'),
            'cart' => $cartData,
            'data' => $cartData,
        ]);
    }

    /**
     * PATCH /api/v1/cart/items/{cartItem}
     */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        $this->authorizeCartItem($request, $cartItem);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $variant = null;
        if ($cartItem->product_variant_id) {
            $variant = ProductVariant::where('id', $cartItem->product_variant_id)
                ->where('product_id', $cartItem->product_id)
                ->first();
        } elseif ($cartItem->color_id || $cartItem->phone_storage_id) {
            $variant = ProductVariant::where('product_id', $cartItem->product_id)
                ->where('color_id', $cartItem->color_id)
                ->where('phone_storage_id', $cartItem->phone_storage_id)
                ->first();
        }

        if ($variant && $variant->stock > 0 && $variant->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock.',
            ], 422);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        $cart = $this->resolveCart($request)->fresh([
            'items.product.brand',
            'items.product.category',
            'items.color',
            'items.storage',
            'promotion',
        ]);
        $cartData = $this->transformCart($cart);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated.',
            'cart_item' => $this->transformCartItem($cartItem->fresh(['product.brand', 'product.category', 'color', 'storage'])),
            'cart_count' => $cart->items->sum('quantity'),
            'cart' => $cartData,
            'data' => $cartData,
        ]);
    }

    /**
     * DELETE /api/v1/cart/items/{cartItem}
     */
    public function remove(Request $request, CartItem $cartItem): JsonResponse
    {
        $this->authorizeCartItem($request, $cartItem);
        $cartItem->delete();

        $cart = $this->resolveCart($request)->fresh([
            'items.product.brand',
            'items.product.category',
            'items.color',
            'items.storage',
            'promotion',
        ]);
        $cartData = $this->transformCart($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'cart_count' => $cart->items->sum('quantity'),
            'cart' => $cartData,
            'data' => $cartData,
        ]);
    }

    /**
     * DELETE /api/v1/cart/clear
     */
    public function clear(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);
        $cart->items()->delete();
        $cart->update(['promotion_id' => null]);

        $cartData = $this->transformCart($cart->fresh(['items']));

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared.',
            'cart' => $cartData,
            'data' => $cartData,
        ]);
    }

    /**
     * POST /api/v1/cart/sync
     */
    public function sync(Request $request): JsonResponse
    {
        if ($request->has('items') && is_array($request->input('items'))) {
            $cart = $this->resolveCart($request);
            foreach ($request->input('items') as $item) {
                $productId = $item['product_id'] ?? null;
                $qty = max(1, (int) ($item['quantity'] ?? 1));
                if (! $productId) {
                    continue;
                }
                $product = Product::with('variants')->find($productId);
                if (! $product) {
                    continue;
                }
                $variant = $product->variants->first();
                $unitPrice = (float) ($variant?->price ?? ($product->sale_price ?? $product->base_price));
                $existing = $cart->items()
                    ->where('product_id', $product->id)
                    ->where('product_variant_id', $variant?->id)
                    ->first();
                if ($existing) {
                    $existing->update(['quantity' => $qty]);
                } else {
                    $cart->items()->create([
                        'product_id' => $product->id,
                        'product_variant_id' => $variant?->id,
                        'color_id' => $variant?->color_id,
                        'phone_storage_id' => $variant?->phone_storage_id,
                        'phone_ram_id' => $variant?->phone_ram_id,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                    ]);
                }
            }
            $cart->load(['items.product.brand', 'items.product.category', 'items.color', 'items.storage', 'promotion']);
            $cartData = $this->transformCart($cart);

            return response()->json([
                'success' => true,
                'message' => 'Cart synced.',
                'cart' => $cartData,
                'data' => $cartData,
            ]);
        }

        if ($request->header('X-Device-Id')) {
            return $this->merge($request);
        }

        $cart = $this->resolveCart($request)->load(['items.product.brand', 'items.product.category', 'items.color', 'items.storage', 'promotion']);
        $cartData = $this->transformCart($cart);

        return response()->json([
            'success' => true,
            'message' => 'Cart synced.',
            'cart' => $cartData,
            'data' => $cartData,
        ]);
    }

    /**
     * POST /api/cart/promotion
     */
    public function applyPromotion(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);

        $promotion = Promotion::where('code', $request->code)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->first();

        if (! $promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired promotion code.',
            ], 422);
        }

        if ($promotion->usage_limit && $promotion->usage_count >= $promotion->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'This promotion has reached its usage limit.',
            ], 422);
        }

        $cart = $this->resolveCart($request);
        $cart->update(['promotion_id' => $promotion->id]);

        $cartData = $this->transformCart($cart->fresh(['items.product.brand', 'items.product.category', 'promotion']));

        return response()->json([
            'success' => true,
            'message' => "Promotion \"{$promotion->name}\" applied.",
            'cart' => $cartData,
            'data' => $cartData,
        ]);
    }

    /**
     * DELETE /api/cart/promotion
     */
    public function removePromotion(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);
        $cart->update(['promotion_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Promotion removed.',
        ]);
    }

    /**
     * POST /api/cart/buy-now
     */
    public function buyNow(Request $request): JsonResponse
    {
        $request->merge(['buy_now' => true]);

        return $this->add($request);
    }

    /**
     * POST /api/cart/merge
     */
    public function merge(Request $request): JsonResponse
    {
        if (! $request->user()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $deviceId = $request->header('X-Device-Id');
        if (! $deviceId) {
            $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
            $cartData = $this->transformCart($cart->load(['items.product.brand', 'items.product.category']));

            return response()->json([
                'success' => true,
                'message' => 'Nothing to merge.',
                'cart' => $cartData,
                'data' => $cartData,
            ]);
        }

        $guestCart = Cart::where('session_id', $deviceId)->first();
        if (! $guestCart || $guestCart->items->isEmpty()) {
            $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
            $cartData = $this->transformCart($cart->load(['items.product.brand', 'items.product.category']));

            return response()->json([
                'success' => true,
                'message' => 'Nothing to merge.',
                'cart' => $cartData,
                'data' => $cartData,
            ]);
        }

        $userCart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()
                ->where('product_id', $guestItem->product_id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existing) {
                $existing->update(['quantity' => $existing->quantity + $guestItem->quantity]);
            } else {
                $userCart->items()->create($guestItem->only([
                    'product_id', 'product_variant_id', 'color_id',
                    'phone_storage_id', 'phone_ram_id', 'quantity', 'unit_price',
                ]));
            }
        }

        $guestCart->items()->delete();
        $guestCart->delete();

        $cartData = $this->transformCart($userCart->fresh(['items.product.brand', 'items.product.category']));

        return response()->json([
            'success' => true,
            'message' => 'Guest cart merged.',
            'cart' => $cartData,
            'data' => $cartData,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    protected function cartError(string $message, bool $needsSelection = false, ?Product $product = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'needs_selection' => $needsSelection,
            'product_slug' => $product?->slug,
        ], 422);
    }

    private function authorizeCartItem(Request $request, CartItem $item): void
    {
        $cart = $this->resolveCart($request);

        abort_if($item->cart_id !== $cart->id, 403, 'This item does not belong to your cart.');
    }

    private function transformCart(Cart $cart): array
    {
        $items = $cart->items->map(fn ($item) => $this->transformCartItem($item))->values();

        $subtotal = $items->sum(fn ($i) => $i['unit_price'] * $i['quantity']);
        $discount = 0;

        if ($cart->promotion) {
            $discount = $cart->promotion->type === 'percentage'
                ? $subtotal * ($cart->promotion->value / 100)
                : min($cart->promotion->value, $subtotal);
        }

        return [
            'id' => $cart->id,
            'items' => $items,
            'item_count' => $items->sum('quantity'),
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'total' => round($subtotal - $discount, 2),
            'promotion' => $cart->promotion ? [
                'code' => $cart->promotion->code,
                'name' => $cart->promotion->name,
                'type' => $cart->promotion->type,
                'value' => (float) $cart->promotion->value,
            ] : null,
        ];
    }

    private function transformCartItem(CartItem $item): array
    {
        $product = $item->relationLoaded('product') ? $item->product : $item->product()->with(['brand', 'category'])->first();

        return [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'product_variant_id' => $item->product_variant_id,
            'quantity' => (int) $item->quantity,
            'price' => (float) $item->unit_price,
            'unit_price' => (float) $item->unit_price,
            'line_total' => round($item->unit_price * $item->quantity, 2),
            'subtotal' => round($item->unit_price * $item->quantity, 2),
            'product' => $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'base_price' => (float) $product->base_price,
                'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
                'price' => (float) ($product->sale_price ?? $product->base_price),
                'thumbnail' => $product->thumbnail,
                'image' => $product->image_url,
                'image_url' => $product->image_url,
                'main_image_url' => $product->main_image_url,
                'brand' => $product->brand ? [
                    'id' => $product->brand->id,
                    'name' => $product->brand->name,
                    'slug' => $product->brand->slug,
                ] : null,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
            ] : null,
            'color' => $item->relationLoaded('color') ? $item->color?->only(['id', 'name', 'hex_code']) : null,
            'storage' => $item->relationLoaded('storage') ? $item->storage?->only(['id', 'capacity', 'label']) : null,
            'ram' => $item->relationLoaded('ram') ? $item->ram?->only(['id', 'capacity', 'name']) : null,
        ];
    }

    private function transformProductSummary(Product $product): array
    {
        $stockQty = $product->total_stock;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'brand' => $product->brand?->name,
            'price' => (float) ($product->sale_price ?? $product->base_price),
            'base_price' => (float) $product->base_price,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'thumbnail' => $product->thumbnail,
            'image' => $product->image_url,
            'image_url' => $product->image_url,
            'main_image_url' => $product->main_image_url,
            'stock' => $stockQty,
            'in_stock' => $stockQty > 0,
            'total_stock' => $stockQty,
            'stock_quantity' => $stockQty,
            'avg_rating' => round((float) ($product->avg_rating ?? 0), 1),
            'reviews_count' => $product->reviews_count ?? 0,
            'total_sold' => (int) ($product->total_sold ?? 0),
        ];
    }
}
