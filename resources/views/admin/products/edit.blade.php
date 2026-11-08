@extends('layouts.admin')

@section('title', isset($product) ? 'Edit Product' : 'Create Product')
@section('page-title', isset($product) ? 'Edit Product' : 'New Product')

@section('content')
<form method="POST"
      action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if($product->exists) @method('PUT') @endif

    <div class="row">

        {{-- ── LEFT COLUMN ── --}}
        <div class="col-md-8">

            {{-- Basic Info --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-circle-info mr-1"></i> Basic Information</h3>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label>Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-control" placeholder="e.g. Apple iPhone 15 Pro Max" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="form-control text-monospace" placeholder="APP-IP15-PM" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $product->slug ?? '') }}" class="form-control text-monospace" placeholder="Auto-generated if blank">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Detailed product description...">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label>Specifications (JSON or text)</label>
                        <textarea name="specifications" class="form-control text-monospace" rows="4" placeholder='{"Screen": "6.7 inch", "Camera": "48MP"}'>{{ old('specifications', $product->specifications ?? '') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- Pricing --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tag mr-1"></i> Pricing & Stock</h3>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-3 form-group">
                            <label>Base Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" name="base_price" value="{{ old('base_price', $product->base_price ?? '') }}" step="0.01" min="0" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-3 form-group">
                            <label>Sale Price</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}" step="0.01" min="0" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3 form-group mb-0">
                            <label>Discount %</label>
                            <div class="input-group">
                                <input type="number" name="discount" value="{{ old('discount', $product->discount ?? 0) }}" step="0.01" min="0" max="100" class="form-control">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                        </div>

                        <div class="col-md-3 form-group mb-0">
                            <label>Stock <small class="text-muted">(Single product)</small></label>
                            <div class="input-group">
                                <input type="number" name="stock" value="{{ old('stock', $product->total_stock ?? 0) }}" min="0" class="form-control" placeholder="0">
                                <div class="input-group-append"><span class="input-group-text"><i class="fas fa-boxes"></i></span></div>
                            </div>
                            <small class="text-muted">Used if no variants are configured</small>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Variants --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-layer-group mr-1"></i> Product Variants</h3>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label>Available Colors</label>
                        <div class="d-flex flex-wrap" style="gap: 10px;">
                            @php $selectedColors = isset($product) ? $product->productColors->pluck('color_id')->unique()->toArray() : []; @endphp
                            @foreach($colors as $color)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="colors[]" value="{{ $color->id }}" id="color_{{ $color->id }}"
                                    {{ in_array($color->id, old('colors', $selectedColors)) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="color_{{ $color->id }}">
                                    @if($color->hex_code)
                                        <span class="d-inline-block" style="width:14px;height:14px;border-radius:3px;background:{{ $color->hex_code }};vertical-align:middle;margin-right:4px;"></span>
                                    @endif
                                    {{ $color->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Storage Options</label>
                        <div class="d-flex flex-wrap" style="gap: 10px;">
                            @php $selectedStorages = isset($product) ? $product->productStorages->pluck('phone_storage_id')->unique()->toArray() : []; @endphp
                            @foreach($storages as $storage)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="storages[]" value="{{ $storage->id }}" id="storage_{{ $storage->id }}"
                                    {{ in_array($storage->id, old('storages', $selectedStorages)) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="storage_{{ $storage->id }}">{{ $storage->capacity }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label>RAM Options</label>
                        <div class="d-flex flex-wrap" style="gap: 10px;">
                            @php $selectedRams = isset($product) ? $product->variants->pluck('phone_ram_id')->unique()->filter()->toArray() : []; @endphp
                            @foreach($rams as $ram)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="rams[]" value="{{ $ram->id }}" id="ram_{{ $ram->id }}"
                                    {{ in_array($ram->id, old('rams', $selectedRams)) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="ram_{{ $ram->id }}">{{ $ram->capacity }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label>Variant Details</label>
                        <p class="text-muted small mb-2">
                            Set price, stock, and SKU for each Color + Storage + RAM combination.
                        </p>
                        <div id="variants-container" style="display: none;">
                            <!-- Variants will be dynamically added here -->
                        </div>
                    </div>

                    <div class="form-group mt-4 mb-0">
                        <label>Color Images</label>
                        <p class="text-muted small mb-2">
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
        <div class="col-md-4">

            {{-- Publish Settings --}}
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-rocket mr-1 text-success"></i> Publish</h3>
                </div>
                <div class="card-body">

                    @foreach([
                        ['is_active',   'Active (Visible on store)'],
                        ['is_featured', 'Featured Product'],
                        ['is_new',      'Mark as New Arrival'],
                        ['is_pre_order','Allow Pre-orders'],
                    ] as [$name, $label])
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ $label }}</span>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="switch_{{ $name }}" name="{{ $name }}" value="1"
                                {{ old($name, ($product->$name ?? false) ? '1' : '') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="switch_{{ $name }}"></label>
                        </div>
                    </div>
                    @endforeach

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-floppy-disk mr-1"></i> {{ isset($product) ? 'Update Product' : 'Save Product' }}
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-default btn-block">Cancel</a>
                </div>
            </div>

            {{-- Organization --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-folder-tree mr-1"></i> Organization</h3>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label>Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-control select2" style="width: 100%;" required>
                            <option value="">Select Category…</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Brand <span class="text-danger">*</span></label>
                        <select name="brand_id" class="form-control select2" style="width: 100%;" required>
                            <option value="">Select Brand…</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Phone Model <span class="text-muted">(Optional)</span></label>
                        <select name="phone_model_id" class="form-control select2" style="width: 100%;">
                            <option value="">Select Model…</option>
                            @foreach($phoneModels as $model)
                                <option value="{{ $model->id }}" @selected(old('phone_model_id', $product->phone_model_id ?? '') == $model->id)>{{ $model->brand->name }} {{ $model->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" class="form-control text-monospace" placeholder="0">
                    </div>

                </div>
            </div>

            {{-- Media --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-images mr-1"></i> Media</h3>
                </div>
                <div class="card-body">

                    <label>Main Thumbnail</label>
                    <div class="text-center border rounded mb-2 p-3" id="thumbnail-preview-container">
                        @if(isset($product) && $product->thumbnail)
                            <img id="thumbnail-preview" src="{{ asset('storage/'.$product->thumbnail) }}" alt="Thumbnail" class="img-fluid" style="max-height: 160px;">
                        @else
                            <i id="thumbnail-icon" class="fas fa-image fa-3x text-muted"></i>
                            <img id="thumbnail-preview" src="" alt="Thumbnail" class="img-fluid" style="display: none; max-height: 160px;">
                        @endif
                    </div>
                    <div class="custom-file mb-3">
                        <input type="file" name="thumbnail" id="thumbnail-input" class="custom-file-input" accept="image/*">
                        <label class="custom-file-label" for="thumbnail-input">Choose file</label>
                    </div>

                    <div>
                        <label>Gallery Images</label>
                        <div class="custom-file">
                            <input type="file" name="images[]" class="custom-file-input" accept="image/*" multiple id="gallery-input">
                            <label class="custom-file-label" for="gallery-input">Choose files</label>
                        </div>
                        <small class="form-text text-muted">Hold CTRL/CMD to select multiple files.</small>
                    </div>

                </div>
            </div>

            {{-- Free Gifts --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-gift mr-1"></i> Free Gifts Included</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Select free items that customers will receive when purchasing this product.
                    </p>
                    @if($gifts->isNotEmpty())
                        @php $selectedGifts = isset($product) ? $product->gifts->pluck('id')->toArray() : []; @endphp
                        @foreach($gifts as $gift)
                            @php $attached = $product->gifts->firstWhere('id', $gift->id) ?? null; @endphp
                            <div class="d-flex align-items-center border rounded p-2 mb-2">
                                <div class="custom-control custom-checkbox mr-2">
                                    <input type="checkbox" class="custom-control-input" name="gifts[]" value="{{ $gift->id }}" id="gift_{{ $gift->id }}"
                                        {{ in_array($gift->id, old('gifts', $selectedGifts)) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="gift_{{ $gift->id }}"></label>
                                </div>
                                <label for="gift_{{ $gift->id }}" class="flex-grow-1 small mb-0" style="cursor: pointer;">
                                    {{ $gift->name }}
                                </label>
                                <input type="number" name="gift_quantity[{{ $gift->id }}]" min="1"
                                       value="{{ $attached?->pivot->quantity ?? 1 }}"
                                       class="form-control form-control-sm" style="width: 70px;" placeholder="Qty">
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted small">
                            No active gifts available. <a href="{{ route('admin.gifts.create') }}">Create one</a>.
                        </div>
                    @endif
                </div>
            </div>

            {{-- RAM Options --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-microchip mr-1"></i> RAM Options</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Select RAM options available for this product.
                    </p>
                    @if($rams->isNotEmpty())
                        @php $selectedRams = isset($product) ? $product->rams->pluck('id')->toArray() : []; @endphp
                        @foreach($rams as $ram)
                            @php $attached = $product->rams->firstWhere('id', $ram->id) ?? null; @endphp
                            <div class="d-flex align-items-center border rounded p-2 mb-2">
                                <div class="custom-control custom-checkbox mr-2">
                                    <input type="checkbox" class="custom-control-input" name="product_rams[]" value="{{ $ram->id }}" id="product_ram_{{ $ram->id }}"
                                        {{ in_array($ram->id, old('product_rams', $selectedRams)) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="product_ram_{{ $ram->id }}"></label>
                                </div>
                                <label for="product_ram_{{ $ram->id }}" class="flex-grow-1 small mb-0" style="cursor: pointer;">
                                    {{ $ram->capacity }}
                                    @if($ram->label) <span class="text-muted">({{ $ram->label }})</span> @endif
                                </label>
                                <input type="number" name="ram_price[{{ $ram->id }}]" step="0.01"
                                       value="{{ $attached?->pivot->price ?? 0 }}"
                                       class="form-control form-control-sm" style="width: 80px;" placeholder="Price">
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted small">
                            No active RAM options available. <a href="{{ route('admin.rams.create') }}">Create one</a>.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function () {
        // AdminLTE/Bootstrap select2 init
        $('.select2').select2();

        // Show chosen filename on custom file inputs
        $('.custom-file-input').on('change', function () {
            const fileNames = Array.from(this.files).map(f => f.name).join(', ');
            $(this).next('.custom-file-label').html(fileNames || 'Choose file');
        });
    });

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
        const ramCheckboxes = document.querySelectorAll('input[name="rams[]"]');
        const variantsContainer = document.getElementById('variants-container');

        // Load existing variants or old input from validation
        const existingVariants = @json(old('variants', isset($product) ? $product->variants->toArray() : []));

        function getSelectedStorages() {
            return Array.from(storageCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => ({
                    id: cb.value,
                    name: cb.closest('.custom-control').querySelector('.custom-control-label').textContent.trim()
                }));
        }

        function getSelectedColors() {
            return Array.from(colorCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => ({
                    id: cb.value,
                    name: cb.closest('.custom-control').querySelector('.custom-control-label').textContent.trim()
                }));
        }

        function getSelectedRams() {
            return Array.from(ramCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => ({
                    id: cb.value,
                    name: cb.closest('.custom-control').querySelector('.custom-control-label').textContent.trim()
                }));
        }

        function generateVariants() {
            const selectedColors = getSelectedColors();
            const selectedStorages = getSelectedStorages();
            const selectedRams = getSelectedRams();

            if (selectedColors.length === 0 || selectedStorages.length === 0) {
                variantsContainer.style.display = 'none';
                variantsContainer.innerHTML = '';
                return;
            }

            // If no RAMs selected, use a single null RAM option
            const ramsToUse = selectedRams.length > 0 ? selectedRams : [{ id: '', name: 'No RAM' }];

            variantsContainer.style.display = 'block';
            variantsContainer.innerHTML = '';

            let variantIndex = 0;
            selectedColors.forEach(color => {
                selectedStorages.forEach(storage => {
                    ramsToUse.forEach(ram => {
                        // Find existing variant for this combination (for stock/SKU/price prefill)
                        const existingVariant = existingVariants.find(v =>
                            String(v.color_id) === String(color.id) &&
                            String(v.phone_storage_id) === String(storage.id) &&
                            (v.phone_ram_id === null || v.phone_ram_id === '' ? (ram.id === '' || ram.id === null) : String(v.phone_ram_id) === String(ram.id))
                        );

                        const variantRow = document.createElement('div');
                        variantRow.className = 'border rounded p-2 mb-2 d-flex align-items-center';
                        variantRow.style.gap = '10px';

                        const variantLabel = ram.id ? `${color.name} + ${storage.name} + ${ram.name}` : `${color.name} + ${storage.name}`;

                        variantRow.innerHTML = `
                            <div style="flex: 0 0 180px;" class="font-weight-bold small">
                                ${variantLabel}
                            </div>
                            <div class="flex-grow-1">
                                <input type="hidden" name="variants[${variantIndex}][color_id]" value="${color.id}">
                                <input type="hidden" name="variants[${variantIndex}][phone_storage_id]" value="${storage.id}">
                                <input type="hidden" name="variants[${variantIndex}][phone_ram_id]" value="${ram.id}">
                                <div class="form-row">
                                    <div class="col-3">
                                        <label class="small text-muted mb-0">Price</label>
                                        <input type="number" name="variants[${variantIndex}][price]" step="0.01" min="0"
                                               class="form-control form-control-sm" placeholder="0.00" value="${existingVariant?.price || ''}" required>
                                    </div>
                                    <div class="col-3">
                                        <label class="small text-muted mb-0">Stock</label>
                                        <input type="number" name="variants[${variantIndex}][stock]" min="0"
                                               class="form-control form-control-sm" placeholder="0" value="${existingVariant?.stock || ''}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted mb-0">SKU</label>
                                        <input type="text" name="variants[${variantIndex}][sku]"
                                               class="form-control form-control-sm text-monospace" placeholder="SKU-${color.id}-${storage.id}-${ram.id}" value="${existingVariant?.sku || ''}">
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

            // Load existing color images or old input from validation
            const existingColorImages = @json(old('color_images', isset($product) ? $product->variantImages->keyBy('color_id') : []));

            selectedColors.forEach(color => {
                const existing = existingColorImages[color.id] || null;

                const colorSection = document.createElement('div');
                colorSection.className = 'border rounded p-3 mb-2';

                colorSection.innerHTML = `
                    <div class="font-weight-bold mb-2">
                        ${color.name} Image
                    </div>
                    <div class="custom-file">
                        <input type="file" name="color_images[${color.id}]" accept="image/*" class="custom-file-input" id="color_img_${color.id}">
                        <label class="custom-file-label" for="color_img_${color.id}">Choose file</label>
                    </div>
                    ${existing ? `<small class="text-muted">Current: ${existing.image.split('/').pop()}</small>` : ''}
                `;

                container.appendChild(colorSection);
            });

            // Re-bind custom-file-label update for newly added inputs
            $(container).find('.custom-file-input').on('change', function () {
                const fileNames = Array.from(this.files).map(f => f.name).join(', ');
                $(this).next('.custom-file-label').html(fileNames || 'Choose file');
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
                generateVariants();
            });
        });

        ramCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                generateVariants();
            });
        });

        // Initial generation on page load
        // Use setTimeout to ensure DOM is fully rendered and checkboxes are in checked state
        setTimeout(() => {
            generateVariants();
            generateColorImageSections();
        }, 50);
    });
</script>
@endpush