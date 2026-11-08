<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
            'brand',
            'phoneModel',
            'category',
            'colors',
            'storages',
            'rams',
            'stock',
        ])
        ->withCount([
            'reviews' => fn ($q) => $q->where('is_approved', true)
        ])
        ->withAvg([
            'reviews as avg_rating' => fn ($q) =>
                $q->where('is_approved', true)
        ], 'rating')
        ->where('is_active', true)
        ->where('status', 'active');

        // Search
        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Brand filter
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Featured
        if ($request->boolean('is_featured')) {
            $query->where('is_featured', true);
        }

        // New products
        if ($request->boolean('is_new')) {
            $query->where('is_new', true);
        }

        // Sorting
        match ($request->get('sort', 'default')) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'newest' => $query->latest(),
            'best_seller' => $query->orderByDesc('total_sold'),
            default => $query->orderBy('sort_order'),
        };

        $products = $query->paginate(16);

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ]);
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
            'stock',
            'gifts',
        ])
        ->withCount([
            'reviews' => fn ($q) => $q->where('is_approved', true)
        ])
        ->withAvg([
            'reviews as avg_rating' => fn ($q) =>
                $q->where('is_approved', true)
        ], 'rating')
        ->where('slug', $slug)
        ->where('is_active', true)
        ->where('status', 'active')
        ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully',
            'data' => $product,
        ]);
    }
}