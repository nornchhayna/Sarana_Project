<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    /**
     * Get variant by product, color, and storage
     */
    public function show(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'nullable|exists:colors,id',
            'phone_storage_id' => 'nullable|exists:phone_storages,id',
        ]);

        $variant = ProductVariant::with(['color', 'phoneStorage'])
            ->where('product_id', $request->product_id)
            ->where('is_active', true);

        if ($request->filled('color_id')) {
            $variant->where('color_id', $request->color_id);
        }

        if ($request->filled('phone_storage_id')) {
            $variant->where('phone_storage_id', $request->phone_storage_id);
        }

        $variant = $variant->first();

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Variant not found'
            ], 404);
        }

        // Get images for this color
        $images = [];
        if ($variant->color_id) {
            $images = $variant->product->variantImages()
                ->where('color_id', $variant->color_id)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($img) => [
                    'id' => $img->id,
                    'image' => asset('storage/' . $img->image),
                    'is_primary' => $img->is_primary,
                ])
                ->toArray();
        }

        // Fallback to product images if no color-specific images
        if (empty($images)) {
            $images = $variant->product->variantImages()
                ->whereNull('color_id')
                ->orderBy('sort_order')
                ->get()
                ->map(fn($img) => [
                    'id' => $img->id,
                    'image' => asset('storage/' . $img->image),
                    'is_primary' => $img->is_primary,
                ])
                ->toArray();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $variant->id,
                'price' => $variant->price,
                'stock' => $variant->stock,
                'sku' => $variant->sku,
                'is_in_stock' => $variant->isInStock(),
                'color' => $variant->color ? [
                    'id' => $variant->color->id,
                    'name' => $variant->color->name,
                    'hex_code' => $variant->color->hex_code,
                ] : null,
                'storage' => $variant->phoneStorage ? [
                    'id' => $variant->phoneStorage->id,
                    'capacity' => $variant->phoneStorage->capacity,
                    'label' => $variant->phoneStorage->label,
                ] : null,
                'images' => $images,
            ]
        ]);
    }

    /**
     * Get all available variants for a product
     */
    public function index(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $variants = ProductVariant::with(['color', 'phoneStorage'])
            ->where('product_id', $request->product_id)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $variants->map(fn($variant) => [
                'id' => $variant->id,
                'price' => $variant->price,
                'stock' => $variant->stock,
                'sku' => $variant->sku,
                'is_in_stock' => $variant->isInStock(),
                'color' => $variant->color ? [
                    'id' => $variant->color->id,
                    'name' => $variant->color->name,
                    'hex_code' => $variant->color->hex_code,
                ] : null,
                'storage' => $variant->phoneStorage ? [
                    'id' => $variant->phoneStorage->id,
                    'capacity' => $variant->phoneStorage->capacity,
                    'label' => $variant->phoneStorage->label,
                ] : null,
            ])
        ]);
    }
}
