@extends('layouts.admin')
@section('title', 'Invoice Statement Audit')
@section('page-title', 'Statement View')

@section('content')

{{-- ── INVOICE SHEET COMPONENT DESIGN ── --}}
<style>
    .invoice-container { display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }
    
    .admin-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 12px; margin-bottom: 24px; overflow: hidden; }
    .admin-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border, #252A3A); font-weight: 700; font-size: 14px; color: var(--text-1, #E8EAF2); display: flex; align-items: center; gap: 8px; justify-content: space-between; }
    .admin-card-body { padding: 24px 20px; }

    .meta-display-box { padding: 16px; background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); border-radius: 8px; margin-bottom: 16px; }
    .meta-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 12px; }
    .meta-row:last-child { margin-bottom: 0; border-top: 1px dashed var(--border); padding-top: 12px; margin-top: 12px; }
    .meta-label { color: var(--text-3, #555B72); font-weight: 500; }
    .meta-val { color: var(--text-1, #E8EAF2); font-weight: 600; font-family: var(--mono, monospace); }

    .btn { width: 100%; padding: 12px; border-radius: 8px; font-family: var(--sans, sans-serif); font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; border: none; text-decoration: none; margin-bottom: 12px; }
    .btn-secondary { background: var(--bg, #0C0E14); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); }
    .btn-secondary:hover { border-color: var(--text-3, #555B72); }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead th { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text-3, #555B72); padding: 12px 16px; border-bottom: 1px solid var(--border, #252A3A); }
    tbody td { padding: 14px 16px; font-size: 13px; color: var(--text-2, #8B90A7); vertical-align: middle; border-bottom: 1px solid rgba(37, 42, 58, 0.3); }

    .status-badge { font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 20px; font-family: monospace; display: inline-block; }
    .status-paid { background: rgba(0, 212, 170, 0.12); color: #00D4AA; }
    .status-issued { background: rgba(91, 110, 245, 0.12); color: #5B6EF5; }
    .status-draft { background: rgba(139, 144, 167, 0.12); color: #8B90A7; }
    .status-void { background: rgba(255, 92, 122, 0.12); color: #FF5C7A; }

    @media(max-width: 991px){ .invoice-container { grid-template-columns: 1fr; } }
</style>

<div class="invoice-container">
    
    {{-- ── LEFT PANEL: INVOICED REVENUE PURCHASE BREAKDOWNS ── --}}
    <div class="main-col">
        <div class="admin-card">
            <div class="admin-card-header">
                <div>Items Breakdown Statement</div>
                <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
            </div>
            @if($invoice->order && $invoice->order->items->count() > 0)
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="text-align: left; padding-left: 20px;">Product Asset Target</th>
                                <th style="text-align: center;">Unit Settlement Price</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: right; padding-right: 20px;">Line Gross Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->order->items as $item)
                            <tr>
                                <td style="padding-left: 20px;">
                                    <div style="color: var(--text-1); font-weight: 600;">{{ $item->product->name ?? 'Deleted Item Reference' }}</div>
                                    <div style="font-size: 11px; color: var(--text-3); font-family: var(--mono); margin-top: 3px;">SKU Track ID: {{ $item->product->sku ?? '—' }}</div>
                                </td>
                                <td style="text-align: center; font-family: var(--mono);">${{ number_format($item->price, 2) }}</td>
                                <td style="text-align: center; font-family: var(--mono); font-weight: 600;">{{ $item->quantity }}</td>
                                <td style="text-align: right; padding-right: 20px; font-family: var(--mono); color: var(--text-1); font-weight: 700;">
                                    ${{ number_format($item->price * $item->quantity, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 30px; text-align: center; color: var(--text-3);">
                    No explicit order data linked to this invoice sheet wrapper.
                </div>
            @endif
        </div>

        {{-- Metadata ledger notes info --}}
        @if($invoice->notes)
            <div class="admin-card">
                <div class="admin-card-header">Internal Audit Memo</div>
                <div class="admin-card-body" style="color: var(--text-2); font-size: 14px; line-height: 1.6;">
                    {{ $invoice->notes }}
                </div>
            </div>
        @endif
    </div>

    {{-- ── RIGHT PANEL: BILLING SUMMARY LEDGER ── --}}
    <div class="side-col">
        <div class="admin-card">
            <div class="admin-card-header">Billing Parameters</div>
            <div class="admin-card-body">
                <div class="meta-display-box">
                    <div class="meta-row">
                        <span class="meta-label">Invoice Number:</span>
                        <span class="meta-val" style="color: var(--accent-2);">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Customer Account:</span>
                        <span class="meta-val" style="font-family: var(--sans); text-transform: none;">{{ $invoice->order->user->name ?? 'Guest Client' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Order Link:</span>
                        <span class="meta-val">
                            @if($invoice->order)
                                <a href="{{ route('admin.orders.show', $invoice->order) }}" style="color: var(--accent); text-decoration: none; font-weight: bold;">
                                    #{{ $invoice->order->order_number }}
                                </a>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Issued Time:</span>
                        <span class="meta-val">{{ \Carbon\Carbon::parse($invoice->issued_at)->format('Y-m-d') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Due Date Limit:</span>
                        <span class="meta-val" style="color: var(--accent-4);">{{ $invoice->due_at ? \Carbon\Carbon::parse($invoice->due_at)->format('Y-m-d') : '—' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Total Volume Due:</span>
                        <span class="meta-val" style="font-size: 18px; color: var(--accent-2);">${{ number_format($invoice->order->total ?? 0, 2) }}</span>
                    </div>
                </div>

                <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-backward"></i> Back to Invoices
                </a>

            </div>
        </div>
    </div>

</div>

@endsection