@extends('layouts.admin')

@section('title', 'Stock & Inventory Valuation Report')
@section('page-title', 'Stock & Inventory Report')

@section('content')
<div class="container-fluid">
    {{-- Header Action Bar --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <p class="text-muted mb-0">Real-time overview of current stock levels, inventory valuation, low stock warnings, and stock movements.</p>
        </div>
        <div class="col-md-6 text-md-right mt-2 mt-md-0">
            <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-primary btn-sm mr-1">
                <i class="fas fa-boxes mr-1"></i> Manage Stock
            </a>
            <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-outline-info btn-sm mr-1">
                <i class="fas fa-history mr-1"></i> Movement History
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
    </div>

    {{-- Top Summary Metric Cards --}}
    <div class="row mb-4">
        {{-- Total Stock Value --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>${{ number_format($totalStockValue, 2) }}</h3>
                    <p class="font-weight-bold">Total Stock Value</p>
                    <small>{{ number_format($totalVariants) }} active variants</small>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('admin.stock.index') }}" class="small-box-footer">
                    View Stock List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Total Units In Stock --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>{{ number_format($totalItems) }}</h3>
                    <p class="font-weight-bold">Total Items In Stock</p>
                    <small>Across {{ $totalProducts }} products ({{ $inStockCount }} healthy)</small>
                </div>
                <div class="icon">
                    <i class="fas fa-cubes"></i>
                </div>
                <a href="{{ route('admin.products.index') }}" class="small-box-footer">
                    Manage Products <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Low Stock Items --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3>{{ $lowStockCount }}</h3>
                    <p class="font-weight-bold">Low Stock Items</p>
                    <small>Items with 10 or fewer units</small>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="#low-stock-section" class="small-box-footer">
                    Inspect Low Stock <i class="fas fa-arrow-circle-down"></i>
                </a>
            </div>
        </div>

        {{-- Out of Stock Items --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-danger">
                <div class="inner">
                    <h3>{{ $outOfStockCount }}</h3>
                    <p class="font-weight-bold">Out of Stock</p>
                    <small>Variants needing replenishment</small>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <a href="#out-of-stock-section" class="small-box-footer">
                    Inspect Out of Stock <i class="fas fa-arrow-circle-down"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Stock by Category & Stock by Brand --}}
    <div class="row">
        {{-- Stock by Category --}}
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-primary h-100">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-tags mr-1 text-primary"></i> Stock by Category
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover table-valign-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Category</th>
                                <th class="text-center">Variants</th>
                                <th class="text-center">Stock Qty</th>
                                <th class="text-right">Valuation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockByCategory as $category)
                            <tr>
                                <td class="font-weight-bold text-dark">
                                    {{ $category->category_name }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-secondary">{{ $category->variant_count }}</span>
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ number_format($category->total_stock) }}
                                </td>
                                <td class="text-right font-weight-bold text-success">
                                    ${{ number_format($category->total_value, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No category data found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Stock by Brand --}}
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-info h-100">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-trademark mr-1 text-info"></i> Stock by Brand
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover table-valign-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Brand</th>
                                <th class="text-center">Variants</th>
                                <th class="text-center">Stock Qty</th>
                                <th class="text-right">Valuation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockByBrand as $brand)
                            <tr>
                                <td class="font-weight-bold text-dark">
                                    {{ $brand->brand_name }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-secondary">{{ $brand->variant_count }}</span>
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ number_format($brand->total_stock) }}
                                </td>
                                <td class="text-right font-weight-bold text-info">
                                    ${{ number_format($brand->total_value, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No brand data found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- High Value Stock Items (Top 20) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-coins mr-1 text-success"></i> High Value Inventory Items (Top 20 by Stock Value)
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Product Details</th>
                                <th>SKU</th>
                                <th>Specs / Color</th>
                                <th class="text-center">Stock</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($highValueItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $item->product->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $item->product->brand->name ?? '' }}</small>
                                </td>
                                <td>
                                    <code>{{ $item->sku ?? 'N/A' }}</code>
                                </td>
                                <td>
                                    <span class="badge badge-light border">{{ $item->color->name ?? 'Default Color' }}</span>
                                    @if($item->phoneStorage)
                                        <span class="badge badge-light border">{{ $item->phoneStorage->capacity ?? '' }}</span>
                                    @endif
                                    @if($item->phoneRam)
                                        <span class="badge badge-light border">{{ $item->phoneRam->capacity ?? '' }} RAM</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success px-2 py-1">{{ $item->stock }}</span>
                                </td>
                                <td class="text-right font-weight-bold">
                                    ${{ number_format($item->price, 2) }}
                                </td>
                                <td class="text-right font-weight-bold text-success">
                                    ${{ number_format($item->stock_value, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No inventory items available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Low Stock & Out of Stock Sections --}}
    <div class="row">
        {{-- Low Stock Items --}}
        <div class="col-lg-6 mb-4" id="low-stock-section">
            <div class="card card-outline card-warning h-100">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-exclamation-triangle text-warning mr-1"></i> Low Stock Items (≤ 10 Units)
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Product / SKU</th>
                                <th>Variant</th>
                                <th class="text-center">Stock</th>
                                <th class="text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockItems as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $item->sku }}</small>
                                </td>
                                <td>
                                    <small>{{ $item->color->name ?? '' }} {{ $item->phoneStorage->capacity ?? '' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning font-weight-bold">{{ $item->stock }}</span>
                                </td>
                                <td class="text-right font-weight-bold">
                                    ${{ number_format($item->price, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-success py-4">
                                    <i class="fas fa-check-circle mr-1"></i> No low stock alerts! All stock is healthy.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Out of Stock Items --}}
        <div class="col-lg-6 mb-4" id="out-of-stock-section">
            <div class="card card-outline card-danger h-100">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-ban text-danger mr-1"></i> Out of Stock Items (0 Units)
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Product / SKU</th>
                                <th>Variant</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($outOfStockItems as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $item->sku }}</small>
                                </td>
                                <td>
                                    <small>{{ $item->color->name ?? '' }} {{ $item->phoneStorage->capacity ?? '' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-danger">Out of Stock</span>
                                </td>
                                <td class="text-right font-weight-bold">
                                    ${{ number_format($item->price, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-success py-4">
                                    <i class="fas fa-check-circle mr-1"></i> Zero out-of-stock items!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Stock Movements (Ledger Audit) --}}
    @if(isset($recentMovements) && $recentMovements->count() > 0)
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-exchange-alt mr-1 text-secondary"></i> Recent Stock Movements Ledger
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-tool text-primary">
                            <i class="fas fa-external-link-alt"></i> View All Movements
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Product Variant</th>
                                <th>Movement Type</th>
                                <th class="text-center">Quantity</th>
                                <th>Logged By</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentMovements as $mov)
                            <tr>
                                <td>
                                    <small class="text-muted">{{ $mov->created_at ? $mov->created_at->format('M d, Y H:i') : 'N/A' }}</small>
                                </td>
                                <td>
                                    <strong>{{ $mov->variant->product->name ?? 'Variant #' . $mov->product_variant_id }}</strong>
                                    <small class="text-muted">({{ $mov->variant->color->name ?? '' }} {{ $mov->variant->phoneStorage->capacity ?? '' }})</small>
                                </td>
                                <td>
                                    @php
                                        $typeBadge = match($mov->type) {
                                            'purchase_receipt' => 'badge-success',
                                            'adjustment_in', 'sale_return' => 'badge-info',
                                            'sale' => 'badge-primary',
                                            'damage', 'adjustment_out' => 'badge-danger',
                                            default => 'badge-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $typeBadge }} text-uppercase">{{ str_replace('_', ' ', $mov->type) }}</span>
                                </td>
                                <td class="text-center font-weight-bold {{ in_array($mov->type, ['purchase_receipt', 'adjustment_in', 'sale_return']) ? 'text-success' : 'text-danger' }}">
                                    {{ in_array($mov->type, ['purchase_receipt', 'adjustment_in', 'sale_return']) ? '+' : '-' }}{{ $mov->quantity }}
                                </td>
                                <td>
                                    <small>{{ $mov->createdBy->name ?? 'System' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $mov->notes ?? '-' }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
