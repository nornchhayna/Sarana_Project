@extends('layouts.webmaster')

@section('title', 'Dashboard')
@section('breadcrumb', 'Overview')

@section('content')

{{--
    Expected variables from the controller (all optional — sensible fallbacks are used below):
    $totalRevenue, $revenueDelta        // float, float (percent, signed)
    $totalOrders, $ordersDelta
    $newCustomers, $customersDelta
    $lowStockCount, $lowStockDelta
    $chartData   // collection of ['label' => 'Jan', 'revenue' => 4200, 'orders' => 38]
    $recentOrders // collection of order models: id, customer_name, total, status
    $topProducts  // collection of product models: name, category, price, stock, stock_max
    $activity     // collection of ['color' => '#5B6EF5', 'text' => '...', 'time' => '2m ago']
--}}

@php
    $totalRevenue    = $totalRevenue    ?? 48230;
    $revenueDelta    = $revenueDelta    ?? 12.4;
    $totalOrders     = $totalOrders     ?? 312;
    $ordersDelta     = $ordersDelta     ?? 8.1;
    $newCustomers    = $newCustomers    ?? 54;
    $customersDelta  = $customersDelta  ?? -3.2;
    $lowStockCount   = $lowStockCount   ?? 7;
    $lowStockDelta   = $lowStockDelta   ?? 2;

    $chartData = $chartData ?? collect([
        ['label' => 'Jan', 'revenue' => 32, 'orders' => 20],
        ['label' => 'Feb', 'revenue' => 45, 'orders' => 28],
        ['label' => 'Mar', 'revenue' => 38, 'orders' => 24],
        ['label' => 'Apr', 'revenue' => 55, 'orders' => 34],
        ['label' => 'May', 'revenue' => 62, 'orders' => 40],
        ['label' => 'Jun', 'revenue' => 48, 'orders' => 30],
        ['label' => 'Jul', 'revenue' => 70, 'orders' => 46],
        ['label' => 'Aug', 'revenue' => 66, 'orders' => 42],
        ['label' => 'Sep', 'revenue' => 78, 'orders' => 50],
        ['label' => 'Oct', 'revenue' => 84, 'orders' => 55],
        ['label' => 'Nov', 'revenue' => 90, 'orders' => 60],
        ['label' => 'Dec', 'revenue' => 100, 'orders' => 66],
    ]);
    $chartMax = collect($chartData)->max('revenue') ?: 1;

    $recentOrders = $recentOrders ?? collect([
        (object)['id' => 'ORD-1042', 'customer_name' => 'Liam Carter', 'total' => 899.00, 'status' => 'paid'],
        (object)['id' => 'ORD-1041', 'customer_name' => 'Sofia Reyes', 'total' => 249.00, 'status' => 'pending'],
        (object)['id' => 'ORD-1040', 'customer_name' => 'Noah Kim', 'total' => 1299.00, 'status' => 'shipped'],
        (object)['id' => 'ORD-1039', 'customer_name' => 'Ava Chen', 'total' => 599.00, 'status' => 'cancelled'],
        (object)['id' => 'ORD-1038', 'customer_name' => 'Ethan Brooks', 'total' => 349.00, 'status' => 'paid'],
    ]);

    $topProducts = $topProducts ?? collect([
        (object)['name' => 'iPhone 16 Pro', 'category' => 'Smartphones', 'price' => 999.00, 'stock' => 42, 'stock_max' => 50],
        (object)['name' => 'Galaxy S25', 'category' => 'Smartphones', 'price' => 899.00, 'stock' => 18, 'stock_max' => 50],
        (object)['name' => 'Pixel 9', 'category' => 'Smartphones', 'price' => 699.00, 'stock' => 6, 'stock_max' => 50],
        (object)['name' => 'AirPods Pro', 'category' => 'Accessories', 'price' => 249.00, 'stock' => 35, 'stock_max' => 50],
    ]);

    $activity = $activity ?? collect([
        ['color' => '#5B6EF5', 'text' => '<strong>Liam Carter</strong> placed a new order for $899.00', 'time' => '2m ago'],
        ['color' => '#00D4AA', 'text' => '<strong>Payment</strong> confirmed for order ORD-1041', 'time' => '18m ago'],
        ['color' => '#F5A623', 'text' => '<strong>Pixel 9</strong> stock fell below reorder threshold', 'time' => '1h ago'],
        ['color' => '#FF5C7A', 'text' => '<strong>Order ORD-1039</strong> was cancelled by customer', 'time' => '3h ago'],
        ['color' => '#5B6EF5', 'text' => '<strong>Sofia Reyes</strong> left a 5-star review', 'time' => '5h ago'],
    ]);

    $statusClasses = [
        'paid' => 'status-paid',
        'pending' => 'status-pending',
        'shipped' => 'status-shipped',
        'cancelled' => 'status-cancelled',
    ];

    $stockClass = function ($stock, $max) {
        $pct = $max > 0 ? $stock / $max : 0;
        if ($pct <= 0.2) return 'fill-low';
        if ($pct <= 0.5) return 'fill-mid';
        return 'fill-good';
    };
@endphp

