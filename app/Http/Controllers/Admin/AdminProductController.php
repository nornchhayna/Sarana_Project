<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Gift;
use App\Models\PhoneModel;
use App\Models\PhoneStorage;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        // PRO-TIP: Eager loading ('with') prevents the N+1 query problem!
        $query = Product::with(['category', 'brand', 'phoneModel', 'variants']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('sku', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $phoneModels = PhoneModel::where('is_active', true)->with('brand')->get();
        $colors = Color::where('is_active', true)->get();
        $gifts = Gift::where('is_active', true)->get();
        $storages = PhoneStorage::where('is_active', true)->get();
        $rams = \App\Models\PhoneRam::where('is_active', true)->get();
        $product = new Product;

        return view('admin.products.create', compact(
            'categories', 'brands', 'phoneModels', 'colors', 'gifts', 'storages', 'rams', 'product'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'phone_model_id' => 'nullable|exists:phone_models,id',
            'sku' => 'required|string|unique:products,sku',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'details' => 'nullable|array',
            'base_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'is_pre_order' => 'boolean',
            'thumbnail' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'colors' => 'nullable|array',
            'colors.*.id' => 'exists:colors,id',
            'colors.*.price_adjustment' => 'nullable|numeric',
            'storages' => 'nullable|array',
            'storages.*.id' => 'exists:phone_storages,id',
            'storages.*.price_adjustment' => 'nullable|numeric',
            'variants' => 'nullable|array',
            'variants.*.color_id' => 'required|exists:colors,id',
            'variants.*.phone_storage_id' => 'required|exists:phone_storages,id',
            'variants.*.phone_ram_id' => 'nullable|exists:phone_rams,id',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:255',
            'color_images' => 'nullable|array',
            'color_images.*' => 'image|max:2048',
            'gifts' => 'nullable|array',
            'gifts.*' => 'exists:gifts,id',
            'gift_quantity' => 'nullable|array',
            'gift_quantity.*' => 'integer|min:1',
            'rams' => 'nullable|array',
            'rams.*' => 'exists:phone_rams,id',
            'ram_price' => 'nullable|array',
            'ram_price.*' => 'numeric|min:0',
            'product_rams' => 'nullable|array',
            'product_rams.*' => 'exists:phone_rams,id',
            'ram_price' => 'nullable|array',
            'ram_price.*' => 'numeric|min:0',
        ]);
      

        $validated['slug'] = Str::slug($validated['name']);

        // PRO-TIP: DB::transaction ensures that if image upload fails, the product is NOT saved (No corrupt data!)
        DB::transaction(function () use ($request, &$validated) {

            // Handle thumbnail
            if ($request->hasFile('thumbnail')) {
                $validated['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
            }

            // Handle gallery images
            if ($request->hasFile('images')) {
                $paths = [];
                foreach ($request->file('images') as $img) {
                    $paths[] = $img->store('products', 'public');
                }
                $validated['images'] = $paths;
            }

            $product = Product::create($validated);

            // Sync colors
            if (! empty($validated['colors'])) {
                foreach ($validated['colors'] as $colorId) {
                    // Adjusted array access depending on how your HTML form sends data.
                    // Assuming basic checkbox array: name="colors[]" value="ID"
                    $product->productColors()->create([
                        'color_id' => is_array($colorId) ? $colorId['id'] : $colorId,
                        'price_adjustment' => 0,
                    ]);
                }
            }

            // Sync storages
            if (! empty($validated['storages'])) {
                foreach ($validated['storages'] as $storageId) {
                    $product->productStorages()->create([
                        'phone_storage_id' => is_array($storageId) ? $storageId['id'] : $storageId,
                        'price_adjustment' => 0,
                    ]);
                }
            }
            // Create product variants
            if (! empty($validated['variants'])) {
                foreach ($validated['variants'] as $variantData) {
                    $product->variants()->create([
                        'color_id' => $variantData['color_id'],
                        'phone_storage_id' => $variantData['phone_storage_id'],
                        'phone_ram_id' => $variantData['phone_ram_id'] ?? null,
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                        'sku' => $variantData['sku'] ?? null,
                        'is_active' => true,
                    ]);
                }
            }

            // Handle color-specific images
            if (! empty($validated['color_images'])) {
                foreach ($validated['color_images'] as $colorId => $imageFile) {
                    if ($imageFile && $imageFile->isValid()) {
                        $path = $imageFile->store('products', 'public');
                        $product->variantImages()->create([
                            'color_id' => $colorId,
                            'image' => $path,
                            'is_primary' => true,
                            'sort_order' => 0,
                        ]);
                    }
                }
            }

            // Sync gifts
            if (! empty($validated['gifts'])) {
                $syncData = [];
                foreach ($validated['gifts'] as $giftId) {
                    $qty = $validated['gift_quantity'][$giftId] ?? 1;
                    $syncData[$giftId] = ['quantity' => $qty];
                }
                $product->gifts()->sync($syncData);
            }

            // Sync RAMs — change $validated['rams'] to $validated['product_rams']:
            if (! empty($validated['product_rams'])) {
                $syncData = [];
                foreach ($validated['product_rams'] as $ramId) {
                $price = $validated['ram_price'][$ramId] ?? 0;
                $syncData[$ramId] = ['price' => $price, 'is_active' => true];
                }
                    $product->rams()->sync($syncData);
            }

            // Initialize stock entries (legacy - for backward compatibility)
            $colorIds = collect($validated['colors'] ?? [])->map(fn ($c) => is_array($c) ? $c['id'] : $c)->toArray();
            $storageIds = collect($validated['storages'] ?? [])->map(fn ($s) => is_array($s) ? $s['id'] : $s)->toArray();

            $initialStock = (int) ($validated['stock'] ?? $request->input('stock', 0));
            if (empty($colorIds) && empty($storageIds)) {
                Stock::create(['product_id' => $product->id, 'quantity' => $initialStock]);
            } else {
                foreach ($colorIds ?: [null] as $cid) {
                    foreach ($storageIds ?: [null] as $sid) {
                        Stock::firstOrCreate([
                            'product_id' => $product->id,
                            'color_id' => $cid,
                            'phone_storage_id' => $sid,
                        ], ['quantity' => 0]);
                    }
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load([
            'category', 'brand', 'phoneModel',
            'productColors.color', 'productStorages.phoneStorage',
            'stock.color', 'stock.phoneStorage',
            'variants.color', 'variants.phoneStorage', 'variants.phoneRam',
            'reviews' => fn ($q) => $q->latest()->take(5),
        ]);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['productColors', 'productStorages', 'stock', 'gifts', 'rams', 'variants', 'variantImages']);
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $phoneModels = PhoneModel::where('is_active', true)->with('brand')->get();
        $colors = Color::where('is_active', true)->get();
        $gifts = Gift::where('is_active', true)->get();
        $storages = PhoneStorage::where('is_active', true)->get();
        $rams = \App\Models\PhoneRam::where('is_active', true)->get();
        $statusOptions = ['active', 'inactive', 'draft'];

        return view('admin.products.edit', compact(
            'product', 'categories', 'brands', 'phoneModels','gifts', 'colors', 'storages', 'rams', 'statusOptions'
        ));
    }

    public function toggleFeatured(Product $product)
    {
        $product->is_featured = ! $product->is_featured;
        $product->save();

        $statusMessage = $product->is_featured ? 'marked as featured' : 'removed from featured';

        return back()->with('success', "Product successfully {$statusMessage}.");
    }

    public function update(Request $request, Product $product)
    {

        // FIX: Added variant validation so users can update Colors & Storages!
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'phone_model_id' => 'nullable|exists:phone_models,id',
            'sku' => 'required|string|unique:products,sku,'.$product->id,
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'details' => 'nullable|array',
            'base_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'is_pre_order' => 'boolean',
            'thumbnail' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'colors' => 'nullable|array',
            'storages' => 'nullable|array',
            'variants' => 'nullable|array',
            'variants.*.color_id' => 'required|exists:colors,id',
            'variants.*.phone_storage_id' => 'required|exists:phone_storages,id',
            'variants.*.phone_ram_id' => 'nullable|exists:phone_rams,id',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:255',
            'color_images' => 'nullable|array',
            'color_images.*' => 'image|max:2048',
            'gifts' => 'nullable|array',
            'gifts.*' => 'exists:gifts,id',
            'gift_quantity' => 'nullable|array',
            'gift_quantity.*' => 'integer|min:1',
            'rams' => 'nullable|array',
            'rams.*' => 'exists:phone_rams,id',
            'ram_price' => 'nullable|array',
            'ram_price.*' => 'numeric|min:0',
        ]);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_new'] = $request->boolean('is_new');
        $validated['is_pre_order'] = $request->boolean('is_pre_order');

        // FIX: Wrapped in DB transaction just like store()
        DB::transaction(function () use ($request, $product, &$validated) {

            // 1. Image Processing
            if ($request->hasFile('thumbnail')) {
                if ($product->thumbnail) {
                    Storage::disk('public')->delete($product->thumbnail);
                }
                $validated['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
            }

            if ($request->hasFile('images')) {
                $paths = [];
                foreach ($request->file('images') as $img) {
                    $paths[] = $img->store('products', 'public');
                }
                $validated['images'] = array_merge($product->images ?? [], $paths);
            }

            // 2. Update Product Core Data
            $product->update($validated);

            // 3. Sync Colors (Deletes unchecked ones, adds checked ones)
            $colorIds = collect($validated['colors'] ?? [])->map(fn ($c) => is_array($c) ? $c['id'] : $c)->toArray();
            $product->productColors()->whereNotIn('color_id', $colorIds)->delete();

            foreach ($colorIds as $cid) {
                $product->productColors()->updateOrCreate(
                    ['color_id' => $cid],
                    ['price_adjustment' => 0]
                );
            }

            // 4. Sync Storages
            $storageIds = collect($validated['storages'] ?? [])->map(fn ($s) => is_array($s) ? $s['id'] : $s)->toArray();
            $product->productStorages()->whereNotIn('phone_storage_id', $storageIds)->delete();

            foreach ($storageIds as $sid) {
                $product->productStorages()->updateOrCreate(
                    ['phone_storage_id' => $sid],
                    ['price_adjustment' => 0]
                );
            }

            // 5. Update Product Variants
            if (! empty($validated['variants'])) {
                // Delete all existing variants for this product
                $product->variants()->delete();

                // Create new variants from the submitted data
                foreach ($validated['variants'] as $variantData) {
                    $product->variants()->create([
                        'color_id' => $variantData['color_id'],
                        'phone_storage_id' => $variantData['phone_storage_id'],
                        'phone_ram_id' => $variantData['phone_ram_id'] ?? null,
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                        'sku' => $variantData['sku'] ?? null,
                        'is_active' => true,
                    ]);
                }
            }

            // 6. Handle color-specific images
            if (! empty($validated['color_images'])) {
                // Delete existing images for colors being updated
                foreach (array_keys($validated['color_images']) as $colorId) {
                    $product->variantImages()->where('color_id', $colorId)->delete();
                }
                
                // Create new images
                foreach ($validated['color_images'] as $colorId => $imageFile) {
                    if ($imageFile && $imageFile->isValid()) {
                        $path = $imageFile->store('products', 'public');
                        $product->variantImages()->create([
                            'color_id' => $colorId,
                            'image' => $path,
                            'is_primary' => true,
                            'sort_order' => 0,
                        ]);
                    }
                }
            }

            // 7. Sync gifts
            if (! empty($validated['gifts'])) {
                $syncData = [];
                foreach ($validated['gifts'] as $giftId) {
                    $qty = $validated['gift_quantity'][$giftId] ?? 1;
                    $syncData[$giftId] = ['quantity' => $qty];
                }
                $product->gifts()->sync($syncData);
            }

            // 8. Sync RAMs
            if (! empty($validated['rams'])) {
                $syncData = [];
                foreach ($validated['rams'] as $ramId) {
                    $price = $validated['ram_price'][$ramId] ?? 0;
                    $syncData[$ramId] = ['price' => $price, 'is_active' => true];
                }
                $product->rams()->sync($syncData);
            }

            // 8. Update Stock Combinations safely (Adds new combinations without deleting existing quantities)
            if (empty($colorIds) && empty($storageIds)) {
                $baseStock = Stock::firstOrCreate(['product_id' => $product->id, 'color_id' => null, 'phone_storage_id' => null], ['quantity' => 0]);
                if ($request->has('stock')) {
                    $baseStock->update(['quantity' => (int) $request->input('stock', 0)]);
                }
            } else {
                foreach ($colorIds ?: [null] as $cid) {
                    foreach ($storageIds ?: [null] as $sid) {
                        Stock::firstOrCreate([
                            'product_id' => $product->id,
                            'color_id' => $cid,
                            'phone_storage_id' => $sid,
                        ], ['quantity' => 0]);
                    }
                }
            }
        });

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }
        foreach ($product->images ?? [] as $img) {
            Storage::disk('public')->delete($img);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function removeImage(Request $request, Product $product)
    {
        $index = $request->input('index');
        $images = $product->images ?? [];

        if (isset($images[$index])) {
            Storage::disk('public')->delete($images[$index]);
            array_splice($images, $index, 1);
            $product->update(['images' => array_values($images)]);
        }

        return back()->with('success', 'Image removed.');
    }
}
