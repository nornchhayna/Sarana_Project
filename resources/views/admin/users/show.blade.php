@extends('layouts.admin')
@section('title', 'User Profile File')
@section('page-title', 'User Profile Dossier')

@section('content')

{{-- ── PROFILE SUMMARY DASHBOARD OVERRIDES ── --}}
<style>
    .profile-layout { display: grid; grid-template-columns: 340px 1fr; gap: 24px; align-items: start; }
    
    .admin-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 12px; margin-bottom: 24px; overflow: hidden; }
    .admin-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border, #252A3A); font-weight: 700; font-size: 14px; color: var(--text-1, #E8EAF2); display: flex; align-items: center; gap: 8px; justify-content: space-between; }
    .admin-card-body { padding: 20px; }

    .telemetry-summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
    .telemetry-metric-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 10px; padding: 14px; }
    .telemetry-title { font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; color: var(--text-3, #555B72); margin-bottom: 4px; }
    .telemetry-val { font-size: 18px; font-family: var(--mono, monospace); font-weight: 700; color: var(--text-1); }

    .info-list-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(37, 42, 58, 0.4); font-size: 13px; }
    .info-list-row:last-child { border-bottom: none; padding-bottom: 0; }
    .info-label { color: var(--text-3, #555B72); font-weight: 500; }
    .info-val { color: var(--text-1, #E8EAF2); font-weight: 600; }

    .btn { width: 100%; padding: 11px; border-radius: 8px; font-family: var(--sans, sans-serif); font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: none; text-decoration: none; }
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; margin-bottom: 10px; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-danger-outline { background: rgba(255, 92, 122, 0.04); color: var(--accent-3, #FF5C7A); border: 1px solid rgba(255, 92, 122, 0.15); margin-bottom: 10px; }
    .btn-danger-outline:hover { background: var(--accent-3, #FF5C7A); color: #fff; }
    .btn-secondary { background: var(--bg, #0C0E14); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead th { font-size: 10px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; color: var(--text-3, #555B72); padding: 10px 14px; border-bottom: 1px solid var(--border, #252A3A); text-align: left; }
    tbody td { padding: 12px 14px; font-size: 13px; color: var(--text-2, #8B90A7); vertical-align: middle; border-bottom: 1px solid rgba(37, 42, 58, 0.3); }

    .status-badge { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 20px; font-family: monospace; display: inline-block; }
    .status-paid { background: rgba(0, 212, 170, 0.12); color: #00D4AA; }
    .status-pending { background: rgba(245, 166, 35, 0.12); color: #F5A623; }

    @media(max-width: 991px){ .profile-layout { grid-template-columns: 1fr; } .telemetry-summary-grid { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="profile-layout">
    
    {{-- ── LEFT DOSSIER SIDEBAR CARD ── --}}
    <div class="side-col">
        <div class="admin-card">
            <div class="admin-card-body" style="text-align: center; padding-top: 28px;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--border, #252A3A); display: grid; place-items: center; color: var(--accent-2, #00D4AA); font-size: 28px; font-weight: 700; margin: 0 auto 16px auto; border: 2px solid var(--border);">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <h3 style="color: var(--text-1); margin: 0 0 4px 0; font-size: 18px; font-weight: 700;">{{ $user->name }}</h3>
                <div style="font-family: var(--mono); font-size: 11px; text-transform: uppercase; color: var(--text-3); letter-spacing: 0.5px;">{{ $user->role }}</div>
                
                <div style="margin-top: 24px; text-align: left; border-top: 1px dashed var(--border); padding-top: 16px;">
                    <div class="info-list-row"><span class="info-label">Email:</span><span class="info-val" style="font-family: var(--mono); font-size: 12px;">{{ $user->email }}</span></div>
                    <div class="info-list-row"><span class="info-label">Phone:</span><span class="info-val" style="font-family: var(--mono);">{{ $user->phone ?: '—' }}</span></div>
                    <div class="info-list-row"><span class="info-label">Gender:</span><span class="info-val" style="text-transform: capitalize;">{{ $user->gender ?: '—' }}</span></div>
                    <div class="info-list-row"><span class="info-label">City:</span><span class="info-val">{{ $user->city ?: '—' }}</span></div>
                    <div class="info-list-row"><span class="info-label">Province:</span><span class="info-val">{{ $user->province ?: '—' }}</span></div>
                    <div class="info-list-row" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                        <span class="info-label">Street Address:</span>
                        <span style="color: var(--text-2); font-size: 12px; line-height: 1.4;">{{ $user->address ?: 'No registered default billing address recorded.' }}</span>
                    </div>
                </div>

                <div style="margin-top: 24px; border-top: 1px dashed var(--border); padding-top: 16px;">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary"><i class="fa-solid fa-user-pen"></i> Edit Profile Fields</a>
                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggleStatus', $user) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-danger-outline">
                                <i class="fa-solid fa-power-off"></i> {{ $user->is_active ? 'Suspend Account' : 'Reactivate Account' }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Registry Directory</a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT WORKFLOW METRIC GRID PANELS ── --}}
    <div class="main-col">
        
        {{-- Telemetry Dashboard Values Grid --}}
        <div class="telemetry-summary-grid">
            <div class="telemetry-metric-card">
                <div class="telemetry-title">Order Ledger</div>
                <div class="telemetry-val">{{ $orderStats['total'] }} items</div>
            </div>
            <div class="telemetry-metric-card">
                <div class="telemetry-title">Total Gross Volume</div>
                <div class="telemetry-val" style="color: var(--accent-2);">${{ number_format($orderStats['spent'], 2) }}</div>
            </div>
            <div class="telemetry-metric-card">
                <div class="telemetry-title">Pending Escrow</div>
                <div class="telemetry-val" style="color: var(--accent-4);">{{ $orderStats['pending'] }} open</div>
            </div>
            <div class="telemetry-metric-card">
                <div class="telemetry-title">Delivered Items</div>
                <div class="telemetry-val">{{ $orderStats['delivered'] }} total</div>
            </div>
        </div>

        {{-- Orders purchase trails logs panel --}}
        <div class="admin-card">
            <div class="admin-card-header">Recent Historical Purchase Invoices</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Execution Date</th>
                            <th>Status Flow</th>
                            <th>Line Count</th>
                            <th>Statement Gross</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->orders->take(8) as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" style="font-family:var(--mono); font-weight:700; color:var(--accent); text-decoration:none;">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td style="font-family: var(--mono); font-size:12px;">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="status-badge" style="background: rgba(255,255,255,0.03); color: var(--text-2); border: 1px solid var(--border);">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td style="font-family: var(--mono);">{{ $order->items->count() }} units</td>
                            <td style="font-family: var(--mono); font-weight:700; color:var(--text-1);">${{ number_format($order->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center; color:var(--text-3); padding: 20px;">No transaction records registered.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Feedback Review Activity Logs --}}
        <div class="admin-card">
            <div class="admin-card-header">Submitted Product Feedback Metrics</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Target Asset</th>
                            <th>Score</th>
                            <th>Summary Heading Title</th>
                            <th>Placed Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->reviews->take(5) as $review)
                        <tr>
                            <td style="font-weight:600; color:var(--text-1);">{{ $review->product->name ?? 'Deleted Item Reference' }}</td>
                            <td style="color:var(--accent-4); font-family:var(--mono); font-weight:bold;">{{ $review->rating }} / 5</td>
                            <td><span style="color:var(--text-2);">{{ $review->title }}</span></td>
                            <td style="font-family: var(--mono); font-size:11px;">{{ $review->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center; color:var(--text-3); padding: 20px;">No public customer reviews submitted.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@endsection