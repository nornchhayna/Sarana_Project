@extends('layouts.admin')
@section('title', 'Invoices')
@section('page-title', 'Billing Invoices')

@section('content')

{{-- ── INVOICE DASHBOARD SPECIFIC OVERRIDES ── --}}
<style>
    .page-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap; }
    
    .btn-secondary { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-secondary:hover { border-color: var(--accent, #5B6EF5); color: var(--accent, #5B6EF5); }
    
    .filters { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
    .form-input, .form-select { background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 9px 12px; border-radius: 8px; font-size: 13px; font-family: var(--sans, sans-serif); outline: none; transition: border-color 0.2s; }
    .form-input:focus, .form-select:focus { border-color: var(--accent, #5B6EF5); }
    .form-select option { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); }

    .invoice-mono-text { font-family: var(--mono, monospace); font-size: 12px; color: var(--text-3, #555B72); }
    .invoice-num-link { color: var(--text-1, #E8EAF2); text-decoration: none; font-weight: 700; font-family: var(--mono, monospace); }
    .invoice-num-link:hover { color: var(--accent, #5B6EF5); }

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
    .status-issued { background: rgba(91, 110, 245, 0.12); color: #5B6EF5; }
    .status-draft { background: rgba(139, 144, 167, 0.12); color: #8B90A7; }
    .status-void { background: rgba(255, 92, 122, 0.12); color: #FF5C7A; }
    .card-footer { padding: 16px 22px; border-top: 1px solid var(--border, #252A3A); }
</style>

{{-- ── FILTER SETTOOLBAR ── --}}
<div class="page-toolbar">
    <form class="filters" method="GET" style="width: 100%;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search Invoice #, Order #…" style="width: 240px;">
        
        <select name="status" class="form-select" style="width: 140px;">
            <option value="">All Statuses</option>
            <option value="paid" @selected(request('status') === 'paid')>Paid</option>
            <option value="issued" @selected(request('status') === 'issued')>Issued</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
            <option value="void" @selected(request('status') === 'void')>Void</option>
        </select>

        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input" title="Issued From">
        <span style="color: var(--text-3, #555B72);">to</span>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input" title="Issued To">
        
        <button class="btn-secondary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
            <a href="{{ route('admin.invoices.index') }}" class="btn-secondary" style="border-color: transparent;">Reset</a>
        @endif
    </form>
</div>

{{-- ── MAIN INVOICES LIST TABLE ── --}}
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="padding-left: 22px;">Invoice Number</th>
                <th>Order Reference</th>
                <th>Customer Name</th>
                <th>Issued Date</th>
                <th>Due Date</th>
                <th>Status</th>
                <th style="text-align: right;">Amount Due</th>
                <th style="text-align: right; padding-right: 22px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td style="padding-left: 22px;">
                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="invoice-num-link">
                        {{ $invoice->invoice_number }}
                    </a>
                </td>
                <td>
                    @if($invoice->order)
                        <a href="{{ route('admin.orders.show', $invoice->order) }}" class="invoice-num-link" style="color: var(--accent, #5B6EF5);">
                            #{{ $invoice->order->order_number }}
                        </a>
                    @else
                        <span style="color: var(--text-3);">—</span>
                    @endif
                </td>
                <td style="color: var(--text-1, #E8EAF2); font-weight: 500;">
                    {{ $invoice->order->user->name ?? 'Guest Client' }}
                </td>
                <td class="invoice-mono-text">
                    {{ \Carbon\Carbon::parse($invoice->issued_at)->format('Y-m-d') }}
                </td>
                <td class="invoice-mono-text">
                    {{ $invoice->due_at ? \Carbon\Carbon::parse($invoice->due_at)->format('Y-m-d') : '—' }}
                </td>
                <td>
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </td>
                <td style="font-family: var(--mono); font-weight: 700; color: var(--text-1); text-align: right;">
                    ${{ number_format($invoice->order->total ?? 0, 2) }}
                </td>
                <td style="padding-right: 22px;">
                    <div class="action-group">
                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn-action" title="View Detail Ledger">
                            <i class="fa-solid fa-file-invoice"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-3, #555B72);">No invoice records found matching filters.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($invoices->hasPages())
    <div class="card-footer">
        {{ $invoices->links() }}
    </div>
    @endif
</div>

@endsection