@extends('layouts.admin')

@section('title', 'Sales & Orders Report')
@section('page-title', 'Sales & Orders Dashboard')

@section('content')
<div class="container-fluid">
    {{-- Header & Quick Presets --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <p class="text-muted mb-0">Comprehensive tracking of sales revenue, order volumes, fulfillment status, and payment channels.</p>
        </div>
        <div class="col-md-6 text-md-right mt-2 mt-md-0">
            <div class="btn-group btn-group-sm mr-2">
                <a href="{{ route('admin.reports.sales-dashboard', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary {{ $startDate == now()->format('Y-m-d') && $endDate == now()->format('Y-m-d') ? 'active' : '' }}">Today</a>
                <a href="{{ route('admin.reports.sales-dashboard', ['start_date' => now()->subDays(7)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary {{ $startDate == now()->subDays(7)->format('Y-m-d') ? 'active' : '' }}">Last 7 Days</a>
                <a href="{{ route('admin.reports.sales-dashboard', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary {{ $startDate == now()->startOfMonth()->format('Y-m-d') ? 'active' : '' }}">This Month</a>
                <a href="{{ route('admin.reports.sales-dashboard', ['start_date' => now()->subDays(30)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary {{ $startDate == now()->subDays(30)->format('Y-m-d') ? 'active' : '' }}">Last 30 Days</a>
            </div>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card card-outline card-primary mb-4">
        <div class="card-header py-2">
            <h3 class="card-title font-weight-bold text-sm">
                <i class="fas fa-filter mr-1 text-primary"></i> Date & Status Filter
            </h3>
        </div>
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.reports.sales-dashboard') }}" class="form-row align-items-end">
                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">From Date:</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">To Date:</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Order Status:</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="all" {{ ($statusFilter ?? 'all') == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="confirmed" {{ ($statusFilter ?? '') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ ($statusFilter ?? '') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ ($statusFilter ?? '') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ ($statusFilter ?? '') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="pending" {{ ($statusFilter ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ ($statusFilter ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 d-flex">
                    <button type="submit" class="btn btn-primary btn-sm mr-2 flex-grow-1">
                        <i class="fas fa-search mr-1"></i> Apply Filter
                    </button>
                    <a href="{{ route('admin.reports.sales-dashboard') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Top Summary Metric Cards --}}
    <div class="row mb-4">
        {{-- Total Revenue --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>${{ number_format($totalRevenue, 2) }}</h3>
                    <p class="font-weight-bold">Total Sales Revenue</p>
                    <small>Delivered: ${{ number_format($deliveredRevenue, 2) }}</small>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="small-box-footer">
                    Manage Orders <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Total Orders --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>{{ number_format($totalOrders) }}</h3>
                    <p class="font-weight-bold">Total Orders Placed</p>
                    <small>{{ $deliveredOrdersCount }} delivered · {{ $pendingOrdersCount }} pending</small>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="small-box-footer">
                    Order List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Items Sold --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h3>{{ number_format($totalItemsSold) }}</h3>
                    <p class="font-weight-bold">Units Sold</p>
                    <small>In confirmed/delivered orders</small>
                </div>
                <div class="icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <a href="{{ route('admin.reports.product-performance') }}" class="small-box-footer">
                    Product Performance <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Average Order Value & Cancel Rate --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3>${{ number_format($averageOrderValue, 2) }}</h3>
                    <p class="font-weight-bold">Average Order Value</p>
                    <small>Cancellation rate: {{ number_format($cancellationRate, 1) }}%</small>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <a href="#orders-by-status" class="small-box-footer">
                    Status Breakdown <i class="fas fa-arrow-circle-down"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Daily Sales & Orders Trend Table --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-chart-line mr-1 text-primary"></i> Daily Sales & Order Volumes ({{ $startDate }} to {{ $endDate }})
                    </h3>
                </div>
                <div class="card-body table-responsive p-0" style="max-height: 380px;">
                    <table class="table table-striped table-hover table-head-fixed mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-center">Total Orders</th>
                                <th class="text-center">Confirmed / Valid</th>
                                <th class="text-center">Cancelled</th>
                                <th class="text-right">Daily Revenue ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailySales as $day)
                            <tr>
                                <td class="font-weight-bold">
                                    {{ \Carbon\Carbon::parse($day->date)->format('M d, Y (D)') }}
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ $day->total_orders }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success">{{ $day->valid_orders }}</span>
                                </td>
                                <td class="text-center">
                                    @if($day->cancelled_orders > 0)
                                        <span class="badge badge-danger">{{ $day->cancelled_orders }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold text-success">
                                    ${{ number_format($day->revenue, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No order activity recorded in this date range.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders by Status & Orders by Payment Method --}}
    <div class="row">
        {{-- Orders by Status --}}
        <div class="col-lg-6 mb-4" id="orders-by-status">
            <div class="card card-outline card-secondary h-100">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-tasks mr-1 text-secondary"></i> Orders Breakdown by Status
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Status</th>
                                <th class="text-center">Order Count</th>
                                <th class="text-center">% of Total</th>
                                <th class="text-right">Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesByStatus as $status => $data)
                            @php
                                $badgeClass = match($status) {
                                    'delivered' => 'badge-success',
                                    'confirmed' => 'badge-primary',
                                    'processing' => 'badge-info',
                                    'shipped' => 'badge-indigo',
                                    'pending' => 'badge-warning',
                                    'cancelled' => 'badge-danger',
                                    default => 'badge-secondary',
                                };
                                $pct = $totalOrders > 0 ? ($data->count / $totalOrders) * 100 : 0;
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge {{ $badgeClass }} text-uppercase px-2 py-1">{{ $status }}</span>
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ $data->count }}
                                </td>
                                <td class="text-center text-muted">
                                    {{ number_format($pct, 1) }}%
                                </td>
                                <td class="text-right font-weight-bold {{ $status == 'cancelled' ? 'text-muted text-decoration-line-through' : 'text-success' }}">
                                    ${{ number_format($data->total, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No order status data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sales by Payment Method --}}
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-info h-100">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-credit-card mr-1 text-info"></i> Sales by Payment Channel
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Payment Method</th>
                                <th class="text-center">Transactions</th>
                                <th class="text-right">Total Collected</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesByPayment as $payment)
                            <tr>
                                <td class="font-weight-bold text-dark">
                                    <i class="fas fa-wallet mr-1 text-secondary"></i> {{ ucfirst($payment->method) }}
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ $payment->count }}
                                </td>
                                <td class="text-right font-weight-bold text-success">
                                    ${{ number_format($payment->total, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No payment transaction records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Selling Products & Sales by Category --}}
    <div class="row">
        {{-- Top Selling Products --}}
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-success h-100">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-trophy mr-1 text-warning"></i> Top Selling Products
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Units Sold</th>
                                <th class="text-right">Avg Price</th>
                                <th class="text-right">Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $prod)
                            <tr>
                                <td class="font-weight-bold text-dark">
                                    {{ $prod->product_name }}
                                </td>
                                <td class="text-center font-weight-bold">
                                    <span class="badge badge-success px-2 py-1">{{ number_format($prod->total_sold) }}</span>
                                </td>
                                <td class="text-right text-muted">
                                    ${{ number_format($prod->avg_price, 2) }}
                                </td>
                                <td class="text-right font-weight-bold text-success">
                                    ${{ number_format($prod->total_revenue, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No sales items recorded in period</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sales by Category --}}
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-primary h-100">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-folder-open mr-1 text-primary"></i> Sales by Category
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Category</th>
                                <th class="text-center">Units Sold</th>
                                <th class="text-right">Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesByCategory as $cat)
                            <tr>
                                <td class="font-weight-bold text-dark">
                                    {{ $cat->category_name }}
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ number_format($cat->total_sold) }}
                                </td>
                                <td class="text-right font-weight-bold text-primary">
                                    ${{ number_format($cat->total_revenue, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No category sales recorded</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders in This Period --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-list-alt mr-1"></i> Recent Orders in Period
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-tool text-primary">
                            <i class="fas fa-external-link-alt"></i> View All Orders
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date & Time</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Items</th>
                                <th>Payment</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $ord)
                            @php
                                $statusBadge = match($ord->status) {
                                    'delivered' => 'badge-success',
                                    'confirmed' => 'badge-primary',
                                    'processing' => 'badge-info',
                                    'shipped' => 'badge-indigo',
                                    'pending' => 'badge-warning',
                                    'cancelled' => 'badge-danger',
                                    default => 'badge-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="{{ route('admin.orders.show', $ord) }}">
                                        {{ $ord->order_number }}
                                    </a>
                                </td>
                                <td>
                                    <strong>{{ $ord->shipping_name ?? ($ord->user->name ?? 'Guest') }}</strong><br>
                                    <small class="text-muted">{{ $ord->shipping_phone ?? ($ord->user->phone ?? '') }}</small>
                                </td>
                                <td>
                                    <small>{{ $ord->created_at->format('M d, Y H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $statusBadge }} text-uppercase">{{ $ord->status }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border">{{ $ord->items->count() }} items</span>
                                </td>
                                <td>
                                    <small class="badge badge-light border text-uppercase">
                                        {{ $ord->payments->first()->method ?? 'Unpaid' }}
                                    </small>
                                </td>
                                <td class="text-right font-weight-bold text-dark">
                                    ${{ number_format($ord->total, 2) }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.orders.show', $ord) }}" class="btn btn-xs btn-outline-primary" title="View Order">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No recent orders matching criteria</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
