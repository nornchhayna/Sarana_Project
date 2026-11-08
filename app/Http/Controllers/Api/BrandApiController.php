<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandApiController extends Controller
{
    public function index(): JsonResponse
    {
        $brands = Brand::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get()
            ->map(fn (Brand $brand) => $this->transformBrand($brand))
            ->values();

        return response()->json([
            'success' => true,
            'brands' => $brands,
            'data' => $brands,
        ]);
    }

    public function show(string $idOrSlug, Request $request): JsonResponse
    {
        $brand = Brand::where('is_active', true)
            ->where(function ($q) use ($idOrSlug) {
                $q->where('slug', $idOrSlug);
                if (is_numeric($idOrSlug)) {
                    $q->orWhere('id', (int) $idOrSlug);
                }
            })
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->first();

        if (! $brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found',
            ], 404);
        }

        $perPage = min((int) $request->input('per_page', 15), 100);
        $products = Product::with(['category:id,name,slug', 'brand:id,name,slug', 'phoneModel:id,name', 'variants', 'stock'])
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->where('brand_id', $brand->id)
            ->where('is_active', true)
            ->latest()
            ->paginate($perPage);

        $brandData = $this->transformBrand($brand);

        return response()->json([
            'success' => true,
            'brand' => $brandData,
            'products' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
            ],
            'data' => [
                'brand' => $brandData,
                'products' => $products->items(),
            ],
        ]);
    }

    private function transformBrand(Brand $brand): array
    {
        $logoUrl = null;
        if (! empty($brand->logo)) {
            $logoUrl = str_starts_with($brand->logo, 'http')
                ? $brand->logo
                : url('storage/' . ltrim($brand->logo, '/'));
        }

        return [
            'id' => $brand->id,
            'name' => $brand->name,
            'slug' => $brand->slug,
            'description' => $brand->description ?? null,
            'website' => $brand->website ?? null,
            'logo' => $brand->logo,
            'logo_url' => $logoUrl,
            'image_url' => $logoUrl,
            'products_count' => $brand->products_count ?? 0,
        ];
    }
}
