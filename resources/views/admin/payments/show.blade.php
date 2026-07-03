@extends('layouts.admin')
@section('title', 'Ledger Transaction Audit')
@section('page-title', 'Transaction Statement')

@section('content')

{{-- ── PROFILE LEDGER CARD COMPONENT DESIGN ── --}}
<style>
    .ledger-layout { display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }
    
    .admin-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 12px; margin-bottom: 24px; overflow: hidden; }
    .admin-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border, #252A3A); font-weight: 700; font-size: 14px; color: var(--text-1, #E8EAF2); display: flex; align-items: center; gap: 8px; justify-content: space-between; }
    .admin-card-body { padding: 24px 20px; }

    .meta-display-box { padding: 16px; background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); border-radius: 8px; margin-bottom: 16px; }
    .meta-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 12px; }
    .meta-row:last-child { margin-bottom: 0; border-top: 1px dashed var(--border); padding-top: 12px; margin-top: 12px; }
    .meta-label { color: var(--text-3, #555B72); font-weight: 500; }
    .meta-val { color: var(--text-1, #E8EAF2); font-weight: 600; font-family: var(--mono, monospace); }

    .form-control { width: 100%; background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 10px 14px; border-radius: 8px; font-family: var(--sans, sans-serif); font-size: 14px; outline: none; margin-bottom: 14px; }
    .form-control::placeholder { color: var(--text-3); }

    .btn { width: 100%; padding: 12px; border-radius: 8px; font-family: var(--sans, sans-serif); font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; border: none; text-decoration: none; }
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; margin-bottom: 12px; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-danger-outline { background: rgba(255, 92, 122, 0.04); color: var(--accent-3, #FF5C7A); border: 1px solid rgba(255, 92, 122, 0.15); margin-bottom: 12px; }
    .btn-danger-outline:hover { background: var(--accent-3, #FF5C7A); color: #fff; border-color: transparent; }
    .btn-secondary { background: var(--bg, #0C0E14); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); }
    .btn-secondary:hover { border-color: var(--text-3, #555B72); }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead th { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text-3, #555B72); padding: 12px 16px; border-bottom: 1px solid var(--border, #252A3A); }
    tbody td { padding: 14px 16px; font-size: 13px; color: var(--text-2, #8B90A7); vertical-align: middle; border-bottom: 1px solid rgba(37, 42, 58, 0.3); }

    .status-badge { font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 20px; font-family: monospace; display: inline-block; }
    .status-paid { background: rgba(0, 212, 170, 0.12); color: #00D4AA; }
    .status-pending { background: rgba(245, 166, 35, 0.12); color: #F5A623; }
    .status-refunded { background: rgba(91, 110, 245, 0.12); color: #5B6EF5; }
    .status-failed { background: rgba(255, 92, 122, 0.12); color: #FF5C7A; }

    @media(max-width: 991px){ .ledger-layout { grid-template-columns: 1fr; } }
</style>

<div class="ledger-layout">
    
    {{-- ── LEFT PANEL: INVOICED REVENUE PURCHASE BREAKDOWNS ── --}}
    <div class="main-col">
        <div class="admin-card">
            <div class="admin-card-header">
                <div>Items Snapshot Statement</div>
                <span class="status-badge status-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span>
            </div>
            @if($payment->order && $payment->order->items->count() > 0)
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
                            @foreach($payment->order->items as $item)
                            <tr>
                                <td style="padding-left: 20px;">
                                    <div style="color: var(--text-1); font-weight: 600;">{{ $item->product->name ?? 'Deleted Item Reference' }}</div>
                                    <div style="font-size: 11px; color: var(--text-3); font-family: var(--mono); margin-top: 3px;">SKU Variant Track ID: {{ $item->product->sku ?? '—' }}</div>
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
                    No explicit order tracking dependencies linked to this execution card.
                </div>
            @endif
        </div>

        {{-- Payment profile account identification metadata links --}}
        <div class="admin-card">
            <div class="admin-card-header">Client & Settlement Properties</div>
            <div class="admin-card-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-3); font-weight: 600; margin-bottom: 6px;">Payer Customer Account</div>
                    <div style="color: var(--text-1); font-weight: 600;">{{ $payment->order->user->name ?? 'Guest Client' }}</div>
                    <div style="color: var(--text-2); font-size: 13px; margin-top: 4px;">Mail: {{ $payment->order->user->email ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-3); font-weight: 600; margin-bottom: 6px;">Order Connections</div>
                    @if($payment->order)
                        <div style="color: var(--text-2); font-size: 13px;">Order Tracker Base: 
                            <a href="{{ route('admin.orders.show', $payment->order) }}" style="font-family: var(--mono); color: var(--accent); text-decoration: none; font-weight: bold;">
                                #{{ $payment->order->order_number }}
                            </a>
                        </div>
                        <div style="color: var(--text-2); font-size: 13px; margin-top: 4px;">Workflow Phase: <span style="text-transform: uppercase; font-size: 11px; font-weight: bold; color: var(--text-1);">{{ $payment->order->status }}</span></div>
                    @else
                        <div style="color: var(--text-3); font-size: 13px;">No relative order metadata binding found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT PANEL: AUDIT VALUES & PROCESSING ACTIONS ── --}}
    <div class="side-col">
        <div class="admin-card">
            <div class="admin-card-header">Ledger Parameters</div>
            <div class="admin-card-body">
                <div class="meta-display-box">
                    <div class="meta-row">
                        <span class="meta-label">Reference ID:</span>
                        <span class="meta-val" style="color: var(--text-2);">{{ $payment->transaction_id ?: 'MANUAL_DEPOSIT' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Method Channel:</span>
                        <span class="meta-val">{{ $payment->method ?? 'COD' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Ingestion Date:</span>
                        <span class="meta-val">{{ $payment->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @if($payment->paid_at)
                        <div class="meta-row">
                            <span class="meta-label">Captured Date:</span>
                            <span class="meta-val" style="color: var(--accent-2);">{{ \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') }}</span>
                        </div>
                    @endif
                    <div class="meta-row">
                        <span class="meta-label">Total Volume:</span>
                        <span class="meta-val" style="font-size: 18px; color: var(--accent-2);">${{ number_format($payment->amount, 2) }}</span>
                    </div>
                </div>

                {{-- Conditional Processing Operations Base Box --}}
                @if($payment->status === 'pending')
                    
                    {{-- Approve Operation Action Trigger --}}
                    <form method="POST" action="{{ route('admin.payments.approve', $payment) }}" style="margin-bottom: 12px;">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-file-circle-check"></i> Authorize & Capture Payment
                        </button>
                    </form>

                    {{-- Reject Operation Action Trigger --}}
                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                        @csrf @method('PUT')
                        <input type="text" name="reason" class="form-control" placeholder="Optional rationale details note statement...">
                        <button type="submit" class="btn btn-danger-outline">
                            <i class="fa-solid fa-file-circle-xmark"></i> Refuse/Reject Processing
                        </button>
                    </form>

                @elseif($payment->status === 'paid')
                    
                    {{-- Refund Operation Action Trigger --}}
                    <form method="POST" action="{{ route('admin.payments.refund', $payment) }}" onsubmit="return confirm('Execute automated payment volume reversal ledger item and terminate linked orders processing stage?')">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-danger-outline">
                            <i class="fa-solid fa-rotate-left"></i> Process Reversal Refund
                        </button>
                    </form>

                @endif

                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-backward"></i> Transactions Ledger
                </a>

            </div>
        </div>
    </div>

</div>

@endsection