{{-- ── STATS GRID ── --}}
<div class="stats-grid">
    <div class="stat-card c1">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">${{ number_format($totalRevenue) }}</div>
        <div class="stat-delta {{ $revenueDelta >= 0 ? 'delta-up' : 'delta-down' }}">
            <i class="fa-solid fa-arrow-{{ $revenueDelta >= 0 ? 'up' : 'down' }}"></i>
            {{ abs($revenueDelta) }}% vs last month
        </div>
        <i class="fa-solid fa-sack-dollar stat-icon"></i>
    </div>

    <div class="stat-card c2">
        <div class="stat-label">Total Orders</div>
        <div class="stat-value">{{ number_format($totalOrders) }}</div>
        <div class="stat-delta {{ $ordersDelta >= 0 ? 'delta-up' : 'delta-down' }}">
            <i class="fa-solid fa-arrow-{{ $ordersDelta >= 0 ? 'up' : 'down' }}"></i>
            {{ abs($ordersDelta) }}% vs last month
        </div>
        <i class="fa-solid fa-cart-shopping stat-icon"></i>
    </div>

    <div class="stat-card c3">
        <div class="stat-label">New Customers</div>
        <div class="stat-value">{{ number_format($newCustomers) }}</div>
        <div class="stat-delta {{ $customersDelta >= 0 ? 'delta-up' : 'delta-down' }}">
            <i class="fa-solid fa-arrow-{{ $customersDelta >= 0 ? 'up' : 'down' }}"></i>
            {{ abs($customersDelta) }}% vs last month
        </div>
        <i class="fa-solid fa-users stat-icon"></i>
    </div>

    <div class="stat-card c4">
        <div class="stat-label">Low Stock Alerts</div>
        <div class="stat-value">{{ $lowStockCount }}</div>
        <div class="stat-delta {{ $lowStockDelta <= 0 ? 'delta-up' : 'delta-down' }}">
            <i class="fa-solid fa-arrow-{{ $lowStockDelta <= 0 ? 'down' : 'up' }}"></i>
            {{ abs($lowStockDelta) }} vs last week
        </div>
        <i class="fa-solid fa-triangle-exclamation stat-icon"></i>
    </div>
</div>

{{-- ── CHART + RECENT ORDERS ── --}}
<div class="mid-row">

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Revenue Overview</div>
                <div class="card-sub">Monthly revenue vs orders</div>
            </div>
            <button class="btn-ghost">This Year <i class="fa-solid fa-chevron-down" style="font-size:10px; margin-left:4px;"></i></button>
        </div>
        <div class="chart-area">
            <div class="chart-bars">
                @foreach($chartData as $point)
                    <div class="bar-group">
                        <div class="bar revenue" style="height: {{ max(4, ($point['revenue'] / $chartMax) * 100) }}%;" title="Revenue: {{ $point['revenue'] }}"></div>
                        <div class="bar orders" style="height: {{ max(4, ($point['orders'] / $chartMax) * 100) }}%;" title="Orders: {{ $point['orders'] }}"></div>
                    </div>
                @endforeach
            </div>
            <div class="chart-bars" style="height:auto; margin-bottom:0;">
                @foreach($chartData as $point)
                    <div class="bar-group" style="height:auto;">
                        <div class="bar-label">{{ $point['label'] }}</div>
                    </div>
                @endforeach
            </div>
            <div class="chart-legend" style="margin-top:16px;">
                <div class="legend-item"><span class="legend-dot" style="background: var(--accent);"></span> Revenue</div>
                <div class="legend-item"><span class="legend-dot" style="background: rgba(91,110,245,0.3);"></span> Orders</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Recent Orders</div>
                <div class="card-sub">Last {{ $recentOrders->count() }} orders placed</div>
            </div>
            <a href="{{ route('webmaster.orders.index') }}" class="btn-ghost">View all</a>
        </div>
        <div class="orders-list">
            @forelse($recentOrders as $order)
                <div class="order-row">
                    <div class="order-thumb"><i class="fa-solid fa-box"></i></div>
                    <div>
                        <div class="order-name">{{ $order->customer_name }}</div>
                        <div class="order-id">{{ $order->id }}</div>
                    </div>
                    <div class="order-price">${{ number_format($order->total, 2) }}</div>
                    <span class="status-badge {{ $statusClasses[$order->status] ?? 'status-pending' }}" style="margin-left:8px;">
                        {{ strtoupper($order->status) }}
                    </span>
                </div>
            @empty
                <div class="order-row"><span style="color: var(--text-3);">No recent orders.</span></div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── PRODUCTS TABLE + ACTIVITY FEED ── --}}
<div class="bottom-row">

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Top Products</div>
                <div class="card-sub">Stock levels at a glance</div>
            </div>
            <a href="{{ route('webmaster.products.index') }}" class="btn-ghost">Manage</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $product)
                        <tr>
                            <td>
                                <div class="prod-name">
                                    <div class="prod-icon"><i class="fa-solid fa-mobile-screen"></i></div>
                                    <div>
                                        {{ $product->name }}
                                        <div style="font-size:11px; color: var(--text-3); font-weight:400;">{{ $product->category }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>
                                <div class="stock-bar-wrap">
                                    <div class="stock-bar">
                                        <div class="stock-fill {{ $stockClass($product->stock, $product->stock_max) }}" style="width: {{ $product->stock_max > 0 ? min(100, ($product->stock / $product->stock_max) * 100) : 0 }}%;"></div>
                                    </div>
                                    <span class="stock-num">{{ $product->stock }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="color: var(--text-3);">No products yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Recent Activity</div>
                <div class="card-sub">Live feed across the store</div>
            </div>
        </div>
        <div class="activity-list">
            @forelse($activity as $item)
                <div class="activity-item">
                    <span class="act-dot" style="background: {{ $item['color'] }};"></span>
                    <div class="act-text">{!! $item['text'] !!}</div>
                    <span class="act-time">{{ $item['time'] }}</span>
                </div>
            @empty
                <div class="activity-item"><span style="color: var(--text-3);">No recent activity.</span></div>
            @endforelse
        </div>
    </div>

</div>

@endsection