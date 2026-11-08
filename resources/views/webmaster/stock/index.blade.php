@extends('layouts.webmaster')

@section('title', 'Stock Control')
@section('page-title', 'Inventory Stock')

@section('content')

{{-- =========================================================
     INVENTORY SUMMARY
========================================================= --}}

<div class="row mb-3">

    <div class="col-md-4">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted text-uppercase small">
                        Total Hand Quantity
                    </div>

                    <h3 class="mb-0">
                        {{ number_format($summary['total_items']) }}
                    </h3>
                </div>

                <div class="text-primary">
                    <i class="fas fa-warehouse fa-2x"></i>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-4">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted text-uppercase small">
                        Low Stock SKUs
                    </div>

                    <h3 class="mb-0">
                        {{ $summary['low_stock'] }}
                    </h3>
                </div>

                <div class="text-warning">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-4">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted text-uppercase small">
                        Out Of Stock
                    </div>

                    <h3 class="mb-0 text-danger">
                        {{ $summary['out_of_stock'] }}
                    </h3>
                </div>

                <div class="text-danger">
                    <i class="fas fa-times-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

</div>


{{-- =========================================================
     FILTER TOOLBAR
========================================================= --}}

<div class="card">

    <div class="card-header">

        <h3 class="card-title">
            <i class="fas fa-filter mr-2"></i>
            Inventory Filters
        </h3>

    </div>

    <div class="card-body">

        <form method="GET">

            <div class="row align-items-end">

                {{-- Search --}}
                <div class="col-md-3">

                    <div class="form-group mb-md-0">

                        <label>
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Search product name, SKU..."
                        >

                    </div>

                </div>


                {{-- Stock Status --}}
                <div class="col-md-2">

                    <div class="form-group mb-md-0">

                        <label>
                            Stock Level
                        </label>

                        <select
                            name="stock_status"
                            class="form-control">

                            <option value="">
                                All Stock Levels
                            </option>

                            <option value="ok"
                                @selected(request('stock_status') === 'ok')}>
                                Stock OK
                            </option>

                            <option value="low"
                                @selected(request('stock_status') === 'low')}>
                                Low Stock
                            </option>

                            <option value="out"
                                @selected(request('stock_status') === 'out')}>
                                Out of Stock
                            </option>

                        </select>

                    </div>

                </div>


                {{-- Filter --}}
                <div class="col-md-2">

                    <div class="form-group mb-md-0">

                        <button
                            type="submit"
                            class="btn btn-secondary">

                            <i class="fas fa-filter mr-1"></i>
                            Filter

                        </button>

                    </div>

                </div>


                {{-- Bulk Update --}}
                <div class="col-md-2 text-md-right">

                    <button
                        type="button"
                        onclick="document.getElementById('bulk-stock-form').submit();"
                        class="btn btn-primary">

                        <i class="fas fa-cloud-upload-alt mr-1"></i>
                        Save Bulk Quantities

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     MAIN STOCK TABLE
========================================================= --}}

