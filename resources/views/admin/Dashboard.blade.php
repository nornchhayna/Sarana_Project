@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Overview')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

@endpush
@section('content')

{{-- ── STATS GRID ── --}}
<div class="stats-grid">
    {{-- Revenue --}}
    <div class="stat-card c1">
        <div class="stat-label">Revenue</div>
        <div class="stat-value">${{ number_format($stats['total_revenue'], 0) }}</div>
        <div class="stat-delta">
            <span style="color: var(--text-3);">Delivered orders</span>
        </div>
        <i class="fa-solid fa-sack-dollar stat-icon"></i>
    </div>

    {{-- Orders --}}
    <div class="stat-card c4">
        <div class="stat-label">Orders</div>
        <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
        <div class="stat-delta">
            <i class="fa-solid fa-clock delta-up" style="color: var(--accent-4);"></i>
            <span style="color: var(--accent-4);">{{ $stats['pending_orders'] }} pending</span>
        </div>
        <i class="fa-solid fa-cart-shopping stat-icon"></i>
    </div>

    {{-- Products --}}
    <div class="stat-card c2">
        <div class="stat-label">Products</div>
        <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
        <div class="stat-delta">
            <span style="color: var(--text-3);">Active listings</span>
        </div>
        <i class="fa-solid fa-box stat-icon"></i>
    </div>

    {{-- Users --}}
    <div class="stat-card c3">
        <div class="stat-label">Users</div>
        <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
        <div class="stat-delta">
            <span style="color: var(--text-3);">Registered accounts</span>
        </div>
        <i class="fa-solid fa-users stat-icon"></i>
    </div>
</div>

{{-- ── MIDDLE ROW (Chart + Top Products) ── --}}
<div class="mid-row">
    
    {{-- Monthly Revenue Chart --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Monthly Revenue</div>
                <div class="card-sub">{{ date('Y') }} Performance</div>
            </div>
        </div>
        <div class="chart-area" style="height: 320px; position: relative;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Top Products</div>
        </div>
        <div class="orders-list" style="padding-top: 12px;">
            @forelse($topProducts as $i => $product)
                <div class="order-row">
                    <span style="color: var(--text-3); font-family: var(--mono); font-size: 12px; width: 15px;">{{ $i + 1 }}</span>
                    
                    @if($product->thumbnail)
                        <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="order-thumb" style="object-fit: cover; background: none;">
                    @else
                        <div class="order-thumb">
                            <i class="fa-solid fa-mobile-screen" style="color: var(--text-3);"></i>
                        </div>
                    @endif
                    
                    <div style="flex: 1; overflow: hidden;">
                        <div class="order-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $product->name }}</div>
                        <div class="order-id">{{ $product->order_items_count }} sold</div>
                    </div>
                </div>
            @empty
                <div style="padding: 22px; color: var(--text-3); font-size: 13px; text-align: center;">No sales data yet.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── BOTTOM ROW (Orders + Quick Actions) ── --}}
<div class="mid-row">
    
    {{-- Recent Orders --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent Orders</div>
            <a href="{{ route('admin.orders.index') }}" class="btn-ghost text-decoration-none">View All</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" style="color: var(--text-1); text-decoration: none; font-weight: 600;">
                                    #{{ $order->order_number }}
                                </a>
                                <div style="font-size: 11px; color: var(--text-3); font-family: var(--mono); margin-top: 4px;">
                                    {{ $order->created_at->format('d M, H:i') }}
                                </div>
                            </td>
                            <td style="color: var(--text-1); font-weight: 500;">
                                {{ $order->user->name ?? '—' }}
                            </td>
                            <td>
                                @php
                                    $statusClass = match($order->status) {
                                        'pending'    => 'status-pending',
                                        'confirmed', 'processing' => 'status-shipped',
                                        'delivered'  => 'status-paid',
                                        'cancelled'  => 'status-cancelled',
                                        default      => 'status-pending',
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td style="text-align: right; color: var(--text-1); font-family: var(--mono); font-weight: 600;">
                                ${{ number_format($order->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: var(--text-3);">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Quick Actions</div>
        </div>
        <div class="activity-list" style="padding-top: 16px;">
            
            <a href="{{ route('admin.products.create') }}" class="activity-item" style="text-decoration: none; align-items: center;">
                <div class="order-thumb" style="background: rgba(91, 110, 245, 0.1); color: var(--accent);">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div class="act-text" style="color: var(--text-1); font-weight: 500; font-size: 14px;">Add New Product</div>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="activity-item" style="text-decoration: none; align-items: center;">
                <div class="order-thumb" style="background: rgba(245, 166, 35, 0.1); color: var(--accent-4);">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div class="act-text" style="color: var(--text-1); font-weight: 500; font-size: 14px; flex: 1;">Pending Orders</div>
                @if($stats['pending_orders'] > 0)
                    <div class="nav-badge" style="background: var(--accent-4);">{{ $stats['pending_orders'] }}</div>
                @endif
            </a>

            <a href="{{ route('admin.reviews.index') }}" class="activity-item" style="text-decoration: none; align-items: center;">
                <div class="order-thumb" style="background: rgba(0, 212, 170, 0.1); color: var(--accent-2);">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="act-text" style="color: var(--text-1); font-weight: 500; font-size: 14px; flex: 1;">Pending Reviews</div>
                @if($stats['pending_reviews'] > 0)
                    <div class="nav-badge" style="background: var(--accent-2);">{{ $stats['pending_reviews'] }}</div>
                @endif
            </a>

            <a href="{{ route('admin.contacts.index') }}" class="activity-item" style="text-decoration: none; align-items: center; border-bottom: none;">
                <div class="order-thumb" style="background: rgba(255, 92, 122, 0.1); color: var(--accent-3);">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="act-text" style="color: var(--text-1); font-weight: 500; font-size: 14px; flex: 1;">Unread Messages</div>
                @if($stats['unread_contacts'] > 0)
                    <div class="nav-badge">{{ $stats['unread_contacts'] }}</div>
                @endif
            </a>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
    // ── Monthly Revenue Bar Chart (Dark Theme Config) ──
    const monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const salesData   = @json($monthlySales);

    const revenueByMonth = Array(12).fill(0);
    salesData.forEach(row => { revenueByMonth[row.month - 1] = row.revenue; });

    // Grab colors from your CSS variables to keep the chart perfectly synced
    const style = getComputedStyle(document.body);
    const accentColor = style.getPropertyValue('--accent').trim() || '#5B6EF5';
    const borderColor = style.getPropertyValue('--border').trim() || '#252A3A';
    const textMuted = style.getPropertyValue('--text-3').trim() || '#555B72';
    const monoFont = style.getPropertyValue('--mono').trim() || 'monospace';

    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Revenue ($)',
                data: revenueByMonth,
                backgroundColor: accentColor,
                borderRadius: 4,
                borderSkipped: false,
                barThickness: 24,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1A1E2B',
                    titleColor: '#E8EAF2',
                    bodyColor: '#8B90A7',
                    borderColor: borderColor,
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: { 
                    grid: { display: false },
                    ticks: { color: textMuted, font: { family: monoFont, size: 11 } },
                    border: { display: false }
                },
                y: {
                    grid: { color: borderColor, drawTicks: false },
                    ticks: {
                        color: textMuted,
                        font: { family: monoFont, size: 11 },
                        padding: 10,
                        callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
                    },
                    border: { display: false }
                }
            }
        }
    });
</script>
@endpush