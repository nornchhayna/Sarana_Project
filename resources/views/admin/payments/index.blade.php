@extends('layouts.admin')
@section('title', 'Payment Transactions')
@section('page-title', 'Billing & Transactions')

@section('content')

{{-- ── TRANSACTION DASHBOARD SPECIFIC OVERRIDES ── --}}
<style>
    .page-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap; }
    
    .btn-secondary { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-secondary:hover { border-color: var(--accent, #5B6EF5); color: var(--accent, #5B6EF5); }
    
    .filters { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
    .form-input, .form-select { background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 9px 12px; border-radius: 8px; font-size: 13px; font-family: var(--sans, sans-serif); outline: none; transition: border-color 0.2s; }
    .form-input:focus, .form-select:focus { border-color: var(--accent, #5B6EF5); }
    .form-select option { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); }

    /* Financial Statistics Matrix Cards Grid Layout */
    .finance-summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .finance-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 12px; padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; }
    .finance-title { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600; color: var(--text-3, #555B72); margin-bottom: 4px; }
    .finance-amount { font-size: 24px; font-family: var(--mono, monospace); font-weight: 700; color: var(--text-1, #E8EAF2); }
    .finance-icon-box { width: 44px; height: 44px; border-radius: 8px; display: grid; place-items: center; font-size: 16px; }

    .tx-mono-text { font-family: var(--mono, monospace); font-size: 12px; color: var(--text-3, #555B72); }
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
    .status-paid { background: rgba(0, 212, 170, 0.12); color: #00D4AA; }
    .status-pending { background: rgba(245, 166, 35, 0.12); color: #F5A623; }
    .status-refunded { background: rgba(91, 110, 245, 0.12); color: #5B6EF5; }
    .status-failed { background: rgba(255, 92, 122, 0.12); color: #FF5C7A; }
    .card-footer { padding: 16px 22px; border-top: 1px solid var(--border, #252A3A); }
</style>

{{-- ── REVENUE STATISTICS OVERVIEWS ── --}}
<div class="finance-summary-grid">
    <div class="finance-card">
        <div>
            <div class="finance-title">Captured Revenue</div>
            <div class="finance-amount" style="color: var(--accent-2, #00D4AA);">${{ number_format($totals['paid'], 2) }}</div>
        </div>
        <div class="finance-icon-box" style="background: rgba(0, 212, 170, 0.1); color: #00D4AA;">
            <i class="fa-solid fa-cash-register"></i>
        </div>
    </div>
    
    <div class="finance-card">
        <div>
            <div class="finance-title">Escrow Pending</div>
            <div class="finance-amount" style="color: var(--accent-4, #F5A623);">${{ number_format($totals['pending'], 2) }}</div>
        </div>
        <div class="finance-icon-box" style="background: rgba(245, 166, 35, 0.1); color: #F5A623;">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
    </div>

    <div class="finance-card">
        <div>
            <div class="finance-title">Total Reversed/Refunded</div>
            <div class="finance-amount" style="color: var(--accent, #5B6EF5);">${{ number_format($totals['refunded'], 2) }}</div>
        </div>
        <div class="finance-icon-box" style="background: rgba(91, 110, 245, 0.1); color: #5B6EF5;">
            <i class="fa-solid fa-arrow-rotate-left"></i>
        </div>
    </div>
</div>

{{-- ── SEARCH FILTER TOOLBAR ── --}}
<div class="page-toolbar">
    <form class="filters" method="GET" style="width: 100%;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search Tx ID, order number…" style="width: 240px;">
        
        <select name="status" class="form-select" style="width: 140px;">
            <option value="">All Statuses</option>
            <option value="paid" @selected(request('status') === 'paid')>Paid</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="refunded" @selected(request('status') === 'refunded')>Refunded</option>
            <option value="failed" @selected(request('status') === 'failed')>Failed</option>
        </select>

        <select name="method" class="form-select" style="width: 150px;">
            <option value="">All Methods</option>
            <option value="Stripe" @selected(request('method') === 'Stripe')>Stripe</option>
            <option value="PayPal" @selected(request('method') === 'PayPal')>PayPal</option>
            <option value="Bank Transfer" @selected(request('method') === 'Bank Transfer')>Bank Transfer</option>
            <option value="COD" @selected(request('method') === 'COD')>COD</option>
        </select>

        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input" title="From creation date">
        <span style="color: var(--text-3, #555B72);">to</span>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input" title="To creation date">
        
        <button class="btn-secondary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        @if(request()->anyFilled(['search', 'status', 'method', 'date_from', 'date_to']))
            <a href="{{ route('admin.payments.index') }}" class="btn-secondary" style="border-color: transparent;">Reset</a>
        @endif
    </form>
</div>

{{-- ── TRANSACTIONS MASTER LIST TABLE ── --}}
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="padding-left: 22px;">Transaction Reference ID</th>
                <th>Order Target</th>
                <th>Customer Profile</th>
                <th>Payment Method</th>
                <th>Execution Date</th>
                <th>Status</th>
                <th>Amount Captured</th>
                <th style="text-align: right; padding-right: 22px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td style="padding-left: 22px;">
                    <a href="{{ route('admin.payments.show', $payment) }}" class="order-num-link">
                        {{ $payment->transaction_id ?: 'UNASSIGNED_REF' }}
                    </a>
                </td>
                <td>
                    @if($payment->order)
                        <a href="{{ route('admin.orders.show', $payment->order) }}" class="order-num-link" style="color: var(--accent, #5B6EF5);">
                            #{{ $payment->order->order_number }}
                        </a>
                    @else
                        <span style="color: var(--text-3);">—</span>
                    @endif
                </td>
                <td style="color: var(--text-1, #E8EAF2); font-weight: 500;">
                    {{ $payment->order->user->name ?? 'Guest Client' }}
                </td>
                <td>
                    <span style="font-size: 12px; color: var(--text-2);">
                        {{ $payment->method ?? 'COD' }}
                    </span>
                </td>
                <td class="tx-mono-text">
                    {{ $payment->created_at->format('Y-m-d H:i') }}
                </td>
                <td>
                    <span class="status-badge status-{{ $payment->status }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
                <td style="font-family: var(--mono); font-weight: 700; color: var(--text-1);">
                    ${{ number_format($payment->amount, 2) }}
                </td>
                <td style="padding-right: 22px;">
                    <div class="action-group">
                        <a href="{{ route('admin.payments.show', $payment) }}" class="btn-action" title="View details Audit Ledger">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-3, #555B72);">No transaction records found matching filters.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($payments->hasPages())
    <div class="card-footer">
        {{ $payments->links() }}
    </div>
    @endif
</div>

@endsection