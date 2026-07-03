@extends('layouts.admin')
@section('title', 'Sales Orders')
@section('page-title', 'Manage Orders')

@section('content')

{{-- ── SALES ORDER DASHBOARD SPECIFIC OVERRIDES ── --}}
<style>
    .page-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap; }
    
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; padding: 9px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(91, 110, 245, 0.2); }
    .btn-secondary { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-secondary:hover { border-color: var(--accent, #5B6EF5); color: var(--accent, #5B6EF5); }
    
    .filters { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
    .form-input, .form-select { background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 9px 12px; border-radius: 8px; font-size: 13px; font-family: var(--sans, sans-serif); outline: none; transition: border-color 0.2s; }
    .form-input:focus, .form-select:focus { border-color: var(--accent, #5B6EF5); }
    .form-select option { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); }

    /* Counter Metric Badges Navigation Grid Layout */
    .status-counter-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-bottom: 24px; }
    .counter-link-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 10px; padding: 12px 16px; display: flex; flex-direction: column; text-decoration: none; transition: all 0.2s ease; }
    .counter-link-card:hover, .counter-link-card.active { border-color: var(--accent, #5B6EF5); background: rgba(91, 110, 245, 0.04); }
    .counter-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; color: var(--text-3, #555B72); margin-bottom: 6px; }
    .counter-num { font-size: 20px; font-family: var(--mono, monospace); font-weight: 700; color: var(--text-1, #E8EAF2); }
    
    .order-num-link { color: var(--text-1, #E8EAF2); text-decoration: none; font-weight: 700; font-family: var(--mono, monospace); }
    .order-num-link:hover { color: var(--accent, #5B6EF5); }

    .action-group { display: flex; gap: 6px; justify-content: flex-end; }
    .btn-action { width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--border, #252A3A); background: var(--card, #1A1E2B); color: var(--text-2, #8B90A7); display: grid; place-items: center; cursor: pointer; transition: all 0.2s; text-decoration: none; font-size: 13px; }
    .btn-action:hover { border-color: var(--accent, #5B6EF5); color: var(--text-1, #E8EAF2); background: rgba(91, 110, 245, 0.1); }

    .table-wrap { overflow-x: auto; background: var(--card, #1A1E2B); border-radius: 12px; border: 1px solid var(--border, #252A3A); }
    table { width: 100%; border-collapse: collapse; }
    thead th { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text-3, #555B72); padding: 14px 22px; text-align: left; border-bottom: 1px solid var(--border, #252A3A); }
    tbody tr { border-bottom: 1px solid rgba(37, 42, 58, 0.5); }
    tbody tr:hover { background: rgba(255, 255, 255, 0.02); }
    tbody td { padding: 14px 22px; font-size: 13px; color: var(--text-2, #8B90A7); vertical-align: middle; }
    
    .status-badge { font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 20px; font-family: monospace; display: inline-block; }
    .status-pending { background: rgba(245, 166, 35, 0.12); color: #F5A623; }
    .status-confirmed { background: rgba(0, 212, 170, 0.12); color: #00D4AA; }
    .status-processing { background: rgba(91, 110, 245, 0.12); color: #5B6EF5; }
    .status-shipped { background: rgba(139, 144, 167, 0.12); color: #8B90A7; }
    .status-delivered { background: rgba(0, 212, 170, 0.2); color: #00E6B8; }
    .status-cancelled { background: rgba(255, 92, 122, 0.12); color: #FF5C7A; }
    .card-footer { padding: 16px 22px; border-top: 1px solid var(--border, #252A3A); }
</style>

{{-- ── QUICK LINK SEGMENT SUMMARY COUNTS ── --}}
<div class="status-counter-grid">
    @foreach($statuses as $s)
        <a href="{{ route('admin.orders.index', ['status' => $s]) }}" class="counter-link-card {{ request('status') === $s ? 'active' : '' }}">
            <span class="counter-label">{{ $s }}</span>
            <span class="counter-num">{{ $counts[$s] ?? 0 }}</span>
        </a>
    @endforeach
</div>

{{-- ── FILTERS TOOLBAR ── --}}
<div class="page-toolbar">
    <form class="filters" method="GET" style="width: 100%; justify-content: space-between;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search order #, customer…" style="width: 220px;">
            
            <select name="status" class="form-select" style="width: 140px;">
                <option value="">All Statuses</option>
                @foreach($statuses as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input" title="From Date">
            <span style="color: var(--text-3);">to</span>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input" title="To Date">
            
            <button class="btn-secondary"><i class="fa-solid fa-filter"></i> Filter</button>
            @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                <a href="{{ route('admin.orders.index') }}" class="btn-secondary" style="border-color: transparent;">Clear</a>
            @endif
        </div>
        
        <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn-secondary" style="background: var(--surface);">
            <i class="fa-solid fa-file-csv" style="color: var(--accent-2);"></i> Export CSV
        </a>
    </form>
</div>

{{-- ── MAIN DATA TABLE ── --}}
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="padding-left: 22px;">Order Number</th>
                <th>Customer Name</th>
                <th>Date Placed</th>
                <th>Payment Option</th>
                <th>Status</th>
                <th>Total Bill</th>
                <th style="text-align: right; padding-right: 22px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td style="padding-left: 22px;">
                    <a href="{{ route('admin.orders.show', $order) }}" class="order-num-link">
                        #{{ $order->order_number }}
                    </a>
                </td>
                <td style="color: var(--text-1, #E8EAF2); font-weight: 500;">
                    {{ $order->user->name ?? 'Guest Account' }}
                </td>
                <td style="font-family: var(--mono); font-size: 12px; color: var(--text-3);">
                    {{ $order->created_at->format('d M Y, H:i') }}
                </td>
                <td>
                    <span style="font-size: 12px; color: var(--text-2);">
                        {{ $order->payment->method ?? 'COD' }}
                    </span>
                </td>
                <td>
                    <span class="status-badge status-{{ $order->status }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td style="font-family: var(--mono); font-weight: 700; color: var(--text-1);">
                    ${{ number_format($order->total, 2) }}
                </td>
                <td style="padding-right: 22px;">
                    <div class="action-group">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn-action" title="View details">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-3, #555B72);">No orders found matching parameters.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($orders->hasPages())
    <div class="card-footer">
        {{ $orders->links() }}
    </div>
    @endif
</div>

@endsection