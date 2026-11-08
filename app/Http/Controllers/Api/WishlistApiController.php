<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = Wishlist::with([
            'product.category',
            'product.brand',
            'product.colors',
            'product.storages',
            'product.variants.color',
            'product.variants.storage',
            'product.stock',
            'color',
            'phoneStorage',
        ])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->filter(fn (Wishlist $w) => $w->product !== null)
            ->map(fn (Wishlist $w) => [
                'id' => $w->id,
                'user_id' => $w->user_id,
                'product_id' => $w->product_id,
                'color_id' => $w->color_id,
                'phone_storage_id' => $w->phone_storage_id,
                'product' => $w->product,
                'color' => $w->color,
                'storage' => $w->phoneStorage,
                'created_at' => $w->created_at?->toIso8601String(),
            ])
            ->values();

        return response()->json([
            'success' => true,
            'wishlist' => $items,
            'data' => $items,
        ]);
    }

    public function toggleBody(Request $request): JsonResponse
    {
        $productId = $request->input('product_id');
        if (! $productId) {
            return response()->json([
                'success' => false,
                'message' => 'product_id is required',
            ], 422);
        }

        return $this->toggle($request, $productId);
    }

    public function toggle(Request $request, $productId): JsonResponse
    {
        if ($productId === 'toggle') {
            $productId = $request->input('product_id');
        }

        $product = Product::where('id', $productId)
            ->orWhere('slug', $productId)
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $colorId = $request->input('color_id');
        $storageId = $request->input('phone_storage_id');

        $existing = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
            $message = 'Removed from wishlist';
        } else {
            Wishlist::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'color_id' => $colorId,
                'phone_storage_id' => $storageId,
            ]);
            $wishlisted = true;
            $message = 'Added to wishlist';
        }

        return response()->json([
            'success' => true,
            'wishlisted' => $wishlisted,
            'in_wishlist' => $wishlisted,
            'message' => $message,
            'data' => [
                'product_id' => $product->id,
                'wishlisted' => $wishlisted,
            ],
        ]);
    }

    public function remove(Request $request, $productId): JsonResponse
    {
        Wishlist::where('user_id', $request->user()->id)
            ->where(function ($q) use ($productId) {
                $q->where('product_id', $productId)->orWhere('id', $productId);
            })
            ->delete();

        return response()->json([
            'success' => true,
            'wishlisted' => false,
            'message' => 'Removed from wishlist',
        ]);
    }
}
