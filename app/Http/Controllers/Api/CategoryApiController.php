<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryApiController extends Controller
{
    protected const CACHE_DURATION = 60;

    /**
     * GET /api/v1/categories
     * Top-level categories with active product counts.
     */
    public function index(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($c) => $this->transformCategorySummary($c))
            ->values();

        return response()->json([
            'success' => true,
            'categories' => $categories,
            'data' => $categories,
        ]);
    }

    /**
     * GET /api/v1/categories/{idOrSlug}
     * Single category + paginated/filtered products.
     */
    public function show(string $idOrSlug, Request $request): JsonResponse
    {
        $category = Category::where('is_active', true)
            ->where(function ($q) use ($idOrSlug) {
                $q->where('slug', $idOrSlug);
                if (is_numeric($idOrSlug)) {
                    $q->orWhere('id', (int) $idOrSlug);
                }
            })
            ->firstOrFail();

        [$products, $meta] = $this->queryProducts($request, $category->id);

        $subcategories = $category->children()->where('is_active', true)->get();
        $brands = $this->brandsForCategory($category->id);
        $priceRange = $this->priceRangeForCategory($category->id);
        $categoryData = $this->transformCategory($category);

        return response()->json([
            'success' => true,
            'category' => $categoryData,
            'subcategories' => $subcategories->map(fn ($c) => $this->transformCategorySummary($c)),
            'brands' => $brands,
            'price_range' => [
                'min' => (float) ($priceRange->min_price ?? 0),
                'max' => (float) ($priceRange->max_price ?? 0),
            ],
            'products' => $products->items(),
            'pagination' => $this->paginationMeta($products),
            'filters' => $meta,
            'breadcrumb' => [['name' => $category->name, 'slug' => $category->slug]],
            'all_categories' => Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with('children')
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($c) => $this->transformCategorySummary($c)),
            'data' => [
                'category' => $categoryData,
                'products' => $products->items(),
            ],
        ]);
    }

    /**
     * GET /api/categories/{parentSlug}/{childSlug}
     */
    public function showSubcategory(string $parentSlug, string $childSlug, Request $request): JsonResponse
    {
        $parent = Category::where('slug', $parentSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $category = Category::where('slug', $childSlug)
            ->where('parent_id', $parent->id)
            ->where('is_active', true)
            ->firstOrFail();

        [$products, $meta] = $this->queryProducts($request, $category->id);

        $subcategories = $category->children()->where('is_active', true)->get();
        $brands = $this->brandsForCategory($category->id);
        $priceRange = $this->priceRangeForCategory($category->id);

        return response()->json([
            'success' => true,
            'category' => $this->transformCategory($category),
            'parent' => $this->transformCategorySummary($parent),
            'subcategories' => $subcategories->map(fn ($c) => $this->transformCategorySummary($c)),
            'brands' => $brands,
            'price_range' => [
                'min' => (float) ($priceRange->min_price ?? 0),
                'max' => (float) ($priceRange->max_price ?? 0),
            ],
            'products' => $products->items(),
            'pagination' => $this->paginationMeta($products),
            'filters' => $meta,
            'breadcrumb' => [
                ['name' => $parent->name, 'slug' => $parent->slug],
                ['name' => $category->name, 'slug' => $category->slug],
            ],
        ]);
    }

    /**
     * GET /api/categories/{slug}/sidebar
     */
    public function getSidebar(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'brands' => $this->brandsForCategory($category->id),
            'price_range' => $this->priceRangeForCategory($category->id),
        ]);
    }

    /**
     * GET /api/categories/filter
     */
    public function filter(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'required|string',
        ]);

        $category = Category::where('slug', $request->input('category'))
            ->where('is_active', true)
            ->firstOrFail();

        [$products, $meta] = $this->queryProducts($request, $category->id, $request->input('brands', []));

        return response()->json([
            'success' => true,
            'products' => $products->items(),
            'pagination' => $this->paginationMeta($products),
            'filters' => $meta,
        ]);
    }

    /**
     * GET /api/categories/{slug}/breadcrumb
     */
    public function getBreadcrumb(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'breadcrumb' => [['name' => $category->name, 'slug' => $category->slug]],
        ]);
    }

    /**
     * GET /api/categories/popular
     */
    public function getPopular(): JsonResponse
    {
        $categories = Cache::remember(
            'categories.popular.v2',
            self::CACHE_DURATION,
            fn () => Category::where('is_active', true)
                ->whereNull('parent_id')
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->orderByDesc('products_count')
                ->take(10)
                ->get()
                ->map(fn ($c) => $this->transformCategorySummary($c))
                ->toArray()
        );

        return response()->json([
            'success' => true,
            'categories' => $categories,
            'data' => $categories,
        ]);
    }

    /**
     * POST /api/categories/clear-cache
     */
    public function clearCache(): JsonResponse
    {
        Cache::forget('categories.popular.v2');

        return response()->json([
            'success' => true,
            'message' => 'Category cache cleared.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Shared query builder used by show(), showSubcategory(), filter()
    // ─────────────────────────────────────────────────────────────

    /**
     * @return array{0: \Illuminate\Pagination\LengthAwarePaginator, 1: array}
     */
    private function queryProducts(Request $request, int $categoryId, array $brandIds = []): array
    {
        $sort = $request->input('sort', 'latest');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $brand = $request->input('brand');
        $page = $request->input('page', 1);

        $query = Product::where('is_active', true)
            ->where('category_id', $categoryId)
            ->with(['brand', 'category', 'variants', 'stock']);

        if ($priceMin) {
            $query->where('base_price', '>=', $priceMin);
        }

        if ($priceMax) {
            $query->where('base_price', '<=', $priceMax);
        }

        if ($brand) {
            $query->where('brand_id', $brand);
        }

        if (! empty($brandIds)) {
            $query->whereIn('brand_id', $brandIds);
        }

        $query = $this->applySorting($query, $sort);

        $products = $query->paginate(12, ['*'], 'page', $page);

        $products->getCollection()->transform(fn ($p) => $this->transformProductSummary($p));

        $meta = [
            'sort' => $sort,
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'brand' => $brand,
        ];

        return [$products, $meta];
    }

    protected function applySorting($query, $sort)
    {
        return match ($sort) {
            'price_low' => $query->orderBy('base_price', 'asc'),
            'price_high' => $query->orderByDesc('base_price'),
            'popular' => $query->orderByDesc('total_sold'),
            'rating' => $query->orderByDesc('avg_rating'),
            'oldest' => $query->oldest(),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->latest(),
        };
    }

    private function brandsForCategory(int $categoryId)
    {
        return Brand::whereHas('products', fn ($q) => $q->where('category_id', $categoryId)
            ->where('is_active', true)
        )
            ->where('is_active', true)
            ->get(['id', 'name', 'slug'])
            ->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'slug' => $b->slug,
            ]);
    }

    private function priceRangeForCategory(int $categoryId)
    {
        return Product::where('category_id', $categoryId)
            ->where('is_active', true)
            ->selectRaw('MIN(base_price) as min_price, MAX(base_price) as max_price')
            ->first();
    }

    private function paginationMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Transformers
    // ─────────────────────────────────────────────────────────────

    private function transformCategory(Category $category): array
    {
        $imageUrl = null;
        if (! empty($category->image)) {
            $imageUrl = str_starts_with($category->image, 'http')
                ? $category->image
                : url('storage/' . ltrim($category->image, '/'));
        }

        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description ?? null,
            'image' => $category->image,
            'image_url' => $imageUrl,
            'parent_id' => $category->parent_id,
        ];
    }

    private function transformCategorySummary(Category $category): array
    {
        $imageUrl = null;
        if (! empty($category->image)) {
            $imageUrl = str_starts_with($category->image, 'http')
                ? $category->image
                : url('storage/' . ltrim($category->image, '/'));
        }

        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'image' => $category->image,
            'image_url' => $imageUrl,
            'products_count' => $category->products_count ?? 0,
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
            'category' => $product->category?->name,
            'price' => (float) ($product->sale_price ?? $product->base_price),
            'base_price' => (float) $product->base_price,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'thumbnail' => $product->thumbnail,
            'image' => $product->image_url,
            'image_url' => $product->image_url,
            'main_image_url' => $product->main_image_url,
            'stock' => $stockQty,
            'in_stock' => $stockQty > 0,
            'stock_quantity' => $stockQty,
            'total_stock' => $stockQty,
            'rating' => round((float) ($product->avg_rating ?? 0), 1),
            'avg_rating' => round((float) ($product->avg_rating ?? 0), 1),
            'total_sold' => $product->total_sold ?? 0,
        ];
    }
}
