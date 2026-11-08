<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\PhoneStorage;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with(['brand', 'phoneModel', 'category', 'colors', 'storages', 'stock'])
                ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
                ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
                ->withSoldCount()
                ->where('is_active', true)
                ->where('status', 'active');

            // Search
            if ($request->filled('q')) {
                $search = $request->q;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('phoneModel', fn ($m) => $m->where('name', 'like', "%{$search}%"));
                });
            }

            // Filters
            if ($request->filled('category')) {
                $category = Category::where('slug', $request->category)->first();
                if ($category) {
                    // Get all category IDs including subcategories
                    $categoryIds = Category::where('id', $category->id)
                        ->orWhere('parent_id', $category->id)
                        ->pluck('id');
                    $query->whereIn('category_id', $categoryIds);
                }
            }

            if ($request->filled('brand')) {
                $query->whereHas('brand', fn ($q) => $q->where('slug', $request->brand));
            }

            if ($request->filled('color')) {
                $query->whereHas('colors', fn ($q) => $q->where('colors.id', $request->color));
            }

            if ($request->filled('storage')) {
                $query->whereHas('storages', fn ($q) => $q->where('phone_storages.id', $request->storage));
            }

            if ($request->filled('min_price')) {
                $query->where('base_price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('base_price', '<=', $request->max_price);
            }

            if ($request->boolean('is_featured')) {
                $query->where('is_featured', true);
            }

            if ($request->boolean('is_new')) {
                $query->where('is_new', true);
            }

            if ($request->boolean('on_sale')) {
                $query->whereNotNull('sale_price');
            }

            // Sorting
            match ($request->get('sort', 'default')) {
                'price_asc' => $query->orderBy('base_price'),
                'price_desc' => $query->orderByDesc('base_price'),
                'newest' => $query->latest(),
                'best_seller' => $query->orderByDesc('actual_sold'),
                'rating' => $query->orderByDesc('avg_rating'),
                default => $query->orderBy('sort_order')->orderByDesc('is_featured'),
            };

            $products = $query->paginate(16)->withQueryString();
        } catch (\Exception $e) {
            // Fallback if product query fails
            $products = Product::where('is_active', true)->paginate(16);
        }

        // Fetch filter data with error handling
        try {
            $categories = Category::where('is_active', true)
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->get();
        } catch (\Exception $e) {
            $categories = collect();
        }

        try {
            $brands = Brand::where('is_active', true)->get();
        } catch (\Exception $e) {
            $brands = collect();
        }

        try {
            $colors = Color::where('is_active', true)->get();
        } catch (\Exception $e) {
            $colors = collect();
        }

        try {
            $storages = PhoneStorage::where('is_active', true)->get();
        } catch (\Exception $e) {
            $storages = collect();
        }

        // Fetch featured products with error handling
        try {
            $featuredProducts = Product::with(['brand', 'stock'])
                ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
                ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
                ->withSoldCount()
                ->where('is_active', true)
                ->where('status', 'active')
                ->where('is_featured', true)
                ->inRandomOrder()
                ->take(4)
                ->get();
        } catch (\Exception $e) {
            $featuredProducts = collect();
        }

        // 2. UPDATE THIS: Add 'featuredProducts' to the compact() array
        return view('web.products.index', compact(
            'products', 'categories', 'brands', 'colors', 'storages', 'featuredProducts'
        ));
    }



    public function show(string $slug)
    {
        
        $product = Product::with([
            'brand',
            'phoneModel',
            'category',
            'colors',
            'storages',
            'rams',
            'stock.color',
            'stock.phoneStorage',
            'variants.color',
            'variants.storage',
            'variants.ram',
            'variantImages',
            'gifts',
            'reviews' => fn ($q) => $q->where('is_approved', true)->with('user')->latest()->take(10),
        ])
        ->withSoldCount()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Related products — same category, exclude current
        $related = Product::with(['brand', 'stock'])
         ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
    ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
    ->withSoldCount()
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Check if authenticated user has wishlisted this product
        $wishlisted = false;
        if (auth()->check()) {
            $wishlisted = $product->wishlistItems()
                ->where('user_id', auth()->id())
                ->exists();
        }

        // Review aggregates — query separately so they're not limited to the 10 loaded above
        $reviewStats = $product->reviews()->where('is_approved', true);
        $averageRating = (clone $reviewStats)->avg('rating');
        $reviewCount = (clone $reviewStats)->count();
        $ratingBreakdown = (clone $reviewStats)
            ->selectRaw('rating, count(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        return view('web.products.show', compact(
            'product', 'related', 'wishlisted', 'averageRating', 'reviewCount', 'ratingBreakdown'
        ));
    }
}
