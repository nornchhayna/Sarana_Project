<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductApiController extends Controller
{
    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function success(mixed $data, string $message = 'OK', int $status = 200, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $extra), $status);
    }

    private function error(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message, 'errors' => $errors], $status);
    }

    // ─── Endpoints ───────────────────────────────────────────────────────────

    /**
     * GET /api/v1/products
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with([
            'category:id,name,slug',
            'brand:id,name,slug',
            'phoneModel:id,name',
            'colors',
            'storages',
            'rams',
            'variants.color',
            'variants.storage',
            'variants.ram',
            'stock',
            'gifts',
        ])
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->where('is_active', true);

        if ($search = ($request->input('search') ?? $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        } elseif ($category = $request->input('category')) {
            if (is_numeric($category)) {
                $query->where('category_id', (int) $category);
            } else {
                $query->whereHas('category', fn ($q) => $q->where('slug', $category)->orWhere('name', $category));
            }
        }

        if ($brandId = $request->input('brand_id')) {
            $query->where('brand_id', $brandId);
        } elseif ($brand = $request->input('brand')) {
            if (is_numeric($brand)) {
                $query->where('brand_id', (int) $brand);
            } else {
                $query->whereHas('brand', fn ($q) => $q->where('slug', $brand)->orWhere('name', $brand));
            }
        }

        if ($request->boolean('featured') || $request->boolean('is_featured')) {
            $query->where('is_featured', true);
        }

        if ($request->boolean('is_new')) {
            $query->where('is_new', true);
        }

        if ($request->boolean('pre_order') || $request->boolean('is_pre_order')) {
            $query->where('is_pre_order', true);
        }

        if ($min = $request->input('min_price')) {
            $query->where('base_price', '>=', $min);
        }

        if ($max = $request->input('max_price')) {
            $query->where('base_price', '<=', $max);
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'price_asc', 'price_low' => $query->orderBy('base_price', 'asc'),
            'price_desc', 'price_high' => $query->orderByDesc('base_price'),
            'popular', 'best_seller' => $query->orderByDesc('total_sold'),
            'rating' => $query->orderByDesc('avg_rating'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->latest(),
        };

        $perPage = min((int) $request->input('per_page', 15), 100);
        $products = $query->paginate($perPage);

        return $this->success($products, 'OK', 200, [
            'products' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
            ],
        ]);
    }

    /**
     * GET /api/v1/products/{slugOrId}
     */
    public function show(string $slugOrId): JsonResponse
    {
        $product = Product::with([
            'category',
            'brand',
            'phoneModel',
            'colors',
            'storages',
            'rams',
            'variants.color',
            'variants.storage',
            'variants.ram',
            'variantImages',
            'stock',
            'gifts',
        ])
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->where('is_active', true)
            ->where(function ($q) use ($slugOrId) {
                $q->where('slug', $slugOrId);
                if (is_numeric($slugOrId)) {
                    $q->orWhere('id', (int) $slugOrId);
                }
            })
            ->first();

        if (! $product) {
            return $this->error('Product not found', 404);
        }

        $reviews = $product->reviews()
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'product_id' => $r->product_id,
                    'user_id' => $r->user_id,
                    'rating' => (int) $r->rating,
                    'title' => $r->title,
                    'body' => $r->body,
                    'comment' => $r->body,
                    'images' => $r->images ?? [],
                    'is_approved' => (bool) $r->is_approved,
                    'user' => $r->user ? [
                        'id' => $r->user->id,
                        'first_name' => $r->user->first_name,
                        'last_name' => $r->user->last_name,
                        'name' => $r->user->name,
                        'avatar' => $r->user->avatar,
                        'avatar_url' => $r->user->avatar_url,
                    ] : null,
                    'created_at' => $r->created_at?->toIso8601String(),
                ];
            });

        $relatedProducts = Product::with([
            'category:id,name,slug',
            'brand:id,name,slug',
            'phoneModel:id,name',
            'colors',
            'storages',
            'variants.color',
            'variants.storage',
            'variants.ram',
            'stock',
        ])
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(6)
            ->get();

        $productArray = $product->toArray();
        $productArray['reviews'] = $reviews;
        $productArray['related_products'] = $relatedProducts;

        return $this->success($productArray, 'OK', 200, [
            'product' => $productArray,
            'reviews' => $reviews,
            'related_products' => $relatedProducts,
        ]);
    }

    /**
     * GET /api/products/token/{token}
     */
    public function showByToken(string $token): JsonResponse
    {
        $product = Product::with(['category:id,name,slug', 'brand:id,name,slug'])
            ->where('token', $token)
            ->available()
            ->first();

        if (! $product) {
            return $this->error('Product not found', 404);
        }

        return $this->success($product);
    }

    /**
     * GET /api/products/featured
     */
    public function featured(): JsonResponse
    {
        $products = Product::with(['category:id,name,slug', 'brand:id,name,slug', 'variants', 'stock'])
            ->available()
            ->featured()
            ->latest()
            ->limit(12)
            ->get();

        return $this->success($products);
    }

    /**
     * POST /api/products  (admin/authenticated)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $data = $validator->validated();

        if ($request->hasFile('thumbnails')) {
            $data['images'] = $this->handleThumbnails($request);
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        }

        $data['is_featured'] = $request->boolean('is_featured', false);
        $data['is_pre_order'] = $request->boolean('is_pre_order', false);
        $data['is_new'] = $request->boolean('is_new', false);
        $data['is_active'] = $request->boolean('is_active', true);

        if (empty($data['slug'])) {
            $data['slug'] = str()->slug($data['name']);
        }

        if (! empty($data['discount']) && $data['discount'] > 0) {
            $data['sale_price'] = $data['price'] - ($data['price'] * ($data['discount'] / 100));
        }

        $data['base_price'] = $data['price'];
        unset($data['price']);

        $product = Product::create($data);
        $product->load(['category:id,name', 'brand:id,name']);

        return $this->success($product, 'Product created successfully', 201);
    }

    /**
     * PUT /api/products/{id}  (admin/authenticated)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::find($id);

        if (! $product) {
            return $this->error('Product not found', 404);
        }

        $validator = Validator::make($request->all(), $this->rules($id));

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $data = $validator->validated();

        if ($request->hasFile('thumbnails')) {
            foreach ((array) $product->images as $old) {
                Storage::disk('public')->delete($old);
            }
            $data['images'] = $this->handleThumbnails($request);
        }

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        }

        $data['is_featured'] = $request->boolean('is_featured', $product->is_featured);
        $data['is_pre_order'] = $request->boolean('is_pre_order', $product->is_pre_order);
        $data['is_new'] = $request->boolean('is_new', $product->is_new);
        $data['is_active'] = $request->boolean('is_active', $product->is_active);

        if (empty($data['slug'])) {
            $data['slug'] = str()->slug($data['name'] ?? $product->name);
        }

        if (isset($data['discount']) && $data['discount'] > 0) {
            $price = $data['price'] ?? $product->base_price;
            $data['sale_price'] = $price - ($price * ($data['discount'] / 100));
        } elseif (isset($data['discount']) && $data['discount'] == 0) {
            $data['sale_price'] = null;
        }

        if (isset($data['price'])) {
            $data['base_price'] = $data['price'];
            unset($data['price']);
        }

        $product->update($data);
        $product->load(['category:id,name', 'brand:id,name']);

        return $this->success($product, 'Product updated successfully');
    }

    /**
     * DELETE /api/products/{id}  (admin/authenticated)
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::find($id);

        if (! $product) {
            return $this->error('Product not found', 404);
        }

        $product->delete();

        return $this->success(null, 'Product deleted successfully');
    }

    // ─── Private ─────────────────────────────────────────────────────────────

    private function rules(?int $id = null): array
    {
        $uniqueSlug = 'unique:products,slug'.($id ? ",{$id}" : '');
        $uniqueSku = 'unique:products,sku'.($id ? ",{$id}" : '');
        $uniqueToken = 'unique:products,token'.($id ? ",{$id}" : '');

        return [
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'phone_model_id' => 'nullable|exists:phone_models,id',
            'name' => 'required|string|max:255',
            'slug' => "nullable|string|max:255|{$uniqueSlug}",
            'sku' => "nullable|string|max:255|{$uniqueSku}",
            'token' => "nullable|string|max:64|{$uniqueToken}",
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'details' => 'nullable|array',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'data_from' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
            'is_pre_order' => 'nullable|boolean',
            'is_new' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive,draft,out_of_stock',
            'sort_order' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,webp,gif|max:2048',
            'thumbnails.*' => 'nullable|image|mimes:jpeg,png,webp,gif|max:2048',
        ];
    }

    private function handleThumbnails(Request $request): array
    {
        $paths = [];
        if ($request->hasFile('thumbnails')) {
            foreach ($request->file('thumbnails') as $file) {
                $paths[] = $file->store('products/images', 'public');
            }
        }

        return $paths;
    }
}
