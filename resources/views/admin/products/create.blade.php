@extends('layouts.admin')

@section('title', isset($product) ? 'Edit Product' : 'Create Product')
@section('page-title', isset($product) ? 'Edit Product' : 'New Product')



@section('content')
<form method="POST"
      action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if($product->exists) @method('PUT') @endif

    <div class="form-layout">
        
        {{-- ── LEFT COLUMN ── --}}
        <div class="main-col">
            
            {{-- Basic Info --}}
            <div class="admin-card">
                <div class="admin-card-header"><i class="fa-solid fa-circle-info" style="color: var(--text-3);"></i> Basic Information</div>
                <div class="admin-card-body">
                    
                    <div class="form-group">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-control" placeholder="e.g. Apple iPhone 15 Pro Max" required>
                    </div>
                    
                    <div class="form-row">
                        <div>
                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="form-control font-mono" placeholder="APP-IP15-PM" required>
                        </div>
                        <div>
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $product->slug ?? '') }}" class="form-control font-mono" placeholder="Auto-generated if blank">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" placeholder="Detailed product description...">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Specifications (JSON or text)</label>
                        <textarea name="specifications" class="form-control font-mono" placeholder='{"Screen": "6.7 inch", "Camera": "48MP"}'>{{ old('specifications', $product->specifications ?? '') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- Pricing --}}
            <div class="admin-card">
                <div class="admin-card-header"><i class="fa-solid fa-tag" style="color: var(--text-3);"></i> Pricing</div>
                <div class="admin-card-body form-row-3" style="margin-bottom: 0;">
                    
                    <div>
                        <label class="form-label">Base Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-text left">$</span>
                            <input type="number" name="base_price" value="{{ old('base_price', $product->base_price ?? '') }}" step="0.01" min="0" class="form-control with-left" required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="form-label">Sale Price</label>
                        <div class="input-group">
                            <span class="input-text left">$</span>
                            <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}" step="0.01" min="0" class="form-control with-left">
                        </div>
                    </div>
                    
                    <div>
                        <label class="form-label">Discount %</label>
                        <div class="input-group">
                            <input type="number" name="discount" value="{{ old('discount', $product->discount ?? 0) }}" step="0.01" min="0" max="100" class="form-control with-right">
                            <span class="input-text right">%</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Variants --}}
            <div class="admin-card">
                <div class="admin-card-header"><i class="fa-solid fa-layer-group" style="color: var(--text-3);"></i> Variants</div>
                <div class="admin-card-body">
                    
                    <div class="form-group">
                        <label class="form-label">Available Colors</label>
                        <div class="checkbox-grid">
                            @foreach($colors as $color)
                            <label class="check-item" for="color_{{ $color->id }}">
                                <input type="checkbox" name="colors[]" value="{{ $color->id }}" id="color_{{ $color->id }}"
                                    {{ in_array($color->id, old('colors', isset($product) ? $product->colors->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                <span class="check-label">
                                    @if($color->hex_code)
                                        <span class="color-swatch" style="background:{{ $color->hex_code }};"></span>
                                    @endif
                                    {{ $color->name }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Storage Options</label>
                        <div class="checkbox-grid">
                            @foreach($storages as $storage)
                            <label class="check-item" for="storage_{{ $storage->id }}">
                                <input type="checkbox" name="storages[]" value="{{ $storage->id }}" id="storage_{{ $storage->id }}"
                                    {{ in_array($storage->id, old('storages', isset($product) ? $product->storages->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                <span class="check-label" style="font-weight: 600;">{{ $storage->capacity }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── RIGHT COLUMN ── --}}
        <div class="side-col">
            
            {{-- Publish Settings --}}
            <div class="admin-card">
                <div class="admin-card-header"><i class="fa-solid fa-rocket" style="color: var(--accent-4);"></i> Publish</div>
                <div class="admin-card-body">
                    
                    @foreach([
                        ['is_active',   'Active (Visible on store)'],
                        ['is_featured', 'Featured Product'],
                        ['is_new',      'Mark as New Arrival'],
                        ['is_pre_order','Allow Pre-orders'],
                    ] as [$name, $label])
                    <div class="switch-wrap">
                        <span class="switch-label">{{ $label }}</span>
                        <label class="switch">
                            <input type="checkbox" name="{{ $name }}" value="1" {{ old($name, ($product->$name ?? false) ? '1' : '') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    @endforeach

                    <div class="btn-group-vertical">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> {{ isset($product) ? 'Update Product' : 'Save Product' }}
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>

            {{-- Organization --}}
            <div class="admin-card">
                <div class="admin-card-header"><i class="fa-solid fa-folder-tree" style="color: var(--text-3);"></i> Organization</div>
                <div class="admin-card-body">
                    
                    <div class="form-group">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category…</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <select name="brand_id" class="form-select" required>
                            <option value="">Select Brand…</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Model <span class="text-danger">*</span></label>
                        <select name="phone_model_id" class="form-select" required>
                            <option value="">Select Model…</option>
                            @foreach($phoneModels as $model)
                                <option value="{{ $model->id }}" @selected(old('phone_model_id', $product->phone_model_id ?? '') == $model->id)>{{ $model->brand->name }} {{ $model->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" class="form-control font-mono" placeholder="0">
                    </div>

                </div>
            </div>

            {{-- Media --}}
            <div class="admin-card">
                <div class="admin-card-header"><i class="fa-solid fa-images" style="color: var(--text-3);"></i> Media</div>
                <div class="admin-card-body">
                    
                    <label class="form-label">Main Thumbnail</label>
                    <div class="image-preview-wrapper" id="thumbnail-preview-container">
                        @if(isset($product) && $product->thumbnail)
                            <img id="thumbnail-preview" src="{{ asset('storage/'.$product->thumbnail) }}" alt="Thumbnail">
                        @else
                            <i id="thumbnail-icon" class="fa-regular fa-image image-preview-icon"></i>
                            <img id="thumbnail-preview" src="" alt="Thumbnail" style="display: none;">
                        @endif
                    </div>
                    <input type="file" name="thumbnail" id="thumbnail-input" class="form-control" accept="image/*" style="margin-bottom: 20px;">

                    <div style="margin-bottom: 0;">
                        <label class="form-label">Gallery Images</label>
                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                        <div class="form-text">Hold CTRL/CMD to select multiple files.</div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Live Image Preview for Thumbnail
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