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
        $query = Product::with(['brand', 'phoneModel', 'category', 'colors', 'storages', 'stock'])
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
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
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
            'best_seller' => $query->orderByDesc('total_sold'),
            'rating' => $query->orderByDesc('avg_rating'),
            default => $query->orderBy('sort_order')->orderByDesc('is_featured'),
        };

        $products = $query->paginate(16)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $colors = Color::where('is_active', true)->get();
        $storages = PhoneStorage::where('is_active', true)->get();

        // 1. ADD THIS: Fetch the featured products for the "Deals of the Day" section
        $featuredProducts = Product::with(['brand', 'stock']) // Eager load to prevent N+1 issues in the view
            ->where('is_active', true)
            ->where('status', 'active')
            ->where('is_featured', true)
            ->inRandomOrder() // Optional: mixes up the deals shown
            ->take(4)         // Matches the 4-column layout in your Blade file
            ->get();

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
            'stock',
            'reviews' => fn ($q) => $q->where('is_approved', true)->with('user')->latest()->take(10),
        ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Related products — same category, exclude current
        $related = Product::with(['brand', 'stock'])
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

        return view('web.products.show', compact('product', 'related', 'wishlisted'));
    }
}
