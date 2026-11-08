@extends('layouts.admin')
@section('title', 'Products')
@section('page-title', 'Products')


@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Products List</h3>
        <div class="card-tools">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="row mb-3 p-3">
            <div class="col-md-12">
                <form class="form-inline" method="GET">
                    <div class="input-group input-group-sm mr-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search products…">
                    </div>

                    <select name="category" class="form-control form-control-sm mr-2" style="width: 160px;">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <select name="brand" class="form-control form-control-sm mr-2" style="width: 140px;">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(request('brand') == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-secondary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </form>
            </div>
        </div>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width:60px;">Img</th>
                    <th>Name / SKU</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Base Price</th>
                    <th>Sale Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th class="text-center">Featured</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        @if($product->thumbnail)
                            <img src="{{ asset('storage/'.$product->thumbnail) }}" alt="{{ $product->name }}" style="width:40px; height:40px; object-fit:cover; border-radius:4px;">
                        @else
                            <div style="width:40px; height:40px; background:#f0f0f0; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#999;">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:600;">{{ $product->name }}</div>
                        <div style="font-size:12px; color:#6c757d;">{{ $product->sku }}</div>
                    </td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->brand->name }}</td>
                    <td>${{ number_format($product->base_price, 2) }}</td>
                    <td>
                        {{ $product->sale_price ? '$'.number_format($product->sale_price,2) : '—' }}
                    </td>
                    <td>
                        @php $stockTotal = $product->variants->sum('stock'); @endphp
                        @if($stockTotal > 5)
                            <span class="badge badge-success">{{ $stockTotal }} In Stock</span>
                        @elseif($stockTotal > 0)
                            <span class="badge badge-warning">{{ $stockTotal }} Low</span>
                        @else
                            <span class="badge badge-danger">Out of Stock</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($product->is_featured)
                            <i class="fas fa-star text-warning"></i>
                        @else
                            <i class="far fa-star text-muted"></i>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')" style="margin: 0;">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">No products found.</td>
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