@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="container-fluid">

    {{-- =========================
        STAT CARDS
    ========================== --}}
    <div class="row">

        {{-- Revenue --}}
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>
                        ${{ number_format($stats['total_revenue'], 0) }}
                    </h3>
                    <p>Revenue</p>

                    <small>
                        Delivered orders
                    </small>
                </div>

                <div class="icon">
                    <i class="fas fa-sack-dollar"></i>
                </div>

                <a href="{{ route('admin.orders.index') }}"
                   class="small-box-footer">
                    View Orders
                    <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>


        {{-- Orders --}}
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning">
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

                <a href="{{ route('admin.orders.index') }}"
                   class="small-box-footer">
                    View Orders
                    <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>


        {{-- Products --}}
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>
                        {{ number_format($stats['total_products']) }}
                    </h3>

                    <p>Products</p>

                    <small>
                        Active listings
                    </small>
                </div>

                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>

                <a href="{{ route('admin.products.index') }}"
                   class="small-box-footer">
                    Manage Products
                    <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>


        {{-- Users --}}
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-info">
                <div class="inner">
                    <h3>
                        {{ number_format($stats['total_users']) }}
                    </h3>

                    <p>Users</p>

                    <small>
                        Registered accounts
                    </small>
                </div>

                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>

                <a href="{{ route('admin.users.index') }}"
                   class="small-box-footer">
                    Manage Users
                    <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

    </div>


    {{-- =========================
        REVENUE + TOP PRODUCTS
    ========================== --}}
    <div class="row">

        {{-- Monthly Revenue --}}
        <div class="col-lg-8">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-column me-2"></i>
                        Monthly Revenue
                    </h3>

                    <div class="card-tools">
                        <span class="badge text-bg-primary">
                            {{ date('Y') }} Performance
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

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-ranking-star me-2"></i>
                        Top Products
                    </h3>
                </div>

                <div class="card-body p-0">

                    <div class="list-group list-group-flush">

                        @forelse($topProducts as $i => $product)

                            <div class="list-group-item">

                                <div class="d-flex align-items-center">

                                    {{-- Rank --}}
                                    <div class="me-3">
                                        <span class="badge text-bg-secondary">
                                            {{ $i + 1 }}
                                        </span>
                                    </div>


                                    {{-- Image --}}
                                    @if($product->thumbnail)

                                        <img
                                            src="{{ Storage::url($product->thumbnail) }}"
                                            alt="{{ $product->name }}"
                                            class="img-size-50 me-3 rounded"
                                            style="object-fit: cover;"
                                        >

                                    @else

                                        <div
                                            class="img-size-50 me-3 rounded bg-light d-flex align-items-center justify-content-center"
                                        >
                                            <i class="fas fa-mobile-screen text-muted"></i>
                                        </div>

                                    @endif


                                    {{-- Product --}}
                                    <div class="flex-grow-1">

                                        <div class="fw-bold text-truncate"
                                             style="max-width: 200px;">
                                            {{ $product->name }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $product->order_items_count }} sold
                                        </small>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0">
                                    No sales data yet.
                                </p>
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

            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-cart-shopping me-2"></i>
                        Recent Orders
                    </h3>

                    <div class="card-tools">
                        <a href="{{ route('admin.orders.index') }}"
                           class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($recentOrders as $order)

                                    <tr>

                                        {{-- Order --}}
                                        <td>

                                            <a
                                                href="{{ route('admin.orders.show', $order) }}"
                                                class="fw-bold text-decoration-none"
                                            >
                                                #{{ $order->order_number }}
                                            </a>

                                            <br>

                                            <small class="text-muted">
                                                {{ $order->created_at->format('d M, H:i') }}
                                            </small>

                                        </td>


                                        {{-- Customer --}}
                                        <td>
                                            {{ $order->user->name ?? '—' }}
                                        </td>


                                        {{-- Status --}}
                                        <td>

                                            @php
                                                $statusClass = match($order->status) {
                                                    'pending' =>
                                                        'text-bg-warning',

                                                    'confirmed',
                                                    'processing' =>
                                                        'text-bg-info',

                                                    'delivered' =>
                                                        'text-bg-success',

                                                    'cancelled' =>
                                                        'text-bg-danger',

                                                    default =>
                                                        'text-bg-secondary',
                                                };
                                            @endphp

                                            <span class="badge {{ $statusClass }}">
                                                {{ ucfirst($order->status) }}
                                            </span>

                                        </td>


                                        {{-- Total --}}
                                        <td class="text-end fw-bold">

                                            ${{ number_format($order->total, 2) }}

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="4"
                                            class="text-center text-muted py-4">

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

            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h3>

                </div>


                <div class="card-body">

                    {{-- Add Product --}}
                    <a
                        href="{{ route('admin.products.create') }}"
                        class="btn btn-primary w-100 mb-3"
                    >
                        <i class="fas fa-plus me-2"></i>
                        Add New Product
                    </a>


                    {{-- Pending Orders --}}
                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="btn btn-warning w-100 mb-3"
                    >
                        <i class="fas fa-cart-shopping me-2"></i>

                        Pending Orders

                        @if($stats['pending_orders'] > 0)

                            <span class="badge text-bg-dark float-end">
                                {{ $stats['pending_orders'] }}
                            </span>

                        @endif

                    </a>


                    {{-- Pending Reviews --}}
                    <a
                        href="{{ route('admin.reviews.index') }}"
                        class="btn btn-success w-100 mb-3"
                    >
                        <i class="fas fa-star me-2"></i>

                        Pending Reviews

                        @if($stats['pending_reviews'] > 0)

                            <span class="badge text-bg-dark float-end">
                                {{ $stats['pending_reviews'] }}
                            </span>

                        @endif

                    </a>


                    {{-- Manage Webmasters --}}
                    <a
                        href="{{ route('admin.webmasters.index') }}"
                        class="btn btn-info w-100 mb-3"
                    >
                        <i class="fas fa-user-shield me-2"></i>
                        Manage Webmasters
                        <span class="badge text-bg-dark float-end">
                            {{ $stats['total_webmasters'] ?? 0 }}
                        </span>
                    </a>

                    {{-- Unread Messages --}}
                    <a
                        href="{{ route('admin.contacts.index') }}"
                        class="btn btn-danger w-100"
                    >
                        <i class="fas fa-envelope me-2"></i>

                        Unread Messages

                        @if($stats['unread_contacts'] > 0)

                            <span class="badge text-bg-dark float-end">
                                {{ $stats['unread_contacts'] }}
                            </span>

                        @endif

                    </a>

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
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
    ];

    const salesData = @json($monthlySales);

    const revenueByMonth = Array(12).fill(0);

    salesData.forEach(row => {
        revenueByMonth[row.month - 1] = row.revenue;
    });


    const ctx = document
        .getElementById('revenueChart')
        .getContext('2d');


    new Chart(ctx, {

        type: 'bar',

        data: {

            labels: monthLabels,

            datasets: [
                {
                    label: 'Revenue ($)',

                    data: revenueByMonth,

                    backgroundColor:
                        'rgba(13, 110, 253, 0.75)',

                    borderColor:
                        '#0d6efd',

                    borderWidth: 1,

                    borderRadius: 5,

                    borderSkipped: false
                }
            ]

        },


        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: false
                },

                tooltip: {

                    callbacks: {

                        label: function(context) {

                            return '$' +
                                Number(context.parsed.y)
                                    .toLocaleString(
                                        undefined,
                                        {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }
                                    );

                        }

                    }

                }

            },


            scales: {

                x: {

                    grid: {
                        display: false
                    }

                },

                y: {

                    beginAtZero: true,

                    grid: {
                        color: 'rgba(0,0,0,0.08)'
                    },

                    ticks: {

                        callback: function(value) {

                            if (value >= 1000) {
                                return '$' +
                                    (value / 1000) +
                                    'k';
                            }

                            return '$' + value;
                        }

                    }

                }

            }

        }

    });

</script>

@endpush