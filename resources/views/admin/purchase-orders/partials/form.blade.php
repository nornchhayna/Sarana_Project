@php
    $isEdit = isset($purchaseOrder) && $purchaseOrder->exists;
    $existingItems = $isEdit ? $purchaseOrder->items : collect();
@endphp

<div class="card card-primary card-outline">
    <div class="card-body">
        @if ($isEdit)
            <div class="row mb-3">
                <div class="form-group col-md-12">
                    <label>Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="draft" @selected(old('status', $purchaseOrder->status) === 'draft')">Draft</option>
                        <option value="ordered" @selected(old('status', $purchaseOrder->status) === 'ordered')">Ordered</option>
                        <option value="partially_received" @selected(old('status', $purchaseOrder->status) === 'partially_received')">Partially Received</option>
                        <option value="received" @selected(old('status', $purchaseOrder->status) === 'received')">Received</option>
                        <option value="cancelled" @selected(old('status', $purchaseOrder->status) === 'cancelled')">Cancelled</option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="text-muted">
                        @if($purchaseOrder->status === 'draft')
                            Submit this purchase order to enable stock receiving.
                        @elseif($purchaseOrder->status === 'ordered')
                            Ready to receive stock.
                        @elseif($purchaseOrder->status === 'partially_received')
                            Partial stock received, more can be received.
                        @elseif($purchaseOrder->status === 'received')
                            All stock received.
                        @elseif($purchaseOrder->status === 'cancelled')
                            This purchase order has been cancelled.
                        @endif
                    </small>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="form-group col-md-4">
                <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" id="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror">
                    <option value="">Select supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id', $purchaseOrder->supplier_id ?? null) == $supplier->id)>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label for="order_date">Order Date <span class="text-danger">*</span></label>
                <input type="date" name="order_date" id="order_date"
                       value="{{ old('order_date', optional($purchaseOrder->order_date ?? null)->format('Y-m-d')) }}"
                       class="form-control @error('order_date') is-invalid @enderror">
                @error('order_date')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label for="expected_date">Expected Date</label>
                <input type="date" name="expected_date" id="expected_date"
                       value="{{ old('expected_date', optional($purchaseOrder->expected_date ?? null)->format('Y-m-d')) }}"
                       class="form-control @error('expected_date') is-invalid @enderror">
                @error('expected_date')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-4">
                <label for="currency">Currency <span class="text-danger">*</span></label>
                <input type="text" name="currency" id="currency" maxlength="3"
                       value="{{ old('currency', $purchaseOrder->currency ?? 'USD') }}"
                       class="form-control @error('currency') is-invalid @enderror" style="text-transform: uppercase;">
                @error('currency')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label for="shipping_cost">Shipping Cost</label>
                <input type="number" step="0.01" min="0" name="shipping_cost" id="shipping_cost"
                       value="{{ old('shipping_cost', $purchaseOrder->shipping_cost ?? 0) }}"
                       class="form-control @error('shipping_cost') is-invalid @enderror">
                @error('shipping_cost')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            @unless ($isEdit)
                <div class="form-group col-md-4">
                    <label for="po_number">PO Number</label>
                    <input type="text" name="po_number" id="po_number"
                           value="{{ old('po_number') }}" placeholder="Auto-generated if left blank"
                           class="form-control @error('po_number') is-invalid @enderror">
                    @error('po_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            @endunless
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $purchaseOrder->notes ?? '') }}</textarea>
            @error('notes')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="card card-outline card-secondary">
    <div class="card-header">
        <h3 class="card-title">Line Items</h3>
        <div class="card-tools">
            <button type="button" id="add-item-row" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Item
            </button>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table" id="items-table">
            <thead>
                <tr>
                    <th style="min-width: 220px;">Product <span class="text-danger">*</span></th>
                    <th style="min-width: 200px;">Variant</th>
                    <th style="width: 120px;">Qty Ordered <span class="text-danger">*</span></th>
                    <th style="width: 140px;">Unit Cost <span class="text-danger">*</span></th>
                    <th style="width: 40px;"></th>
                </tr>
            </thead>
            <tbody id="items-body">
                @php $rows = $existingItems->isNotEmpty() ? $existingItems : [null]; @endphp
                @foreach ($rows as $index => $item)
                    <tr class="item-row">
                        <td>
                            <select name="items[{{ $index }}][product_id]" class="form-control form-control-sm product-select" required>
                                <option value="">Select product</option>
                                @if (isset($products))
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" @selected(old("items.$index.product_id", $item->product_id ?? null) == $product->id)>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td>
                            @if(isset($item) && $item->product_variant_id)
                                <input type="hidden" name="items[{{ $index }}][product_variant_id]" value="{{ $item->product_variant_id }}">
                                @php
                                    $currentVariant = $item->variant;
                                    $colorName = $currentVariant ? ($currentVariant->color->name ?? 'No Color') : 'No Color';
                                    $storageLabel = $currentVariant ? ($currentVariant->phoneStorage ? ($currentVariant->phoneStorage->label ?? $currentVariant->phoneStorage->capacity) : ($currentVariant->phone_storage_id ? 'Storage #' . $currentVariant->phone_storage_id : 'No Storage')) : 'No Storage';
                                    $ramLabel = $currentVariant ? ($currentVariant->phoneRam ? ($currentVariant->phoneRam->label ?? $currentVariant->phoneRam->capacity) : ($currentVariant->phone_ram_id ? 'RAM #' . $currentVariant->phone_ram_id : 'No RAM')) : 'No RAM';
                                @endphp
                                <span class="form-control form-control-sm" style="background-color: #f8f9fa;">
                                    {{ $colorName }} / {{ $storageLabel }} / {{ $ramLabel }}
                                </span>
                            @else
                                <select name="items[{{ $index }}][product_variant_id]" class="form-control form-control-sm variant-select">
                                    <option value="">Select product first</option>
                                    @if (isset($item) && $item->product_id && isset($products))
                                        @php
                                            $selectedProduct = $products->firstWhere('id', $item->product_id);
                                            $variants = $selectedProduct ? $selectedProduct->variants : collect();
                                            $currentVariantId = old("items.$index.product_variant_id", $item->product_variant_id ?? null);
                                        @endphp
                                        @if ($variants->isNotEmpty())
                                            @foreach ($variants as $variant)
                                                @php
                                                    $colorName = $variant->color->name ?? 'No Color';
                                                    $storageLabel = $variant->phoneStorage ? ($variant->phoneStorage->label ?? $variant->phoneStorage->capacity) : ($variant->phone_storage_id ? 'Storage #' . $variant->phone_storage_id : 'No Storage');
                                                    $ramLabel = $variant->phoneRam ? ($variant->phoneRam->label ?? $variant->phoneRam->capacity) : ($variant->phone_ram_id ? 'RAM #' . $variant->phone_ram_id : 'No RAM');
                                                @endphp
                                                <option value="{{ $variant->id }}" @if($currentVariantId == $variant->id) selected @endif>
                                                    {{ $colorName }} / {{ $storageLabel }} / {{ $ramLabel }}
                                                </option>
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                            @endif
                        </td>
                        <td>
                            <input type="number" min="1" name="items[{{ $index }}][quantity_ordered]"
                                   value="{{ old("items.$index.quantity_ordered", $item->quantity_ordered ?? '') }}"
                                   class="form-control form-control-sm" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_cost]"
                                   value="{{ old("items.$index.unit_cost", $item->unit_cost ?? '') }}"
                                   class="form-control form-control-sm" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-item-row">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mb-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ $isEdit ? 'Update' : 'Create' }} Purchase Order
    </button>
    <a href="{{ $isEdit ? route('admin.purchase-orders.show', $purchaseOrder) : route('admin.purchase-orders.index') }}" class="btn btn-default">
        Cancel
    </a>
