@extends('layouts.admin')

@section('title', isset($product) ? 'Edit Product' : 'Create Product')
@section('page-title', isset($product) ? 'Edit Product' : 'New Product')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ isset($product) ? 'Edit Product' : 'New Product' }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.products.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <form method="POST"
          action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if($product->exists) @method('PUT') @endif

        <div class="card-body">
            <div class="row">
                {{-- LEFT COLUMN --}}
                <div class="col-md-8">

                    {{-- Basic Info --}}
                    <div class="card card-outline card-info mb-3">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Basic Information</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label class="col-form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-control" placeholder="e.g. Apple iPhone 15 Pro Max" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">SKU <span class="text-danger">*</span></label>
                                        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="form-control" placeholder="APP-IP15-PM" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Slug</label>
                                        <input type="text" name="slug" value="{{ old('slug', $product->slug ?? '') }}" class="form-control" placeholder="Auto-generated if blank">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Detailed product description...">{{ old('description', $product->description ?? '') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="col-form-label">Specifications (JSON or text)</label>
                                <textarea name="specifications" class="form-control" rows="3" placeholder='{"Screen": "6.7 inch", "Camera": "48MP"}'>{{ old('specifications', $product->specifications ?? '') }}</textarea>
                            </div>

                        </div>
                    </div>

                    {{-- Pricing & Stock --}}
                    <div class="card card-outline card-success mb-3">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tag"></i> Pricing & Initial Stock</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label">Base Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" name="base_price" value="{{ old('base_price', $product->base_price ?? '') }}" step="0.01" min="0" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label">Sale Price</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}" step="0.01" min="0" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label">Discount %</label>
                                        <div class="input-group">
                                            <input type="number" name="discount" value="{{ old('discount', $product->discount ?? 0) }}" step="0.01" min="0" max="100" class="form-control">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label">Stock <small class="text-muted">(Single product)</small></label>
                                        <div class="input-group">
                                            <input type="number" name="stock" value="{{ old('stock', isset($product) ? $product->total_stock : 0) }}" min="0" class="form-control" placeholder="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-boxes"></i></span>
                                            </div>
                                        </div>
                                        <small class="text-muted">Used if no variants are added below</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

            {{-- Variants --}}
            <div class="card card-outline card-primary mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-layer-group"></i> Product Variants</h3>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label class="col-form-label">Available Colors</label>
                        <div class="row">
                            @foreach($colors as $color)
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="colors[]" value="{{ $color->id }}" id="color_{{ $color->id }}" class="custom-control-input"
                                        {{ in_array($color->id, old('colors', isset($product) ? $product->colors->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                    <label for="color_{{ $color->id }}" class="custom-control-label">
                                        @if($color->hex_code)
                                            <span style="display:inline-block; width:16px; height:16px; background:{{ $color->hex_code }}; border-radius:50%; margin-right:5px; border:1px solid #ccc;"></span>
                                        @endif
                                        {{ $color->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">Storage Options</label>
                        <div class="row">
                            @foreach($storages as $storage)
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="storages[]" value="{{ $storage->id }}" id="storage_{{ $storage->id }}" class="custom-control-input"
                                        {{ in_array($storage->id, old('storages', isset($product) ? $product->storages->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                    <label for="storage_{{ $storage->id }}" class="custom-control-label font-weight-bold">
                                        {{ $storage->capacity }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">RAM Options</label>
                        <div class="row">
                            @foreach($rams as $ram)
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="rams[]" value="{{ $ram->id }}" id="ram_{{ $ram->id }}" class="custom-control-input"
                                        {{ in_array($ram->id, old('rams', isset($product) ? $product->variants->pluck('phone_ram_id')->unique()->filter()->toArray() : [])) ? 'checked' : '' }}>
                                    <label for="ram_{{ $ram->id }}" class="custom-control-label font-weight-bold">
                                        {{ $ram->capacity }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">Variant Details</label>
                        <p class="text-muted small">Set price, stock, and SKU for each Color + Storage + RAM combination.</p>
                        <div id="variants-container" style="display: none;">
                            <!-- Variants will be dynamically added here -->
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">Color Images</label>
                        <p class="text-muted small">Upload 1 image for each color.</p>
                        <div id="color-images-container" style="display: none;">
                            <!-- Color image upload sections will be dynamically added here -->
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-md-4">

            {{-- Publish Settings --}}
            <div class="card card-outline card-warning mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-rocket"></i> Publish</h3>
                </div>
                <div class="card-body">

                    @foreach([
                        ['is_active',   'Active (Visible on store)'],
                        ['is_featured', 'Featured Product'],
                        ['is_new',      'Mark as New Arrival'],
                        ['is_pre_order','Allow Pre-orders'],
                    ] as [$name, $label])
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="{{ $name }}" value="1" id="{{ $name }}" class="custom-control-input" {{ old($name, ($product->$name ?? false) ? '1' : '') ? 'checked' : '' }}>
                            <label for="{{ $name }}" class="custom-control-label">{{ $label }}</label>
                        </div>
                    </div>
                    @endforeach

                    <div class="btn-group-vertical w-100">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> {{ isset($product) ? 'Update Product' : 'Save Product' }}
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>

            {{-- Organization --}}
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-folder-tree"></i> Organization</h3>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label class="col-form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-control select2" required>
                            <option value="">Select Category…</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">Brand <span class="text-danger">*</span></label>
                        <select name="brand_id" class="form-control select2" required>
                            <option value="">Select Brand…</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">Phone Model <span class="text-muted">(Optional)</span></label>
                        <select name="phone_model_id" class="form-control select2">
                            <option value="">Select Model…</option>
                            @foreach($phoneModels as $model)
                                <option value="{{ $model->id }}" @selected(old('phone_model_id', $product->phone_model_id ?? '') == $model->id)>{{ $model->brand->name }} {{ $model->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" class="form-control" placeholder="0">
                    </div>

                </div>
            </div>

            {{-- Media --}}
            <div class="card card-outline card-info mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-images"></i> Media</h3>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label class="col-form-label">Main Thumbnail</label>
                        <div class="text-center mb-2" id="thumbnail-preview-container">
                            @if(isset($product) && $product->thumbnail)
                                <img id="thumbnail-preview" src="{{ asset('storage/'.$product->thumbnail) }}" alt="Thumbnail" style="max-width:200px; max-height:200px;">
                            @else
                            <i id="thumbnail-icon" class="fas fa-image fa-3x text-muted"></i>
                            <img id="thumbnail-preview" src="" alt="Thumbnail" style="display:none; max-width:200px; max-height:200px;">
                            @endif
                        </div>
                        <input type="file" name="thumbnail" id="thumbnail-input" class="form-control-file" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">Gallery Images</label>
                        <input type="file" name="images[]" class="form-control-file" accept="image/*" multiple>
                        <small class="form-text text-muted">Hold CTRL/CMD to select multiple files.</small>
                    </div>

                </div>
            </div>

            {{-- Free Gifts --}}
            <div class="card card-outline card-danger mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-gift"></i> Free Gifts Included</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Select free items that customers will receive when purchasing this product.
                    </p>
                    @if($gifts->isNotEmpty())
                        @php $selectedGifts = isset($product) ? $product->gifts->pluck('id')->toArray() : []; @endphp
                        @foreach($gifts as $gift)
                            @php $attached = $product->gifts->firstWhere('id', $gift->id) ?? null; @endphp
                            <div class="custom-control custom-checkbox mb-2 p-2 border rounded">
                                <input type="checkbox" name="gifts[]" value="{{ $gift->id }}" id="gift_{{ $gift->id }}" class="custom-control-input"
                                    {{ in_array($gift->id, old('gifts', $selectedGifts)) ? 'checked' : '' }}>
                                <label for="gift_{{ $gift->id }}" class="custom-control-label d-flex align-items-center">
                                    <span class="flex-grow-1">{{ $gift->name }}</span>
                                    <input type="number" name="gift_quantity[{{ $gift->id }}]" min="1"
                                           value="{{ $attached?->pivot->quantity ?? 1 }}"
                                           class="form-control form-control-sm" style="width: 70px;" placeholder="Qty">
                                </label>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-warning">
                            No active gifts available. <a href="{{ route('admin.gifts.create') }}">Create one</a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ isset($product) ? 'Update Product' : 'Save Product' }}
        </button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-default">Cancel</a>
    </div>
</form>


@push('scripts')
<script>

    // Live Image Preview for Thumbnail
    var thumbInput = document.getElementById('thumbnail-input');
    if (thumbInput) {
        thumbInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById('thumbnail-preview');
                    const icon = document.getElementById('thumbnail-icon');
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    if (icon) icon.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        try {
            const colorCheckboxes = document.querySelectorAll('input[name="colors[]"]');
            const storageCheckboxes = document.querySelectorAll('input[name="storages[]"]');
            const ramCheckboxes = document.querySelectorAll('input[name="rams[]"]');
            const variantsContainer = document.getElementById('variants-container');
            const colorImagesContainer = document.getElementById('color-images-container');

            // Visible diagnostic count, always shown at the top of Variant Details
            const debugLine = document.createElement('div');
            debugLine.style.cssText = 'font-size:11px; color:#059669; margin-bottom:8px; font-family:monospace;';
            debugLine.textContent = 'Found: ' + colorCheckboxes.length + ' colors, ' + storageCheckboxes.length + ' storages, ' + ramCheckboxes.length + ' rams. variants-container found: ' + (!!variantsContainer);
            if (variantsContainer && variantsContainer.parentNode) {
                variantsContainer.parentNode.insertBefore(debugLine, variantsContainer);
            }

            if (!variantsContainer) {
                throw new Error('variants-container element not found in DOM');
            }

            @php
                $existingVariantsData = isset($product) ? $product->variants->toArray() : [];
                $existingColorImagesData = isset($product) ? $product->variantImages->keyBy('color_id')->toArray() : [];
            @endphp
            const existingVariants = @json($existingVariantsData);
            const existingColorImages = @json($existingColorImagesData);

            function getChecked(list) {
                return Array.from(list)
                    .filter(cb => cb.checked)
                    .map(cb => {
                        const wrapper = cb.closest('.custom-control') || cb.closest('label') || cb.parentElement;
                        const labelEl = wrapper ? wrapper.querySelector('.custom-control-label') : null;
                        return {
                            id: cb.value,
                            name: labelEl ? labelEl.textContent.trim() : cb.value
                        };
                    });
            }

            function generateVariants() {
                const selectedColors = getChecked(colorCheckboxes);
                const selectedStorages = getChecked(storageCheckboxes);
                const selectedRams = getChecked(ramCheckboxes);

                if (selectedColors.length === 0 || selectedStorages.length === 0) {
                    variantsContainer.style.display = 'none';
                    variantsContainer.innerHTML = '';
                    return;
                }

                const ramsToUse = selectedRams.length > 0 ? selectedRams : [{ id: '', name: 'No RAM' }];

                variantsContainer.style.display = 'block';
                variantsContainer.innerHTML = '';

                let variantIndex = 0;
                selectedColors.forEach(color => {
                    selectedStorages.forEach(storage => {
                        ramsToUse.forEach(ram => {
                            const existingVariant = existingVariants.find(v =>
                                v.color_id == color.id &&
                                v.phone_storage_id == storage.id &&
                                v.phone_ram_id == ram.id
                            );

                            const variantRow = document.createElement('div');
                            variantRow.className = 'card card-outline card-secondary mb-2 p-2';

                            const variantLabel = ram.id ? (color.name + ' + ' + storage.name + ' + ' + ram.name) : (color.name + ' + ' + storage.name);

                            variantRow.innerHTML = `
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <strong>${variantLabel}</strong>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="hidden" name="variants[${variantIndex}][color_id]" value="${color.id}">
                                        <input type="hidden" name="variants[${variantIndex}][phone_storage_id]" value="${storage.id}">
                                        <input type="hidden" name="variants[${variantIndex}][phone_ram_id]" value="${ram.id}">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="small text-muted">Price</label>
                                                <input type="number" name="variants[${variantIndex}][price]" step="0.01" min="0"
                                                       class="form-control form-control-sm" placeholder="0.00" value="${existingVariant?.price || ''}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small text-muted">Stock</label>
                                                <input type="number" name="variants[${variantIndex}][stock]" min="0"
                                                       class="form-control form-control-sm" placeholder="0" value="${existingVariant?.stock || ''}" required>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="small text-muted">SKU</label>
                                                <input type="text" name="variants[${variantIndex}][sku]"
                                                       class="form-control form-control-sm" placeholder="SKU-${color.id}-${storage.id}-${ram.id}" value="${existingVariant?.sku || ''}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            variantsContainer.appendChild(variantRow);
                            variantIndex++;
                        });
                    });
                });
            }

            function generateColorImageSections() {
                const selectedColors = getChecked(colorCheckboxes);

                if (!colorImagesContainer) return;

                if (selectedColors.length === 0) {
                    colorImagesContainer.style.display = 'none';
                    colorImagesContainer.innerHTML = '';
                    return;
                }

                colorImagesContainer.style.display = 'block';
                colorImagesContainer.innerHTML = '';

                selectedColors.forEach(color => {
                    const existing = existingColorImages[color.id] || null;

                    const colorSection = document.createElement('div');
                    colorSection.className = 'card card-outline card-info mb-2 p-2';

                    colorSection.innerHTML = `
                        <div class="font-weight-bold mb-2">${color.name} Image</div>
                        <input type="file" name="color_images[${color.id}]" accept="image/*" class="form-control-file">
                        ${existing ? `<small class="text-muted">Current: ${existing.image.split('/').pop()}</small>` : ''}
                    `;

                    colorImagesContainer.appendChild(colorSection);
                });
            }

            colorCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    try {
                        generateColorImageSections();
                        generateVariants();
                    } catch (err) { console.error(err); }
                });
            });

            storageCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    try { generateVariants(); } catch (err) { console.error(err); }
                });
            });

            ramCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    try { generateVariants(); } catch (err) { console.error(err); }
                });
            });

            generateVariants();
            generateColorImageSections();

        } catch (err) {
            showJsError(err);
        }
    });
</script>
@endpush
@endsection