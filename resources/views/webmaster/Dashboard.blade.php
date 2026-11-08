@extends('layouts.webmaster')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="container-fluid">

    {{-- =========================
        STAT CARDS (AdminLTE small-box)
    ========================== --}}
    <div class="row">

        {{-- Revenue --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>
                        ${{ number_format($stats['total_revenue'], 2) }}
                    </h3>
                    <p>Total Revenue</p>
                    <small>
                        <i class="fas fa-arrow-{{ $revenueDelta >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($revenueDelta) }}% vs last month
                    </small>
                </div>
                <div class="icon">
                    <i class="fas fa-sack-dollar"></i>
                </div>
                <a href="{{ route('webmaster.orders.index') }}" class="small-box-footer">
                    View Orders <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Orders --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>
                        {{ number_format($stats['total_orders']) }}
                    </h3>
                    <p>Orders</p>
                    <small>
                        {{ $stats['pending_orders'] }} pending
                    </small>
                </div>
                <div class="icon">
                    <i class="fas fa-cart-shopping"></i>
                </div>
                <a href="{{ route('webmaster.orders.index') }}" class="small-box-footer">
                    View Orders <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Products --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>
                        {{ number_format($stats['total_products']) }}
                    </h3>
                    <p>Active Products</p>
                    <small>
                        {{ $lowStockCount }} low in stock
                    </small>
                </div>
                <div class="icon">
                    <i class="fas fa-mobile-screen"></i>
                </div>
                <a href="{{ route('webmaster.products.index') }}" class="small-box-footer">
                    Manage Products <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Customers --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>
                        {{ number_format($stats['total_users']) }}
                    </h3>
                    <p>Customers</p>
                    <small>
                        Registered accounts
                    </small>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('webmaster.contacts.index') }}" class="small-box-footer">
                    Customer Inquiries <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

    </div>

    {{-- =========================
        ORDER STATUS QUICK PILLS
    ========================== --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-outline card-secondary mb-0">
                <div class="card-body py-2 px-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <span class="text-muted mr-2 font-weight-bold" style="font-size: 13px;">
                            <i class="fas fa-filter mr-1"></i> Order Status:
                        </span>
                        <div class="d-flex flex-wrap" style="gap: 8px;">
                            <a href="{{ route('webmaster.orders.index', ['status' => 'pending']) }}" class="badge badge-warning px-2 py-1" style="font-size: 12px;">
                                Pending: <strong>{{ $orderStatusCounts['pending'] ?? 0 }}</strong>
                            </a>
                            <a href="{{ route('webmaster.orders.index', ['status' => 'confirmed']) }}" class="badge badge-primary px-2 py-1" style="font-size: 12px;">
                                Confirmed: <strong>{{ $orderStatusCounts['confirmed'] ?? 0 }}</strong>
                            </a>
                            <a href="{{ route('webmaster.orders.index', ['status' => 'processing']) }}" class="badge badge-info px-2 py-1" style="font-size: 12px;">
                                Processing: <strong>{{ $orderStatusCounts['processing'] ?? 0 }}</strong>
                            </a>
                            <a href="{{ route('webmaster.orders.index', ['status' => 'shipped']) }}" class="badge badge-secondary px-2 py-1" style="font-size: 12px;">
                                Shipped: <strong>{{ $orderStatusCounts['shipped'] ?? 0 }}</strong>
                            </a>
                            <a href="{{ route('webmaster.orders.index', ['status' => 'delivered']) }}" class="badge badge-success px-2 py-1" style="font-size: 12px;">
                                Delivered: <strong>{{ $orderStatusCounts['delivered'] ?? 0 }}</strong>
                            </a>
                            <a href="{{ route('webmaster.orders.index', ['status' => 'cancelled']) }}" class="badge badge-danger px-2 py-1" style="font-size: 12px;">
                                Cancelled: <strong>{{ $orderStatusCounts['cancelled'] ?? 0 }}</strong>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================
        REVENUE CHART + TOP PRODUCTS
    ========================== --}}
    <div class="row">

        {{-- Monthly Revenue & Orders --}}
        <div class="col-lg-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-column mr-2"></i>
                        Monthly Revenue & Orders
                    </h3>

                    <div class="card-tools d-flex align-items-center">
                        <form method="GET" action="{{ route('webmaster.dashboard') }}" class="mr-2" style="margin: 0;">
                            <select name="year" onchange="this.form.submit()" class="form-control form-control-sm" style="width: 120px;">
                                @foreach($availableYears as $yr)
                                    <option value="{{ $yr }}" @selected($yr == $selectedYear)>Year {{ $yr }}</option>
                                @endforeach
                            </select>
                        </form>
                        <span class="badge badge-primary">
                            Total: ${{ number_format($yearTotalRevenue, 2) }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div style="height: 320px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="col-lg-4">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-ranking-star mr-2"></i>
                        Top Selling Products
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('webmaster.products.index') }}" class="btn btn-tool" title="View Catalogue">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($topProducts as $i => $product)
                            <div class="list-group-item d-flex align-items-center justify-content-between py-2">
                                <div class="d-flex align-items-center" style="min-width: 0;">
                                    <div class="mr-3">
                                        <span class="badge {{ $i == 0 ? 'badge-warning' : ($i == 1 ? 'badge-secondary' : ($i == 2 ? 'badge-info' : 'badge-light')) }} font-weight-bold" style="width: 24px; text-align: center;">
                                            {{ $i + 1 }}
                                        </span>
                                    </div>

                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-size-50 mr-3 rounded" style="object-fit: cover; width: 40px; height: 40px;">
                                    @else
                                        <div class="img-size-50 mr-3 rounded bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-mobile-screen text-muted"></i>
                                        </div>
                                    @endif

                                    <div style="min-width: 0;">
                                        <a href="{{ route('webmaster.products.edit', $product->id) }}" class="font-weight-bold text-dark d-block text-truncate" style="max-width: 170px;">
                                            {{ $product->name }}
                                        </a>
                                        <small class="text-muted">
                                            {{ $product->total_sold }} sold • ${{ number_format($product->price, 2) }}
                                        </small>
                                    </div>
                                </div>

                                <div class="text-right ml-2 flex-shrink-0">
                                    <span class="badge {{ $product->stock <= 5 ? 'badge-danger' : ($product->stock <= 15 ? 'badge-warning' : 'badge-success') }}">
                                        {{ $product->stock }} in stock
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0">No sales data yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- =========================
        RECENT ORDERS + QUICK ACTIONS
    ========================== --}}
    <div class="row">

        {{-- Recent Orders --}}
        <div class="col-lg-8">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cart-shopping mr-2"></i>
                        Recent Orders
                    </h3>

                    <div class="card-tools">
                        <a href="{{ route('webmaster.orders.index') }}" class="btn btn-sm btn-primary">
                            View All Orders
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    @php
                                        $statusBadge = match($order->status) {
                                            'pending' => 'badge-warning',
                                            'confirmed', 'processing' => 'badge-info',
                                            'shipped' => 'badge-secondary',
                                            'delivered' => 'badge-success',
                                            'cancelled' => 'badge-danger',
                                            default => 'badge-secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('webmaster.orders.show', $order->id) }}" class="font-weight-bold text-primary">
                                                #{{ $order->formatted_id }}
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $order->created_at }}</small>
                                        </td>
                                        <td>
                                            <div class="font-weight-600">{{ $order->customer_name }}</div>
                                            @if($order->customer_email)
                                                <small class="text-muted">{{ $order->customer_email }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $statusBadge }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            ${{ number_format($order->total, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-cart-shopping fa-2x mb-2"></i>
                                            <br>
                                            No orders yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-lg-4">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-2 text-warning"></i>
                        Quick Actions
                    </h3>
                </div>

                <div class="card-body">
                    {{-- Add Product --}}
                    <a href="{{ route('webmaster.products.create') }}" class="btn btn-primary btn-block mb-3 text-left">
                        <i class="fas fa-plus mr-2"></i> Add New Product
                    </a>

                    {{-- Pending Orders --}}
                    <a href="{{ route('webmaster.orders.index', ['status' => 'pending']) }}" class="btn btn-warning btn-block mb-3 text-left">
                        <i class="fas fa-cart-shopping mr-2"></i> Pending Orders
                        @if(($orderStatusCounts['pending'] ?? 0) > 0)
                            <span class="badge badge-dark float-right">
                                {{ $orderStatusCounts['pending'] }}
                            </span>
                        @endif
                    </a>

                    {{-- Stock Inventory --}}
                    <a href="{{ route('webmaster.stock.index', ['stock_status' => 'low']) }}" class="btn btn-danger btn-block mb-3 text-left">
                        <i class="fas fa-boxes mr-2"></i> Low Stock Alerts
                        @if($lowStockCount > 0)
                            <span class="badge badge-light text-danger float-right font-weight-bold">
                                {{ $lowStockCount }}
                            </span>
                        @endif
                    </a>

                    {{-- Pending Reviews --}}
                    <a href="{{ route('webmaster.reviews.index') }}" class="btn btn-success btn-block mb-3 text-left">
                        <i class="fas fa-star mr-2"></i> Pending Reviews
                        @if($stats['pending_reviews'] > 0)
                            <span class="badge badge-dark float-right">
                                {{ $stats['pending_reviews'] }}
                            </span>
                        @endif
                    </a>

                    {{-- Unread Inquiries --}}
                    <a href="{{ route('webmaster.contacts.index') }}" class="btn btn-info btn-block text-left">
                        <i class="fas fa-envelope mr-2"></i> Customer Messages
                        @if($stats['unread_contacts'] > 0)
                            <span class="badge badge-dark float-right">
                                {{ $stats['unread_contacts'] }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- =========================
        CRITICAL STOCK + ACTIVITY FEED
    ========================== --}}
    <div class="row">

        {{-- Critical Low Stock Table --}}
        <div class="col-lg-6">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-triangle-exclamation mr-2 text-danger"></i>
                        Critical Inventory Watchlist
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('webmaster.stock.index') }}" class="btn btn-tool" title="View Full Inventory">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Specs</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($criticalStockItems as $cItem)
                                    <tr>
                                        <td>
                                            <strong>{{ $cItem->product?->name ?? 'Product' }}</strong>
                                            <div class="text-muted" style="font-size: 11px;">SKU: {{ $cItem->sku ?? '—' }}</div>
                                        </td>
                                        <td>
                                            <small class="badge badge-light">
                                                {{ $cItem->color?->name ?? '' }} {{ $cItem->phoneStorage?->capacity ?? '' }}
                                            </small>
                                        </td>
                                        <td class="text-center font-weight-bold">
                                            <span class="badge {{ $cItem->stock == 0 ? 'badge-danger' : 'badge-warning' }}">
                                                {{ $cItem->stock }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('webmaster.stock.edit', $cItem->id) }}" class="btn btn-xs btn-outline-danger">
                                                <i class="fas fa-plus-circle"></i> Restock
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            <i class="fas fa-check-circle text-success mr-1"></i> All products have healthy stock levels.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Store Activity Feed --}}
        <div class="col-lg-6">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-stream mr-2"></i>
                        Live Store Activity
                    </h3>
                </div>

                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-3 pr-3">
                        @forelse($activity as $item)
                            <li class="item py-2">
                                <div class="product-img">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background: {{ $item['color'] }}; color: #fff; font-size: 13px;">
                                        <i class="fas {{ $item['icon'] ?? 'fa-circle' }}"></i>
                                    </div>
                                </div>
                                <div class="product-info ml-2">
                                    <a href="{{ $item['url'] ?? '#' }}" class="product-title text-dark">
                                        {!! $item['text'] !!}
                                    </a>
                                    <span class="product-description text-muted" style="font-size: 11px;">
                                        <i class="far fa-clock mr-1"></i> {{ $item['time'] }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="item text-center text-muted py-3">No recent activity.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    const monthLabels = [
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];

    const salesData = @json($monthlySales);
    const revenueByMonth = Array(12).fill(0);
    const ordersByMonth = Array(12).fill(0);

    salesData.forEach(row => {
        if (row.month >= 1 && row.month <= 12) {
            revenueByMonth[row.month - 1] = row.revenue || 0;
            ordersByMonth[row.month - 1] = row.orders || 0;
        }
    });

    const ctx = document.getElementById('revenueChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: revenueByMonth,
                    backgroundColor: 'rgba(0, 123, 255, 0.8)',
                    borderColor: '#007bff',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y'
                },
                {
                    label: 'Orders Count',
                    data: ordersByMonth,
                    backgroundColor: 'rgba(240, 96, 63, 0.4)',
                    borderColor: '#f0603f',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Revenue ($)') {
                                return ' Revenue: $' + Number(context.parsed.y).toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                            return ' Orders: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.06)' },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000) return '$' + (value / 1000) + 'k';
                            return '$' + value;
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false },
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush
