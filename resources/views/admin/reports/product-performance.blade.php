@extends('layouts.admin')

@section('title', 'Product Performance')
@section('page-title', 'Product Performance')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Date Range Filter
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" class="form-inline">
                    <div class="form-group mr-2">
                        <label class="mr-2">From:</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                    </div>
                    <div class="form-group mr-2">
                        <label class="mr-2">To:</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.reports.product-performance') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Best Sellers --}}
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Best Sellers (Top 10)</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($bestSellers->count() > 0)
                            @foreach($bestSellers as $product)
                            <tr>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ number_format($product->total_sold) }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2" class="text-center text-muted">No best sellers data available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Worst Sellers --}}
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Slow Moving (Stock but No Sales)</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($worstSellers->count() > 0)
                            @foreach($worstSellers as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->stock }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2" class="text-center text-muted">No slow moving items</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Product Performance Table --}}
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Performance Details</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Orders</th>
                            <th>Qty Sold</th>
                            <th>Revenue</th>
                            <th>Avg Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($productPerformance->count() > 0)
                            @foreach($productPerformance as $product)
                            <tr>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->order_count }}</td>
                                <td>{{ number_format($product->total_sold) }}</td>
                                <td>${{ number_format($product->total_revenue, 2) }}</td>
                                <td>${{ number_format($product->avg_price, 2) }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center text-muted">No product performance data available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                {{ $productPerformance->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