</div>

@push('scripts')
<script>
    (function () {
        let rowIndex = {{ max(count($rows) - 1, 0) }};

        const products = @json(isset($products) ? $products : []);

        const emptyRow = () => `
            <tr class="item-row">
                <td>
                    <select name="items[${++rowIndex}][product_id]" class="form-control form-control-sm product-select" required>
                        <option value="">Select product</option>
                        ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select name="items[${rowIndex}][product_variant_id]" class="form-control form-control-sm variant-select">
                        <option value="">—</option>
                    </select>
                </td>
                <td>
                    <input type="number" min="1" name="items[${rowIndex}][quantity_ordered]" class="form-control form-control-sm" required>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_cost]" class="form-control form-control-sm" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-item-row">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;

        document.getElementById('add-item-row').addEventListener('click', function () {
            document.getElementById('items-body').insertAdjacentHTML('beforeend', emptyRow());
        });

        document.getElementById('items-body').addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-item-row');
            if (!btn) return;

            const rows = document.querySelectorAll('#items-body .item-row');
            if (rows.length > 1) {
                btn.closest('.item-row').remove();
            } else {
                alert('A purchase order needs at least one item.');
            }
        });

        /**
         * Populate the variant <select> for a given row, refreshing
         * Select2 afterwards if it's been applied to that element.
         */
        function setVariantOptions(variantSelect, variants) {
            variantSelect.innerHTML = '<option value="">Select variant</option>';

            if (variants.length > 0) {
                variants.forEach(variant => {
                    const option = document.createElement('option');
                    option.value = variant.id;
                    const color = variant.color_name || 'No Color';
                    const storage = variant.storage_label || 'No Storage';
                    const ram = variant.ram_label || 'No RAM';
                    const label = `${color} / ${storage} / ${ram}`;
                    option.textContent = label;
                    variantSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No variants available';
                variantSelect.appendChild(option);
            }

            // If Select2 (or a similar plugin) is attached to this element,
            // appending native <option> tags alone won't refresh its UI.
            if (window.jQuery && jQuery(variantSelect).data('select2')) {
                jQuery(variantSelect).trigger('change');
            }
        }

        // Handle product selection to load variants via AJAX
        document.getElementById('items-body').addEventListener('change', function (e) {
            if (e.target.classList.contains('product-select')) {
                const productSelect = e.target;
                const row = productSelect.closest('.item-row');
                const variantSelect = row.querySelector('.variant-select');
                const productId = parseInt(productSelect.value);

                variantSelect.innerHTML = '<option value="">Loading…</option>';

                if (!productId) {
                    variantSelect.innerHTML = '<option value="">Select product first</option>';
                    return;
                }

                const url = `/admin/products/${productId}/variants`;

                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(payload => {
                        // Accepts either a plain array or a Laravel Resource-wrapped { data: [...] }
                        const variants = Array.isArray(payload) ? payload : (payload.data ?? []);
                        setVariantOptions(variantSelect, variants);
                    })
                    .catch(error => {
                        console.error('Error fetching variants:', error);
                        variantSelect.innerHTML = '<option value="">Error loading variants</option>';
                    });
            }
        });
    })();
</script>
@endpush