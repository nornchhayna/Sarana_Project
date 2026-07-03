<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;

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

        // FIX: Removed ->where('is_featured', true) so any active product will show up
        $featuredProducts = Product::with(['brand', 'phoneModel', 'stock'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        // FIX: Removed ->where('is_new', true) so any active product will show up
        $newProducts = Product::with(['brand', 'phoneModel', 'stock'])
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        // Best sellers will still work, but since total_sold defaults to 0, it will just grab the latest active products for now.
        $bestSellers = Product::with(['brand', 'phoneModel', 'stock'])
            ->where('is_active', true)
            ->orderByDesc('total_sold')
            ->take(8)
            ->get();

        return view('web.home.index', compact(
            'sliders',
            'categories',
            'brands',
            'featuredProducts',
            'newProducts',
            'bestSellers'
        ));
    }
}
