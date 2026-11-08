<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomeApiController extends Controller
{
    protected const CACHE_DURATION = 300; // 5 minutes — homepage data doesn't need to be second-fresh

    /**
     * GET /api/v1/home
     * Single aggregated payload for the Flutter home screen.
     */
    public function index(): JsonResponse
    {
        $sliders = Slider::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($s) => $this->transformSlider($s));

        $categories = Cache::remember('home.categories.v1', self::CACHE_DURATION, function () {
            return Category::where('is_active', true)
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'image' => $c->image,
                    'image_url' => ! empty($c->image)
                        ? (str_starts_with($c->image, 'http') ? $c->image : url('storage/' . ltrim($c->image, '/')))
                        : null,
                    'products_count' => $c->products_count,
                ])
                ->toArray();
        });

        $brands = Cache::remember('home.brands.v1', self::CACHE_DURATION, function () {
            return Brand::where('is_active', true)
                ->get()
                ->map(fn ($b) => [
                    'id' => $b->id,
                    'name' => $b->name,
                    'slug' => $b->slug,
                    'logo' => $b->logo ?? null,
                    'logo_url' => ! empty($b->logo)
                        ? (str_starts_with($b->logo, 'http') ? $b->logo : url('storage/' . ltrim($b->logo, '/')))
                        : null,
                ])
                ->toArray();
        });

        $flashSaleProducts = Product::with(['brand', 'phoneModel', 'variants', 'stock'])
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

        $featuredProducts = Product::with(['brand', 'phoneModel', 'variants', 'stock'])
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        // Accessories — parent category + all its subcategories
        $accessoriesCategory = Category::where('slug', 'accessories')
            ->where('is_active', true)
            ->first();

        $accessoryProducts = collect();

        if ($accessoriesCategory) {
            $categoryIds = Category::where('id', $accessoriesCategory->id)
                ->orWhere('parent_id', $accessoriesCategory->id)
                ->pluck('id');

            $accessoryProducts = Product::with(['brand', 'phoneModel', 'variants', 'stock'])
                ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
                ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
                ->where('is_active', true)
                ->whereIn('category_id', $categoryIds)
                ->latest()
                ->take(8)
                ->get();
        }

        $newProducts = Product::with(['brand', 'phoneModel', 'variants', 'stock'])
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->where('is_active', true)
            ->where('is_new', true)
            ->latest()
            ->take(8)
            ->get();

        $preOrderProducts = Product::with(['brand', 'phoneModel', 'variants', 'stock'])
            ->where('is_active', true)
            ->where('is_pre_order', true)
            ->latest()
            ->take(8)
            ->get();

        $bestSellers = Product::with(['brand', 'phoneModel', 'variants', 'stock'])
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

        $blogPosts = Blog::where('is_published', true)
            ->latest()
            ->take(4)
            ->get();

        $payload = [
            'sliders' => $sliders,
            'categories' => $categories,
            'brands' => $brands,
            'flash_sale_products' => $flashSaleProducts->map(fn ($p) => $this->transformProduct($p)),
            'featured_products' => $featuredProducts->map(fn ($p) => $this->transformProduct($p)),
            'accessory_products' => $accessoryProducts->map(fn ($p) => $this->transformProduct($p)),
            'accessories_category' => $accessoriesCategory ? [
                'id' => $accessoriesCategory->id,
                'name' => $accessoriesCategory->name,
                'slug' => $accessoriesCategory->slug,
            ] : null,
            'new_products' => $newProducts->map(fn ($p) => $this->transformProduct($p)),
            'pre_order_products' => $preOrderProducts->map(fn ($p) => $this->transformProduct($p, includeReviews: false)),
            'best_sellers' => $bestSellers->map(fn ($p) => $this->transformProduct($p, includeReviews: false)),
            'latest_reviews' => $latestReviews->map(fn ($r) => $this->transformReview($r)),
            'review_summary' => [
                'count' => $reviewCount,
                'average_rating' => round((float) $averageRating, 1),
            ],
            'blog_posts' => $blogPosts->map(fn ($b) => $this->transformBlogPost($b)),
        ];

        return response()->json(array_merge(['success' => true, 'data' => $payload], $payload));
    }

    /**
     * GET /api/v1/blogs
     */
    public function blogs(): JsonResponse
    {
        $blogs = Blog::where('is_published', true)
            ->with('author:id,first_name,last_name,name')
            ->latest()
            ->get()
            ->map(fn (Blog $b) => $this->transformBlogPost($b, true));

        return response()->json([
            'success' => true,
            'blogs' => $blogs,
            'data' => $blogs,
        ]);
    }

    /**
     * GET /api/v1/blogs/{slug}
     */
    public function blogDetail(string $slugOrId): JsonResponse
    {
        $blog = Blog::where('slug', $slugOrId)
            ->when(is_numeric($slugOrId), fn ($q) => $q->orWhere('id', (int) $slugOrId))
            ->with('author:id,first_name,last_name,name')
            ->first();

        if (! $blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found',
            ], 404);
        }

        $data = $this->transformBlogPost($blog, true);

        return response()->json([
            'success' => true,
            'blog' => $data,
            'data' => $data,
        ]);
    }

    /**
     * POST /api/v1/newsletter/subscribe
     */
    public function subscribeNewsletter(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing to our newsletter!',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Transformers
    // ─────────────────────────────────────────────────────────────

    private function transformSlider(Slider $slider): array
    {
        $imageUrl = null;
        if (! empty($slider->image)) {
            $imageUrl = str_starts_with($slider->image, 'http')
                ? $slider->image
                : url('storage/' . ltrim($slider->image, '/'));
        }

        return [
            'id' => $slider->id,
            'title' => $slider->title,
            'subtitle' => $slider->subtitle ?? null,
            'badge_text' => $slider->badge_text ?? null,
            'button_text' => $slider->button_text ?? null,
            'button_url' => $slider->button_url ?? null,
            'image' => $slider->image,
            'image_url' => $imageUrl,
        ];
    }

    private function transformProduct(Product $product, bool $includeReviews = true): array
    {
        $stockQty = (int) ($product->variants->sum('stock') ?: $product->stock->sum('quantity'));

        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'brand' => $product->brand?->name,
            'phone_model' => $product->phoneModel?->name ?? null,
            'price' => (float) ($product->sale_price ?? $product->base_price),
            'base_price' => (float) $product->base_price,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'is_on_sale' => (bool) $product->sale_price,
            'thumbnail' => $product->thumbnail,
            'image' => $product->image_url,
            'image_url' => $product->image_url,
            'main_image_url' => $product->main_image_url,
            'stock' => $stockQty,
            'in_stock' => $stockQty > 0,
            'stock_quantity' => $stockQty,
            'total_stock' => $stockQty,
            'is_flash_sale' => (bool) $product->is_flash_sale,
            'is_featured' => (bool) $product->is_featured,
            'is_new' => (bool) $product->is_new,
            'is_pre_order' => (bool) $product->is_pre_order,
            'total_sold' => $product->total_sold ?? 0,
        ];

        if ($includeReviews) {
            $data['reviews_count'] = $product->reviews_count ?? 0;
            $data['avg_rating'] = round((float) ($product->avg_rating ?? 0), 1);
        }

        return $data;
    }

    private function transformReview(Review $review): array
    {
        $images = collect($review->images ?? [])->map(function ($img) {
            if (empty($img)) {
                return null;
            }
            return str_starts_with($img, 'http') ? $img : url('storage/' . ltrim($img, '/'));
        })->filter()->values()->toArray();

        return [
            'id' => $review->id,
            'rating' => $review->rating,
            'title' => $review->title ?? null,
            'body' => $review->body ?? $review->comment ?? null,
            'comment' => $review->comment ?? $review->body ?? null,
            'images' => $images,
            'user' => [
                'id' => $review->user?->id,
                'name' => $review->user->name ?? 'Anonymous',
                'avatar_url' => $review->user?->avatar_url,
                'initial' => strtoupper(substr($review->user->name ?? 'A', 0, 1)),
            ],
            'product' => $review->product ? [
                'id' => $review->product->id,
                'name' => $review->product->name,
                'slug' => $review->product->slug,
            ] : null,
            'created_at' => $review->created_at?->toIso8601String(),
            'created_at_human' => $review->created_at?->diffForHumans(),
        ];
    }

    private function transformBlogPost(Blog $blog, bool $includeContent = false): array
    {
        $imageUrl = null;
        if (! empty($blog->image)) {
            $imageUrl = str_starts_with($blog->image, 'http')
                ? $blog->image
                : url('storage/' . ltrim($blog->image, '/'));
        }

        $data = [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug ?? (string) $blog->id,
            'excerpt' => $blog->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $blog->content), 120),
            'image' => $blog->image ?? null,
            'image_url' => $imageUrl,
            'author' => $blog->author?->name ?? 'Ashahi Team',
            'published_at' => ($blog->published_at ?? $blog->created_at)?->toIso8601String(),
            'published_at_formatted' => ($blog->published_at ?? $blog->created_at)?->format('M d, Y'),
        ];

        if ($includeContent) {
            $data['content'] = $blog->content ?? '';
        }

        return $data;
    }
}
