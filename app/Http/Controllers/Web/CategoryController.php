<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * clear chache ១​ នាទីម្ដង
     */
    protected const CACHE_DURATION = 60;

    /**
     * ទាញយកdata categoriesយក​មកទាំងអស់
     */
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        return view('web.categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Display single category with products
     */
    public function show($slug, Request $request)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get sort and filter parameters
        $sort = $request->input('sort', 'latest');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $brand = $request->input('brand');

        // Build query for products
        $query = Product::where('is_active', true)
            ->where('category_id', $category->id)
            ->with(['brand', 'category', 'stock']);

        // Apply filter
        if ($priceMin) {
            $query->where('price', '>=', $priceMin);
        }

        if ($priceMax) {
            $query->where('price', '<=', $priceMax);
        }

        if ($brand) {
            $query->where('brand_id', $brand);
        }

        // Apply sorting
        $query = $this->applySorting($query, $sort);

        // Paginate results
        $products = $query->paginate(12);

        // Get related data
        // In CategoryController.php — replace line 75 with:
        $subcategories = $category->children()->where('is_active', true)->get();
        $brands = Brand::whereHas('products', fn ($q) => $q->where('category_id', $category->id)
            ->where('is_active', true)
        )
            ->where('is_active', true)
            ->get();

        $priceRange = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->selectRaw('MIN(base_price) as min_price, MAX(base_price) as max_price')
            ->first();

        return view('web.categories.show', [
            'category' => $category,
            'products' => $products,
            'subcategories' => $subcategories,
            'brands' => $brands,
            'priceRange' => $priceRange,
            'sort' => $sort,
            'selectedBrand' => $brand,
            'selectedPriceMin' => $priceMin,
            'selectedPriceMax' => $priceMax,
            'breadcrumb' => $category->breadcrumb_path,
            'allCategories' => Category::where('is_active', true)
                ->whereNull('parent_id')  // top-level only
                ->with('children')        // eager load for the sidebar
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    /**
     * Display subcategory
     */
    public function showSubcategory($parentSlug, $childSlug, Request $request)
    {
        // Get parent category
        $parent = Category::where('slug', $parentSlug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get child category
        $category = Category::where('slug', $childSlug)
            ->where('parent_id', $parent->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Get sort and filter parameters
        $sort = $request->input('sort', 'latest');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $brand = $request->input('brand');

        // Build query for products
        $query = Product::where('is_active', true)
            ->where('category_id', $category->id)
            ->with(['brand', 'stock', 'category']);

        // Apply filters
        if ($priceMin) {
            $query->where('price', '>=', $priceMin);
        }

        if ($priceMax) {
            $query->where('price', '<=', $priceMax);
        }

        if ($brand) {
            $query->where('brand_id', $brand);
        }

        // Apply sorting
        $query = $this->applySorting($query, $sort);

        // Paginate results
        $products = $query->paginate(12);

        // Get related data
        $subcategories = $category->activeChildren()->get();
        $brands = Brand::whereHas('products', fn ($q) => $q->where('category_id', $category->id)
            ->where('is_active', true)
        )
            ->where('is_active', true)
            ->get();

        $priceRange = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('web.categories.show', [
            'category' => $category,
            'parent' => $parent,
            'products' => $products,
            'subcategories' => $subcategories,
            'brands' => $brands,
            'priceRange' => $priceRange,
            'sort' => $sort,
            'selectedBrand' => $brand,
            'selectedPriceMin' => $priceMin,
            'selectedPriceMax' => $priceMax,
            'breadcrumb' => $category->breadcrumb_path,
        ]);
    }

    /**
     * Apply sorting to product query
     */
    protected function applySorting($query, $sort)
    {
        return match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderByDesc('price'),
            'popular' => $query->orderByDesc('total_sold'),
            'rating' => $query->orderByDesc('rating'),
            'oldest' => $query->oldest(),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->latest(), // 'latest' is default
        };
    }

    /**
     * Get category sidebar data (AJAX)
     */
    public function getSidebar($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $priceRange = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $brands = Brand::whereHas('products', fn ($q) => $q->where('category_id', $category->id)
            ->where('is_active', true)
        )
            ->where('is_active', true)
            ->select('id', 'name', 'slug')
            ->get();

        return response()->json([
            'brands' => $brands,
            'priceRange' => $priceRange,
        ]);
    }

    /**
     * Filter products (AJAX)
     */
    public function filter(Request $request)
    {
        $categorySlug = $request->input('category');
        $sort = $request->input('sort', 'latest');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $brands = $request->input('brands', []);
        $page = $request->input('page', 1);

        $category = Category::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = Product::where('is_active', true)
            ->where('category_id', $category->id)
            ->with(['brand', 'stock', 'category']);

        // Apply filters
        if ($priceMin) {
            $query->where('price', '>=', $priceMin);
        }

        if ($priceMax) {
            $query->where('price', '<=', $priceMax);
        }

        if (! empty($brands)) {
            $query->whereIn('brand_id', $brands);
        }

        // Apply sorting
        $query = $this->applySorting($query, $sort);

        $products = $query->paginate(12, ['*'], 'page', $page);

        return response()->json([
            'products' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
            ],
            'links' => [
                'next' => $products->nextPageUrl(),
                'prev' => $products->previousPageUrl(),
            ],
        ]);
    }

    /**
     * Get breadcrumb for category
     */
    public function getBreadcrumb($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json($category->breadcrumb_path);
    }

    /**
     * Popular categories (for sidebar/home)
     */
    public function getPopular()
    {
        $categories = Cache::remember(
            'categories.popular',
            self::CACHE_DURATION,
            function () {
                return Category::where('is_active', true)
                    ->whereNull('parent_id')
                    ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                    ->orderByDesc('products_count')
                    ->take(10)
                    ->get();
            }
        );

        return response()->json($categories);
    }

    /**
     * Clear category cache
     */
    public function clearCache()
    {
        Cache::forget('categories.popular');

        return back()->with('success', 'Category cache cleared');
    }
}
