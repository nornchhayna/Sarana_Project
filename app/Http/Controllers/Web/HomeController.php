<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Slider;
use App\Models\Blog;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->get();

        $brands = Brand::where('is_active', true)->get();

        // Flash Sale — products with sale price and is_flash_sale flag
        $flashSaleProducts = Product::with(['brand', 'phoneModel', 'stock'])
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->where('is_active', true)
            ->where('is_flash_sale', true)
            ->whereNotNull('sale_price')
            ->where('sale_price', '>', 0)
            ->where('sale_start_date', '<=', now())
            ->where('sale_end_date', '>=', now())
            ->orderByDesc('sale_price')
            ->take(8)
            ->get();

        // Featured Products — only products flagged as featured
        $featuredProducts = Product::with(['brand', 'phoneModel', 'stock'])
        ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
    ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating') 
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->take(8)
            ->get();

// Accessories — parent category + all its subcategories (e.g. Charging Cables)
$accessoriesCategory = Category::where('slug', 'accessories')
    ->where('is_active', true)
    ->first();

$accessoryProducts = collect();

if ($accessoriesCategory) {
    // Get all category IDs including subcategories
    $categoryIds = Category::where('id', $accessoriesCategory->id)
        ->orWhere('parent_id', $accessoriesCategory->id)
        ->pluck('id');

    $accessoryProducts = Product::with(['brand', 'phoneModel', 'stock'])
        ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
        ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
        ->where('is_active', true)
        ->whereIn('category_id', $categoryIds)
        ->latest()
        ->take(8)
        ->get();
}


        // New Arrivals — only products flagged as new
        $newProducts = Product::with(['brand', 'phoneModel', 'stock'])
        ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
    ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating') 
            ->where('is_active', true)
            ->where('is_new', true)
            ->latest()
            ->take(8)
            ->get();

        // Pre-Orders — only products flagged as pre-order
        $preOrderProducts = Product::with(['brand', 'phoneModel', 'stock'])
            ->where('is_active', true)
            ->where('is_pre_order', true)
            ->latest()
            ->take(8)
            ->get();

        // Best sellers — unaffected, still orders by total_sold
        $bestSellers = Product::with(['brand', 'phoneModel', 'stock'])
            ->where('is_active', true)
            ->orderByDesc('total_sold')
            ->take(8)
            ->get();

        $latestReviews = Review::with(['user', 'product'])
            ->where('is_approved', true)
            ->latest()
            ->take(4)
            ->get();

        $reviewCount = Review::where('is_approved', true)->count();
        $averageRating = Review::where('is_approved', true)->avg('rating') ?? 0;

        // Blog posts
        $blogPosts = Blog::where('is_published', true)
            ->latest()
            ->take(4)
            ->get();

        return view('web.home.index', compact(
            'sliders',
            'categories',
            'brands',
            'flashSaleProducts',
            'featuredProducts',
            'newProducts',
            'preOrderProducts',
            'bestSellers',
            'accessoryProducts',
            'accessoriesCategory',
            'latestReviews',
            'reviewCount',
            'averageRating',
            'blogPosts'
        ));
    }
}