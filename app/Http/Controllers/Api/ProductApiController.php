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

    private function success(mixed $data, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    private function error(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message, 'errors' => $errors], $status);
    }

    // ─── Endpoints ───────────────────────────────────────────────────────────

    /**
     * GET /api/products
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category:id,name', 'brand:id,name'])->available();

        if ($search = $request->input('search')) {
            $query->search($search);
        }
        if ($categoryId = $request->input('category_id')) {
            $query->byCategory($categoryId);
        }
        if ($brandId = $request->input('brand_id')) {
            $query->byBrand($brandId);
        }
        if ($request->boolean('featured')) {
            $query->featured();
        }
        if ($request->boolean('pre_order')) {
            $query->preOrder();
        }
        if ($min = $request->input('min_price')) {
            $query->where('base_price', '>=', $min);
        }
        if ($max = $request->input('max_price')) {
            $query->where('base_price', '<=', $max);
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'popular' => $query->orderByDesc('total_sold'),
            'rating' => $query->orderByDesc('avg_rating'),
            default => $query->latest(),
        };

        $perPage = min((int) $request->input('per_page', 15), 100);
        $products = $query->paginate($perPage);

        return $this->success($products);
    }

    /**
     * GET /api/products/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::with(['category:id,name', 'brand:id,name'])
            ->where('slug', $slug)
            ->available()
            ->first();

        if (! $product) {
            return $this->error('Product not found', 404);
        }

        return $this->success($product);
    }

    /**
     * GET /api/products/token/{token}
     */
    public function showByToken(string $token): JsonResponse
    {
        $product = Product::with(['category:id,name', 'brand:id,name'])
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
        $products = Product::with(['category:id,name', 'brand:id,name'])
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

        // Handle file uploads
        if ($request->hasFile('thumbnails')) {
            $data['images'] = $this->handleThumbnails($request);
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        }

        // Set boolean fields
        $data['is_featured'] = $request->boolean('is_featured', false);
        $data['is_pre_order'] = $request->boolean('is_pre_order', false);
        $data['is_new'] = $request->boolean('is_new', false);
        $data['is_active'] = $request->boolean('is_active', true);

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = str()->slug($data['name']);
        }

        // Calculate sale price from discount if provided
        if (! empty($data['discount']) && $data['discount'] > 0) {
            $data['sale_price'] = $data['price'] - ($data['price'] * ($data['discount'] / 100));
        }

        // Map price to base_price for database
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

        // Handle file uploads
        if ($request->hasFile('thumbnails')) {
            // Delete old images
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

        // Set boolean fields
        $data['is_featured'] = $request->boolean('is_featured', $product->is_featured);
        $data['is_pre_order'] = $request->boolean('is_pre_order', $product->is_pre_order);
        $data['is_new'] = $request->boolean('is_new', $product->is_new);
        $data['is_active'] = $request->boolean('is_active', $product->is_active);

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = str()->slug($data['name'] ?? $product->name);
        }

        // Calculate sale price from discount if provided
        if (isset($data['discount']) && $data['discount'] > 0) {
            $price = $data['price'] ?? $product->base_price;
            $data['sale_price'] = $price - ($price * ($data['discount'] / 100));
        } elseif (isset($data['discount']) && $data['discount'] == 0) {
            $data['sale_price'] = null;
        }

        // Map price to base_price for database
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
