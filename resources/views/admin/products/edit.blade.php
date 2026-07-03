@extends('layouts.admin')

@section('title', 'Edit Product: ' . $product->name)
@section('page-title', 'Edit Product')

@push('styles')
<style>
    /* Premium Admin Form Styling */
    :root {
        --clr-primary: #334A8E;
        --clr-primary-dark: #223468;
        --radius-card: 12px;
        --radius-input: 8px;
    }

    .admin-card {
        border: none;
        border-radius: var(--radius-card);
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        margin-bottom: 24px;
        background: #fff;
    }
    .admin-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 24px;
        border-radius: var(--radius-card) var(--radius-card) 0 0;
    }
    .admin-card .card-header h6 { color: #2B3D5D; font-weight: 700; font-size: 16px; margin: 0; }
    .admin-card .card-body { padding: 24px; }

    /* Form Controls */
    .form-label { font-weight: 600; color: #4a5568; font-size: 13px; margin-bottom: 8px; }
    .form-control, .form-select {
        border-radius: var(--radius-input); border: 1px solid #E2E8F0; padding: 10px 15px; font-size: 14px; box-shadow: none; transition: 0.2s;
    }
    .form-control:focus, .form-select:focus { border-color: var(--clr-primary); box-shadow: 0 0 0 3px rgba(51, 74, 142, 0.1); }
    .input-group-text { background: #F8FAFC; border: 1px solid #E2E8F0; color: #64748B; font-weight: 600; }

    /* Buttons & Switches */
    .btn-primary-custom { background: var(--clr-primary); border-color: var(--clr-primary); color: #fff; padding: 12px 20px; border-radius: var(--radius-input); font-weight: 600; transition: all 0.2s; }
    .btn-primary-custom:hover { background: var(--clr-primary-dark); border-color: var(--clr-primary-dark); transform: translateY(-1px); }
    
    .form-check-input:checked { background-color: var(--clr-primary); border-color: var(--clr-primary); }
    .form-switch .form-check-input { width: 40px; height: 20px; cursor: pointer; }
    .form-switch .form-check-label { margin-left: 10px; padding-top: 2px; cursor: pointer; font-weight: 500; color: #4a5568; }

    /* Image Previews & Gallery */
    .image-preview-wrapper {
        width: 100%; height: 200px; border: 2px dashed #E2E8F0; border-radius: var(--radius-input);
        display: flex; align-items: center; justify-content: center; background: #F8FAFC;
        margin-bottom: 15px; overflow: hidden; position: relative;
    }
    .image-preview-wrapper img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .image-preview-icon { font-size: 40px; color: #CBD5E1; }
    
    /* Existing Gallery Images */
    .gallery-grid { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
    .gallery-item { position: relative; width: 100px; height: 100px; border-radius: var(--radius-input); border: 1px solid #E2E8F0; overflow: hidden; background: #fff; }
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
    .btn-remove-img { 
        position: absolute; top: 5px; right: 5px; width: 24px; height: 24px; 
        background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-size: 14px; cursor: pointer; transition: 0.2s;
    }
    .btn-remove-img:hover { background: #c82333; transform: scale(1.1); }
</style>
@endpush

@section('content')

{{-- ── ERROR/SUCCESS MESSAGES ── --}}
@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    {{-- ── Left Column ── --}}
    <div class="col-lg-8">
        
        <form id="product-form" method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="card admin-card">
                <div class="card-header"><h6 class="mb-0">Basic Information</h6></div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="form-control @error('sku') is-invalid @enderror" required>
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="form-control" placeholder="Auto-generated if left empty">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="5" class="form-control">{{ old('description', $product->description) }}</textarea>
                    </div>
                    
                    <div>
                        <label class="form-label">Specifications (JSON or text)</label>
                        <textarea name="specifications" rows="3" class="form-control font-monospace">{{ old('specifications', $product->specifications) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card admin-card">
                <div class="card-header"><h6 class="mb-0">Pricing</h6></div>
                <div class="card-body row g-4">
                    <div class="col-md-4">
                        <label class="form-label">Base Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="base_price" value="{{ old('base_price', $product->base_price) }}" step="0.01" min="0" class="form-control @error('base_price') is-invalid @enderror" required>
                        </div>
                        @error('base_price')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Sale Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Discount %</label>
                        <div class="input-group">
                            <input type="number" name="discount" value="{{ old('discount', $product->discount) }}" step="0.01" min="0" max="100" class="form-control">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card admin-card">
                <div class="card-header"><h6 class="mb-0">Variants</h6></div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Available Colors</label>
                        <div class="d-flex flex-wrap gap-3 p-3 border rounded bg-light">
                            @php $selectedColors = $product->productColors->pluck('color_id')->toArray(); @endphp
                            @foreach($colors as $color)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="colors[]" value="{{ $color->id }}" id="color_{{ $color->id }}"
                                    {{ in_array($color->id, old('colors', $selectedColors)) ? 'checked' : '' }}>
                                <label class="form-check-label d-flex align-items-center gap-2" for="color_{{ $color->id }}">
                                    @if($color->hex_code)
                                        <span class="rounded-circle border shadow-sm" style="width:16px;height:16px;background:{{ $color->hex_code }};display:inline-block"></span>
                                    @endif
                                    {{ $color->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div>
                        <label class="form-label">Storage Options</label>
                        <div class="d-flex flex-wrap gap-3 p-3 border rounded bg-light">
                            @php $selectedStorages = $product->productStorages->pluck('phone_storage_id')->toArray(); @endphp
                            @foreach($storages as $storage)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="storages[]" value="{{ $storage->id }}" id="storage_{{ $storage->id }}"
                                    {{ in_array($storage->id, old('storages', $selectedStorages)) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="storage_{{ $storage->id }}">{{ $storage->capacity }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            </form>

        <div class="card admin-card border-primary">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-primary">Manage Existing Gallery Images</h6>
            </div>
            <div class="card-body bg-light rounded-bottom">
                @if(is_array($product->images) && count($product->images) > 0)
                    <div class="gallery-grid">
                        @foreach($product->images as $index => $img)
                            <div class="gallery-item shadow-sm">
                                <img src="{{ asset('storage/'.$img) }}" alt="Gallery Image">
                                <form action="{{ route('admin.products.removeImage', $product) }}" method="POST" onsubmit="return confirm('Remove this image?');">
                                    @csrf
                                    <input type="hidden" name="index" value="{{ $index }}">
                                    <button type="submit" class="btn-remove-img" title="Remove Image"><i class="bi bi-x-lg"></i></button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>To add new images, use the media uploader on the right and click "Update Product".</small>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-images fs-1 mb-2 d-block text-black-50"></i>
                        No gallery images attached to this product yet.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Right Column ── --}}
    <div class="col-lg-4">
        
        <div class="card admin-card">
            <div class="card-header"><h6 class="mb-0">Publish Status</h6></div>
            <div class="card-body d-flex flex-column gap-3">
                
                @foreach([
                    ['is_active',   'Active (Visible on store)'],
                    ['is_featured', 'Featured Product'],
                    ['is_new',      'Mark as New Arrival'],
                    ['is_pre_order','Allow Pre-orders'],
                ] as [$name, $label])
                <div class="form-check form-switch d-flex align-items-center p-0">
                    <input class="form-check-input m-0" type="checkbox" name="{{ $name }}" id="{{ $name }}" value="1" form="product-form"
                        {{ old($name, $product->$name ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
                </div>
                @endforeach

                <hr class="my-2">
                
                <div class="d-grid gap-2">
                    <button type="submit" form="product-form" class="btn btn-primary-custom">
                        <i class="bi bi-save me-2"></i>Update Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light border fw-bold text-secondary">Cancel</a>
                </div>
            </div>
        </div>

        <div class="card admin-card">
            <div class="card-header"><h6 class="mb-0">Organization</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category_id" form="product-form" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Category…</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Brand <span class="text-danger">*</span></label>
                    <select name="brand_id" form="product-form" class="form-select @error('brand_id') is-invalid @enderror" required>
                        <option value="">Select Brand…</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <!-- <div class="mb-3">
                    <label class="form-label">Phone Model <span class="text-danger">*</span></label>
                    <select name="phone_model_id" form="product-form" class="form-select @error('phone_model_id') is-invalid @enderror" required>
                        <option value="">Select Model…</option>
                        @foreach($phoneModels as $model)
                            <option value="{{ $model->id }}" @selected(old('phone_model_id', $product->phone_model_id) == $model->id)>{{ $model->brand->name }} {{ $model->name }}</option>
                        @endforeach
                    </select>
                    @error('phone_model_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div> -->
                
                <div>
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" form="product-form" value="{{ old('sort_order', $product->sort_order) }}" class="form-control" placeholder="0">
                </div>
            </div>
        </div>

        <div class="card admin-card">
            <div class="card-header"><h6 class="mb-0">Add Media</h6></div>
            <div class="card-body">
                
                <label class="form-label">Replace Thumbnail</label>
                <div class="image-preview-wrapper" id="thumbnail-preview-container">
                    @if($product->thumbnail)
                        <img id="thumbnail-preview" src="{{ asset('storage/'.$product->thumbnail) }}" alt="Current Thumbnail">
                    @else
                        <i id="thumbnail-icon" class="bi bi-image image-preview-icon"></i>
                        <img id="thumbnail-preview" src="" alt="New Thumbnail" style="display: none;">
                    @endif
                </div>
                <input type="file" name="thumbnail" id="thumbnail-input" form="product-form" class="form-control mb-4" accept="image/*">

                <div>
                    <label class="form-label">Add to Gallery</label>
                    <input type="file" name="images[]" form="product-form" class="form-control" accept="image/*" multiple>
                    <div class="form-text text-muted mt-2"><i class="bi bi-info-circle me-1"></i>Uploading files here will ADD them to the existing gallery.</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live Image Preview for Replacing Thumbnail
    document.getElementById('thumbnail-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('thumbnail-preview');
                const icon = document.getElementById('thumbnail-icon');
                
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                if(icon) icon.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush