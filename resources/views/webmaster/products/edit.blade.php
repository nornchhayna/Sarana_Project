@extends('layouts.webmaster')

@section('title', isset($product) ? 'Edit Product' : 'Create Product')
@section('page-title', isset($product) ? 'Edit Product' : 'New Product')

@section('content')
<form method="POST"
      action="{{ $product->exists ? route('webmaster.products.update', $product) : route('webmaster.products.store') }}"
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
                <div class="admin-card-header"><i class="fa-solid fa-layer-group" style="color: var(--text-3);"></i> Product Variants</div>
                <div class="admin-card-body">

                    <div class="form-group">
                        <label class="form-label">Available Colors</label>
                        <div class="checkbox-grid">
                            @php $selectedColors = isset($product) ? $product->variants->pluck('color_id')->unique()->toArray() : []; @endphp
                            @foreach($colors as $color)
                            <label class="check-item" for="color_{{ $color->id }}">
                                <input type="checkbox" name="colors[]" value="{{ $color->id }}" id="color_{{ $color->id }}"
                                    {{ in_array($color->id, old('colors', $selectedColors)) ? 'checked' : '' }}>
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

                    <div class="form-group">
                        <label class="form-label">Storage Options</label>
                        <div class="checkbox-grid">
                            @php $selectedStorages = isset($product) ? $product->variants->pluck('phone_storage_id')->unique()->toArray() : []; @endphp
                            @foreach($storages as $storage)
                            <label class="check-item" for="storage_{{ $storage->id }}">
                                <input type="checkbox" name="storages[]" value="{{ $storage->id }}" id="storage_{{ $storage->id }}"
                                    {{ in_array($storage->id, old('storages', $selectedStorages)) ? 'checked' : '' }}>
                                <span class="check-label" style="font-weight: 600;">{{ $storage->capacity }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Price by Storage</label>
                        <p class="text-muted" style="font-size: 13px; margin-bottom: 10px;">
                            Set one price per storage size — it applies to every color at that storage.
                        </p>
                        <div id="storage-prices-container" style="display: none;">
                            <!-- Storage price inputs will be dynamically added here -->
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Variant Details</label>
                        <p class="text-muted" style="font-size: 13px; margin-bottom: 10px;">
                            Stock and SKU for each color + storage combination. Price is set above, per storage.
                        </p>
                        <div id="variants-container" style="display: none;">
                            <!-- Variants will be dynamically added here -->
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label class="form-label">Color Images</label>
                        <p class="text-muted" style="font-size: 13px; margin-bottom: 10px;">
                            Upload 1 image for each color.
                        </p>
                        <div id="color-images-container" style="display: none;">
                            <!-- Color image upload sections will be dynamically added here -->
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
                        <a href="{{ route('webmaster.products.index') }}" class="btn btn-secondary">Cancel</a>
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
                        <label class="form-label">Phone Model</label>
                        <select name="phone_model_id" class="form-select">
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

    // Dynamic Variant Generation
    document.addEventListener('DOMContentLoaded', function() {
        const colorCheckboxes = document.querySelectorAll('input[name="colors[]"]');
        const storageCheckboxes = document.querySelectorAll('input[name="storages[]"]');
        const variantsContainer = document.getElementById('variants-container');
        const storagePricesContainer = document.getElementById('storage-prices-container');

        // Load existing variants
        const existingVariants = @json(isset($product) ? $product->variants->toArray() : []);

        function getSelectedStorages() {
            return Array.from(storageCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => ({
                    id: cb.value,
                    name: cb.closest('.check-item').querySelector('.check-label').textContent.trim()
                }));
        }

        function getSelectedColors() {
            return Array.from(colorCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => ({
                    id: cb.value,
                    name: cb.closest('.check-item').querySelector('.check-label').textContent.trim()
                }));
        }

        // One price input per selected storage size, prefilled from any existing
        // variant at that storage — applies to every color at that storage
        function generateStoragePrices() {
            const selectedStorages = getSelectedStorages();

            if (selectedStorages.length === 0) {
                storagePricesContainer.style.display = 'none';
                storagePricesContainer.innerHTML = '';
                return;
            }

            // Preserve prices already typed in before regenerating
            const typedValues = {};
            storagePricesContainer.querySelectorAll('[data-storage-price]').forEach(input => {
                typedValues[input.dataset.storagePrice] = input.value;
            });

            storagePricesContainer.style.display = 'block';
            storagePricesContainer.innerHTML = '';

            selectedStorages.forEach(storage => {
                const existingForStorage = existingVariants.find(v => v.phone_storage_id == storage.id);
                const prefill = typedValues[storage.id] || existingForStorage?.price || '';

                const row = document.createElement('div');
                row.style.cssText = 'display: flex; align-items: center; gap: 10px; margin-bottom: 8px;';
                row.innerHTML = `
                    <div style="flex: 0 0 100px; font-weight: 600; font-size: 13px;">${storage.name}</div>
                    <div class="input-group" style="max-width: 180px;">
                        <span class="input-text left">$</span>
                        <input type="number" step="0.01" min="0" class="form-control with-left"
                               placeholder="0.00" data-storage-price="${storage.id}" value="${prefill}" required>
                    </div>
                `;
                storagePricesContainer.appendChild(row);
            });

            storagePricesContainer.querySelectorAll('[data-storage-price]').forEach(input => {
                input.addEventListener('input', generateVariants);
            });
        }

        function generateVariants() {
            const selectedColors = getSelectedColors();
            const selectedStorages = getSelectedStorages();

            if (selectedColors.length === 0 || selectedStorages.length === 0) {
                variantsContainer.style.display = 'none';
                variantsContainer.innerHTML = '';
                return;
            }

            variantsContainer.style.display = 'block';
            variantsContainer.innerHTML = '';

            let variantIndex = 0;
            selectedColors.forEach(color => {
                selectedStorages.forEach(storage => {
                    // Find existing variant for this combination (for stock/SKU prefill)
                    const existingVariant = existingVariants.find(v =>
                        v.color_id == color.id && v.phone_storage_id == storage.id
                    );

                    const priceInput = storagePricesContainer.querySelector(`[data-storage-price="${storage.id}"]`);
                    const price = priceInput ? priceInput.value : (existingVariant?.price || '');

                    const variantRow = document.createElement('div');
                    variantRow.className = 'variant-row';
                    variantRow.style.cssText = 'display: flex; gap: 10px; align-items: center; padding: 10px; border: 1px solid var(--border); border-radius: 6px; margin-bottom: 8px; background: var(--surface);';

                    variantRow.innerHTML = `
                        <div style="flex: 0 0 150px; font-weight: 600; font-size: 13px;">
                            ${color.name} + ${storage.name}
                        </div>
                        <div style="flex: 1;">
                            <input type="hidden" name="variants[${variantIndex}][color_id]" value="${color.id}">
                            <input type="hidden" name="variants[${variantIndex}][phone_storage_id]" value="${storage.id}">
                            <input type="hidden" name="variants[${variantIndex}][price]" value="${price}">
                            <div style="display: flex; gap: 8px; align-items: flex-end;">
                                <div style="flex: 0 0 90px; font-size: 12px; color: var(--text-2);">
                                    $${price || '0.00'}
                                </div>
                                <div style="flex: 0 0 80px;">
                                    <label style="font-size: 11px; color: var(--text-2);">Stock</label>
                                    <input type="number" name="variants[${variantIndex}][stock]" min="0"
                                           class="form-control" placeholder="0" value="${existingVariant?.stock || ''}" required>
                                </div>
                                <div style="flex: 1;">
                                    <label style="font-size: 11px; color: var(--text-2);">SKU</label>
                                    <input type="text" name="variants[${variantIndex}][sku]"
                                           class="form-control font-mono" placeholder="SKU-${color.id}-${storage.id}" value="${existingVariant?.sku || ''}">
                                </div>
                            </div>
                        </div>
                    `;

                    variantsContainer.appendChild(variantRow);
                    variantIndex++;
                });
            });
        }

        // Generate color image upload sections (1 image per color)
        function generateColorImageSections() {
            const selectedColors = getSelectedColors();

            const container = document.getElementById('color-images-container');

            if (selectedColors.length === 0) {
                container.style.display = 'none';
                container.innerHTML = '';
                return;
            }

            container.style.display = 'block';
            container.innerHTML = '';

            // Load existing color images (one per color)
            const existingColorImages = @json(isset($product) ? $product->variantImages->keyBy('color_id') : []);

            selectedColors.forEach(color => {
                const existing = existingColorImages[color.id] || null;

                const colorSection = document.createElement('div');
                colorSection.className = 'color-image-section';
                colorSection.style.cssText = 'margin-bottom: 15px; padding: 15px; border: 1px solid var(--border); border-radius: 6px; background: var(--surface);';

                colorSection.innerHTML = `
                    <div style="font-weight: 600; margin-bottom: 10px; color: var(--text-1);">
                        ${color.name} Image
                    </div>
                    <input type="file" name="color_images[${color.id}]" accept="image/*" class="form-control">
                    ${existing ? `<small style="color: var(--text-2); font-size: 10px;">Current: ${existing.image.split('/').pop()}</small>` : ''}
                `;

                container.appendChild(colorSection);
            });
        }

        colorCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                generateColorImageSections();
                generateVariants();
            });
        });

        storageCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                generateStoragePrices();
                generateVariants();
            });
        });

        // Initial generation on page load
        console.log('Running initial generation');
        generateStoragePrices();
        generateVariants();
        generateColorImageSections();
        console.log('Initial generation complete');
    });
</script>
@endpush