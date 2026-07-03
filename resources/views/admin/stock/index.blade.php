@extends('layouts.admin')
@section('title', 'Stock Control')
@section('page-title', 'Inventory Stock')

@section('content')

{{-- ── INVENTORY GRID & COMPONENT CSS OVERRIDES ── --}}
<style>
    .page-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap; }
    
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; padding: 9px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(91, 110, 245, 0.2); }
    .btn-secondary { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; }
    .btn-secondary:hover { border-color: var(--accent, #5B6EF5); color: var(--accent, #5B6EF5); }
    
    .filters { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
    .form-input, .form-select { background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 9px 12px; border-radius: 8px; font-size: 13px; font-family: var(--sans, sans-serif); outline: none; transition: border-color 0.2s; }
    .form-input:focus, .form-select:focus { border-color: var(--accent, #5B6EF5); }
    .form-select option { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); }

    /* Inventory Cards Grid Layout */
    .mini-summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .summary-item-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; }
    .summary-title { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600; color: var(--text-3, #555B72); margin-bottom: 4px; }
    .summary-metric { font-size: 24px; font-family: var(--mono, monospace); font-weight: 700; color: var(--text-1, #E8EAF2); }
    .summary-icon-box { width: 40px; height: 40px; border-radius: 8px; display: grid; place-items: center; font-size: 16px; }

    /* Stock Inline Inputs style */
    .inline-stock-input { background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); width: 70px; padding: 6px; border-radius: 6px; text-align: center; font-family: var(--mono, monospace); font-size: 13px; }
    .inline-stock-input:focus { border-color: var(--accent, #5B6EF5); outline: none; }
    
    .variant-pill { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-family: var(--mono, monospace); color: var(--text-2, #8B90A7); background: rgba(255,255,255,0.03); border: 1px solid var(--border, #252A3A); padding: 2px 6px; border-radius: 4px; margin-top: 4px; }

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
    .status-cancelled { background: rgba(255, 92, 122, 0.12); color: #FF5C7A; }
    .card-footer { padding: 16px 22px; border-top: 1px solid var(--border, #252A3A); }
</style>

{{-- ── INVENTORY SUMMARY BAR ── --}}
<div class="mini-summary-grid">
    <div class="summary-item-card">
        <div>
            <div class="summary-title">Total Hand Quantity</div>
            <div class="summary-metric">{{ number_format($summary['total_items']) }}</div>
        </div>
        <div class="summary-icon-box" style="background: rgba(91, 110, 245, 0.1); color: var(--accent);">
            <i class="fa-solid fa-warehouse"></i>
        </div>
    </div>
    
    <div class="summary-item-card">
        <div>
            <div class="summary-title">Low Stock SKUs</div>
            <div class="summary-metric">{{ $summary['low_stock'] }}</div>
        </div>
        <div class="summary-icon-box" style="background: rgba(245, 166, 35, 0.1); color: var(--accent-4);">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
    </div>

    <div class="summary-item-card">
        <div>
            <div class="summary-title">Out Of Stock</div>
            <div class="summary-metric" style="color: var(--accent-3);">{{ $summary['out_of_stock'] }}</div>
        </div>
        <div class="summary-icon-box" style="background: rgba(255, 92, 122, 0.1); color: var(--accent-3);">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
    </div>
</div>

{{-- ── FILTER SETTOOLBAR ── --}}
<div class="page-toolbar">
    <form class="filters" method="GET" style="width: 100%; justify-content: space-between;">
        <div style="display: flex; gap: 12px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search product name, SKU…" style="width: 240px;">
            
            <select name="stock_status" class="form-select" style="width: 150px;">
                <option value="">All Stock Levels</option>
                <option value="ok" @selected(request('stock_status') === 'ok')>Stock OK</option>
                <option value="low" @selected(request('stock_status') === 'low')>Low Stock</option>
                <option value="out" @selected(request('stock_status') === 'out')>Out of Stock</option>
            </select>
            
            <select name="product" class="form-select" style="width: 200px; max-width: 250px;">
                <option value="">Filter By Specific Product...</option>
                @foreach($products as $prod)
                    <option value="{{ $prod->id }}" @selected(request('product') == $prod->id)>{{ $prod->name }}</option>
                @endforeach
            </select>

            <button class="btn-secondary">Filter</button>
        </div>
        
        {{-- Bulk Update Form Submission Connection Trigger Button --}}
        <button type="button" onclick="document.getElementById('bulk-stock-form').submit();" class="btn-primary">
            <i class="fa-solid fa-cloud-arrow-up"></i> Save Bulk Quantities
        </button>
    </form>
</div>

{{-- ── MAIN BATCH STOCK TABLE ── --}}
<form id="bulk-stock-form" method="POST" action="{{ route('admin.stock.bulk-update') }}">
    @csrf @method('PUT')
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="padding-left: 22px;">Product Asset Target</th>
                    <th>Variant Options</th>
                    <th>Current Level</th>
                    <th>Quantity On Hand</th>
                    <th>Low Warning Threshold</th>
                    <th style="text-align: right; padding-right: 22px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $index => $stock)
                <tr>
                    <td style="padding-left: 22px;">
                        <input type="hidden" name="stocks[{{ $index }}][id]" value="{{ $stock->id }}">
                        <div style="color: var(--text-1, #E8EAF2); font-weight: 600;">{{ $stock->product->name ?? '—' }}</div>
                        <div class="variant-pill" style="font-size: 10px;">SKU: {{ $stock->product->sku ?? '—' }}</div>
                    </td>
                    <td>
                        @if($stock->color || $stock->phoneStorage)
                            <div class="variant-pill">
                                <i class="fa-solid fa-sliders" style="font-size: 10px; color: var(--text-3)"></i>
                                {{ $stock->color->name ?? '' }} {{ $stock->phoneStorage->capacity ?? '' }}
                            </div>
                        @else
                            <span style="color: var(--text-3);">Standard (No Variants)</span>
                        @endif
                    </td>
                    <td>
                        @if($stock->quantity == 0)
                            <span class="status-badge status-cancelled">Out Of Stock</span>
                        @elseif($stock->quantity <= $stock->low_stock_threshold)
                            <span class="status-badge status-pending">Low Stock Alert</span>
                        @else
                            <span class="status-badge status-paid">Healthy</span>
                        @endif
                    </td>
                    <td>
                        <input type="number" name="stocks[{{ $index }}][quantity]" value="{{ old("stocks.{$index}.quantity", $stock->quantity) }}" class="inline-stock-input" min="0" required>
                    </td>
                    <td>
                        <input type="number" name="stocks[{{ $index }}][low_stock_threshold]" value="{{ old("stocks.{$index}.low_stock_threshold", $stock->low_stock_threshold) }}" class="inline-stock-input" min="0" required>
                    </td>
                    <td style="padding-right: 22px;">
                        <div class="action-group">
                            <a href="{{ route('admin.stock.edit', $stock) }}" class="btn-action" title="Adjust Balance Logs">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-3, #555B72);">No inventory SKU tracking targets mapped.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($stocks->hasPages())
        <div class="card-footer">
            {{ $stocks->links() }}
        </div>
        @endif
    </div>
</form>

@endsection