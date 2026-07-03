@extends('layouts.admin')
@section('title', 'Order Invoice Details')
@section('page-title', "Order #{$order->order_number}")

@section('content')

{{-- ── INVOICE VIEW SPECIFIC OVERRIDES ── --}}
<style>
    .invoice-layout { display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }
    
    .admin-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 12px; margin-bottom: 24px; overflow: hidden; }
    .admin-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border, #252A3A); font-weight: 700; font-size: 14px; color: var(--text-1, #E8EAF2); display: flex; align-items: center; gap: 8px; justify-content: space-between; }
    .admin-card-body { padding: 24px 20px; }

    .meta-display-box { padding: 16px; background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); border-radius: 8px; margin-bottom: 16px; }
    .meta-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 10px; }
    .meta-row:last-child { margin-bottom: 0; border-top: 1px dashed var(--border); padding-top: 10px; }
    .meta-label { color: var(--text-3, #555B72); font-weight: 500; }
    .meta-val { color: var(--text-1, #E8EAF2); font-weight: 600; }

    .form-control, .form-select { width: 100%; background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 10px 14px; border-radius: 8px; font-family: var(--sans, sans-serif); font-size: 14px; outline: none; transition: border-color 0.2s; }
    .form-control:focus, .form-select:focus { border-color: var(--accent, #5B6EF5); }
    textarea.form-control { resize: vertical; min-height: 80px; }

    .btn { width: 100%; padding: 12px; border-radius: 8px; font-family: var(--sans, sans-serif); font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; border: none; text-decoration: none; }
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-danger-outline { background: rgba(255, 92, 122, 0.05); color: var(--accent-3, #FF5C7A); border: 1px solid rgba(255, 92, 122, 0.2); }
    .btn-danger-outline:hover { background: var(--accent-3, #FF5C7A); color: #fff; border-color: transparent; }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead th { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text-3, #555B72); padding: 12px 16px; border-bottom: 1px solid var(--border, #252A3A); }
    tbody td { padding: 14px 16px; font-size: 13px; color: var(--text-2, #8B90A7); vertical-align: middle; border-bottom: 1px solid rgba(37, 42, 58, 0.3); }

    .status-badge { font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 20px; font-family: monospace; display: inline-block; }
    .status-pending { background: rgba(245, 166, 35, 0.12); color: #F5A623; }
    .status-confirmed { background: rgba(0, 212, 170, 0.12); color: #00D4AA; }
    .status-processing { background: rgba(91, 110, 245, 0.12); color: #5B6EF5; }
    .status-shipped { background: rgba(139, 144, 167, 0.12); color: #8B90A7; }
    .status-delivered { background: rgba(0, 212, 170, 0.2); color: #00E6B8; }
    .status-cancelled { background: rgba(255, 92, 122, 0.12); color: #FF5C7A; }

    @media(max-width: 991px){ .invoice-layout { grid-template-columns: 1fr; } }
</style>

<div class="invoice-layout">
    
    {{-- ── LEFT PANEL: ITEMS MATRIX LIST ── --}}
    <div class="main-col">
        <div class="admin-card">
            <div class="admin-card-header">
                <div>Items Overview Matrix</div>
                <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: left; padding-left: 20px;">Product Spec Description</th>
                            <th style="text-align: center;">Price</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: right; padding-right: 20px;">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td style="padding-left: 20px;">
                                <div style="color: var(--text-1); font-weight: 600;">{{ $item->product->name ?? 'Deleted Item' }}</div>
                                @if($item->color || $item->phoneStorage)
                                    <div style="font-size: 11px; color: var(--text-3); font-family: var(--mono); margin-top: 4px;">
                                        Variant parameters: {{ $item->color->name ?? '' }} {{ $item->phoneStorage->capacity ?? '' }}
                                    </div>
                                @endif
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
        </div>

        {{-- Shipping Profile Info --}}
        <div class="admin-card">
            <div class="admin-card-header">Delivery Profiles & Billing</div>
            <div class="admin-card-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-3); font-weight: 600; margin-bottom: 6px;">Customer Profile</div>
                    <div style="color: var(--text-1); font-weight: 600;">{{ $order->user->name ?? 'Guest Client' }}</div>
                    <div style="color: var(--text-2); font-size: 13px; margin-top: 4px;">{{ $order->user->email ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-3); font-weight: 600; margin-bottom: 6px;">Meta Indexes</div>
                    <div style="color: var(--text-2); font-size: 13px;">Placed Time: <span style="font-family: var(--mono); color: var(--text-1);">{{ $order->created_at->format('Y-m-d H:i') }}</span></div>
                    <div style="color: var(--text-2); font-size: 13px; margin-top: 4px;">Invoice Tracking ID: <span style="font-family: var(--mono); color: var(--accent-2);">{{ $order->invoice->invoice_number ?? 'Drafting' }}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT PANEL: STATUS MODIFICATION CONTROLS ── --}}
    <div class="side-col">
        
        {{-- Totals Breakdowns --}}
        <div class="admin-card">
            <div class="admin-card-header">Finances Summary</div>
            <div class="admin-card-body">
                <div class="meta-display-box">
                    <div class="meta-row">
                        <span class="meta-label">Subtotal Matrix:</span>
                        <span class="meta-val">${{ number_format($order->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</span>
                    </div>
                    @if($order->promotion)
                        <div class="meta-row">
                            <span class="meta-label">Promo Applied ({{ $order->promotion->code }}):</span>
                            <span class="meta-val" style="color: var(--accent-3);">- ${{ number_format($order->discount_amount ?? 0, 2) }}</span>
                        </div>
                    @endif
                    <div class="meta-row">
                        <span class="meta-label">Grand Gross Total:</span>
                        <span class="meta-val" style="font-size: 18px; color: var(--accent-2);">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Route Transition Operator Dropdown --}}
        <div class="admin-card">
            <div class="admin-card-header">Route Transition Operator</div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                    @csrf @method('PUT')
                    
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label">Set Stage Workflow</label>
                        <select name="status" class="form-select">
                            @foreach($statuses as $s)
                                <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Operation Progress Log Note</label>
                        <textarea name="note" class="form-control" placeholder="Optional progress notification audit message trails..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-file-shield"></i> Update Status Transition
                    </button>
                </form>
            </div>
        </div>

        {{-- Danger zone Cancel Trigger System --}}
        @if(!in_array($order->status, ['delivered', 'cancelled']))
            <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" onsubmit="return confirm('Revoke invoice processing and restore tracking item inventories stock records immediately?');">
                @csrf
                <button type="submit" class="btn btn-danger-outline">
                    <i class="fa-solid fa-ban"></i> Terminate & Reverse Order Stock
                </button>
            </form>
        @endif

    </div>

</div>

@endsection