<form
    id="bulk-stock-form"
    method="POST"
    action="{{ route('webmaster.stock.bulk-update') }}">

    @csrf
    @method('PUT')

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                <i class="fas fa-boxes mr-2"></i>
                Inventory Stock
            </h3>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead>

                        <tr>

                            <th style="width: 40%;">
                                Product
                            </th>

                            <th style="width: 15%;">
                                Total Stock
                            </th>

                            <th style="width: 10%;">
                                Variants
                            </th>

                            <th style="width: 10%;">
                                Status
                            </th>

                            <th class="text-right" style="width: 25%;">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($groupedStocksPaginated as $group)

                            @php
                                $product = $group['product'];
                                $variants = $group['variants'];
                                $totalStock = $group['total_stock'];
                                $variantCount = $group['variant_count'];
                            @endphp

                            {{-- Product Row --}}
                            <tr data-product-id="{{ $product->id }}" style="cursor: pointer;" onclick="toggleVariants({{ $product->id }})">

                                <td>
                                    <div class="d-flex align-items-center">
                                        <i id="toggle-icon-{{ $product->id }}" class="fas fa-chevron-right mr-2 text-muted"></i>
                                        <div>
                                            <strong>{{ $product->name }}</strong>
                                            <br>
                                            <small class="text-muted">SKU: {{ $product->sku ?? '—' }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge {{ $totalStock == 0 ? 'badge-danger' : ($totalStock <= 10 ? 'badge-warning' : 'badge-success') }}">
                                        {{ number_format($totalStock) }}
                                    </span>
                                </td>

                                <td>
                                    @if($variantCount > 0)
                                        <span class="badge badge-info">{{ $variantCount }} variant{{ $variantCount > 1 ? 's' : '' }}</span>
                                    @else
                                        <span class="badge badge-secondary"><i class="fas fa-box mr-1"></i> Single Item</span>
                                    @endif
                                </td>

                                <td>
                                    @if($totalStock == 0)
                                        <span class="badge badge-danger">Out of Stock</span>
                                    @elseif($totalStock <= 10)
                                        <span class="badge badge-warning">Low Stock</span>
                                    @else
                                        <span class="badge badge-success">In Stock</span>
                                    @endif
                                </td>

                                <td class="text-right">
                                    @if($variantCount == 0)
                                        <a href="{{ route('webmaster.stock.edit', ['stock' => $product->id, 'type' => 'product']) }}" class="btn btn-sm btn-info mr-1" title="Adjust Stock">
                                            <i class="fas fa-pen-to-square"></i> Edit
                                        </a>
                                    @endif
                                    <a href="{{ route('webmaster.products.show', $product) }}" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>

                            </tr>

                            {{-- Variants Row (Collapsible) --}}
                            <tr id="variants-{{ $product->id }}" style="display: none;">

                                <td colspan="5" class="p-0">

                                    <table class="table table-sm mb-0">

                                        <thead>

                                            <tr>

                                                <th>Variant</th>

                                                <th>Stock</th>

                                                <th>SKU</th>

                                                <th>Actions</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            @if($variantCount > 0)
                                                @foreach($variants as $variant)

                                                    <tr>

                                                        <td>
                                                            <small>
                                                                @if($variant->color)
                                                                    <span class="badge badge-light">{{ $variant->color->name }}</span>
                                                                @endif
                                                                @if($variant->phoneStorage)
                                                                    <span class="badge badge-light">{{ $variant->phoneStorage->capacity }}</span>
                                                                @endif
                                                                @if($variant->phoneRam)
                                                                    <span class="badge badge-light">{{ $variant->phoneRam->capacity }}</span>
                                                                @endif
                                                            </small>
                                                        </td>

                                                        <td>
                                                            <input
                                                                type="number"
                                                                name="stocks[{{ $variant->id }}][stock]"
                                                                value="{{ $variant->stock }}"
                                                                class="form-control form-control-sm"
                                                                min="0"
                                                                style="width: 80px;"
                                                            >
                                                        </td>

                                                        <td>
                                                            <input
                                                                type="text"
                                                                name="stocks[{{ $variant->id }}][sku]"
                                                                value="{{ $variant->sku }}"
                                                                class="form-control form-control-sm"
                                                                style="width: 120px;"
                                                                placeholder="SKU"
                                                            >
                                                        </td>

                                                        <td>
                                                            <a href="{{ route('webmaster.stock.edit', $variant) }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-pen-to-square"></i>
                                                            </a>
                                                        </td>

                                                    </tr>

                                                @endforeach
                                            @else
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-light"><i class="fas fa-box text-muted mr-1"></i> Standard Item</span>
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="number"
                                                            name="product_stocks[{{ $product->id }}][stock]"
                                                            value="{{ $totalStock }}"
                                                            class="form-control form-control-sm"
                                                            min="0"
                                                            style="width: 80px;"
                                                        >
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            name="product_stocks[{{ $product->id }}][sku]"
                                                            value="{{ $product->sku }}"
                                                            class="form-control form-control-sm"
                                                            style="width: 120px;"
                                                            placeholder="SKU"
                                                        >
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('webmaster.stock.edit', ['stock' => $product->id, 'type' => 'product']) }}" class="btn btn-sm btn-info" title="Adjust Stock">
                                                            <i class="fas fa-pen-to-square"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif

                                        </tbody>

                                    </table>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="text-center text-muted py-5">

                                    No inventory found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if($groupedStocksPaginated->hasPages())

            <div class="card-footer">

                {{ $groupedStocksPaginated->links() }}

            </div>

        @endif

    </div>

</form>

@push('scripts')
<script>
    function toggleVariants(productId) {
        const variantsRow = document.getElementById('variants-' + productId);
        const toggleIcon = document.getElementById('toggle-icon-' + productId);

        if (variantsRow.style.display === 'none') {
            variantsRow.style.display = 'table-row';
            toggleIcon.classList.remove('fa-chevron-right');
            toggleIcon.classList.add('fa-chevron-down');
        } else {
            variantsRow.style.display = 'none';
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-right');
        }
    }

    // Auto-expand first product on load
    document.addEventListener('DOMContentLoaded', function() {
        const firstProductRow = document.querySelector('[data-product-id]');
        if (firstProductRow) {
            const productId = firstProductRow.getAttribute('data-product-id');
            toggleVariants(productId);
        }
    });
</script>
@endpush

@endsection