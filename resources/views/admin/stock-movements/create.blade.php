@extends('layouts.admin')

@section('title', 'Record Stock Adjustment')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-exchange-alt"></i> Record Stock Adjustment
        </h3>
        <div class="card-tools">
            <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Use this only for manual corrections — damage, recounts, or fixes. Movements from purchase order
            receiving or sales are recorded automatically by those workflows.
        </div>

        <form action="{{ route('admin.stock-movements.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="card card-outline card-info mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Product Selection</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="col-form-label">Product Variant <span class="text-danger">*</span></label>
                                <select name="product_variant_id" id="product_variant_id" class="form-control select2 @error('product_variant_id') is-invalid @enderror">
                                    <option value="">Select variant...</option>
                                    @foreach ($variants as $variant)
                                        <option value="{{ $variant->id }}" data-stock="{{ $variant->stock }}" @selected(old('product_variant_id') == $variant->id)>
                                            {{ $variant->product->name ?? 'Unknown' }}
                                            @if($variant->color) — {{ $variant->color->name }} @endif
                                            @if($variant->phoneStorage) — {{ $variant->phoneStorage->capacity }} @endif
                                            @if($variant->phoneRam) — {{ $variant->phoneRam->capacity }} @endif
                                            (SKU: {{ $variant->sku ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_variant_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div id="current-stock-display" class="alert alert-secondary" style="display: none;">
                                <i class="fas fa-boxes"></i> Current Stock: <strong id="current-stock-value">0</strong>
                            </div>
                        </div>
                    </div>

                    <div class="card card-outline card-warning mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Adjustment Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Adjustment Type <span class="text-danger">*</span></label>
                                        <div class="btn-group w-100" data-toggle="buttons">
                                            <label class="btn btn-outline-success @if(old('type') === 'adjustment_in') active @endif">
                                                <input type="radio" name="type" value="adjustment_in" @checked(old('type') === 'adjustment_in')>
                                                <i class="fas fa-plus-circle"></i> Stock In
                                            </label>
                                            <label class="btn btn-outline-danger @if(old('type') === 'adjustment_out') active @endif">
                                                <input type="radio" name="type" value="adjustment_out" @checked(old('type') === 'adjustment_out')>
                                                <i class="fas fa-minus-circle"></i> Stock Out
                                            </label>
                                            <label class="btn btn-outline-warning @if(old('type') === 'damage') active @endif">
                                                <input type="radio" name="type" value="damage" @checked(old('type') === 'damage')>
                                                <i class="fas fa-exclamation-triangle"></i> Damaged
                                            </label>
                                        </div>
                                        @error('type')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" min="1" name="quantity" id="quantity"
                                               value="{{ old('quantity') }}"
                                               class="form-control @error('quantity') is-invalid @enderror"
                                               placeholder="Enter quantity">
                                        @error('quantity')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-form-label">Reason <span class="text-danger">*</span></label>
                                <textarea name="notes" id="notes" rows="3"
                                          placeholder="e.g. Damaged in warehouse, stock recount correction, found missing items..."
                                          class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Common adjustment reasons:</p>
                            <div class="list-group">
                                <button type="button" class="list-group-item list-group-item-action" onclick="setReason('Damaged in warehouse')">
                                    <i class="fas fa-exclamation-triangle text-danger"></i> Damaged
                                </button>
                                <button type="button" class="list-group-item list-group-item-action" onclick="setReason('Stock recount correction')">
                                    <i class="fas fa-calculator text-info"></i> Recount
                                </button>
                                <button type="button" class="list-group-item list-group-item-action" onclick="setReason('Found missing items')">
                                    <i class="fas fa-search text-success"></i> Found Missing
                                </button>
                                <button type="button" class="list-group-item list-group-item-action" onclick="setReason('Theft/Loss')">
                                    <i class="fas fa-user-secret text-warning"></i> Theft/Loss
                                </button>
                                <button type="button" class="list-group-item list-group-item-action" onclick="setReason('Quality control failure')">
                                    <i class="fas fa-times-circle text-danger"></i> QC Failure
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Record Adjustment
                </button>
                <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setReason(reason) {
        document.getElementById('notes').value = reason;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const variantSelect = document.getElementById('product_variant_id');
        const stockDisplay = document.getElementById('current-stock-display');
        const stockValue = document.getElementById('current-stock-value');

        if (variantSelect) {
            variantSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const stock = selectedOption.getAttribute('data-stock');

                if (stock !== null && this.value !== '') {
                    stockValue.textContent = stock;
                    stockDisplay.style.display = 'block';
                } else {
                    stockDisplay.style.display = 'none';
                }
            });

            // Trigger on load if value is pre-selected
            if (variantSelect.value !== '') {
                variantSelect.dispatchEvent(new Event('change'));
            }
        }
    });
</script>
@endpush
