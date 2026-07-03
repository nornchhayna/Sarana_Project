@extends('layouts.admin')
@section('title', 'Products')
@section('page-title', 'Products')


@section('content')

{{-- ── TOOLBAR ── --}}
<div class="page-toolbar">
    <div>
        <a href="{{ route('admin.products.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Add Product
        </a>
    </div>
    
    <form class="filters" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search products…">
        
        <select name="category" class="form-select" style="width: 160px;">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        
        <select name="brand" class="form-select" style="width: 140px;">
            <option value="">All Brands</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}" @selected(request('brand') == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        
        <button class="btn-secondary">Filter</button>
    </form>
</div>

{{-- ── PRODUCTS TABLE ── --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:60px; padding-left: 22px;">Img</th>
                    <th>Name / SKU</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Base Price</th>
                    <th>Sale Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th style="text-align: center;">Featured</th>
                    <th style="text-align: right; padding-right: 22px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td style="padding-left: 22px;">
                        @if($product->thumbnail)
                            <div class="product-thumb">
                                <img src="{{ asset('storage/'.$product->thumbnail) }}" alt="{{ $product->name }}">
                            </div>
                        @else
                            <div class="product-thumb">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="color: var(--text-1); font-weight: 600;">{{ $product->name }}</div>
                        <div class="prod-sku">{{ $product->sku }}</div>
                    </td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->brand->name }}</td>
                    <td style="font-family: var(--mono); color: var(--text-1);">${{ number_format($product->base_price, 2) }}</td>
                    <td style="font-family: var(--mono); color: var(--accent-2);">
                        {{ $product->sale_price ? '$'.number_format($product->sale_price,2) : '—' }}
                    </td>
                    <td>
                        @php $stockTotal = $product->stock->sum('quantity'); @endphp
                        @if($stockTotal > 5)
                            <span class="status-badge status-paid">{{ $stockTotal }} In Stock</span>
                        @elseif($stockTotal > 0)
                            <span class="status-badge status-pending">{{ $stockTotal }} Low</span>
                        @else
                            <span class="status-badge status-cancelled">Out of Stock</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge {{ $product->is_active ? 'status-paid' : 'status-pending' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        @if($product->is_featured)
                            <i class="fa-solid fa-star" style="color: var(--accent-4);"></i>
                        @else
                            <i class="fa-regular fa-star" style="color: var(--text-3);"></i>
                        @endif
                    </td>
                    <td style="padding-right: 22px;">
                        <div class="action-group">
                            <a href="{{ route('admin.products.show', $product) }}" class="btn-action" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn-action" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')" style="margin: 0;">
                                @csrf @method('DELETE')
                                <button class="btn-action delete" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-3);">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="card-footer">
        {{ $products->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection