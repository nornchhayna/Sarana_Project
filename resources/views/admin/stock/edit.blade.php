@extends('layouts.admin')
@section('title', 'Adjust Stock SKU')
@section('page-title', 'Adjust Stock Item')

@section('content')

{{-- ── ADJUSTMENT ENGINE COMPONENT STYLES ── --}}
<style>
    .form-layout { display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }
    
    .admin-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 12px; margin-bottom: 24px; overflow: hidden; }
    .admin-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border, #252A3A); font-weight: 700; font-size: 14px; color: var(--text-1, #E8EAF2); display: flex; align-items: center; gap: 8px; }
    .admin-card-body { padding: 24px 20px; }

    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-2, #8B90A7); margin-bottom: 8px; }
    
    .form-control, .form-select { width: 100%; background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 10px 14px; border-radius: 8px; font-family: var(--sans, sans-serif); font-size: 14px; transition: border-color 0.2s; outline: none; }
    .form-control:focus, .form-select:focus { border-color: var(--accent, #5B6EF5); }
    .form-select option { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); }
    textarea.form-control { resize: vertical; min-height: 100px; }

    .meta-display-box { padding: 14px; background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); border-radius: 8px; margin-bottom: 14px; }
    .meta-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; }
    .meta-row:last-child { margin-bottom: 0; }
    .meta-label { color: var(--text-3, #555B72); font-weight: 500; }
    .meta-val { color: var(--text-1, #E8EAF2); font-weight: 600; font-family: var(--mono, monospace); }

    .btn { width: 100%; padding: 12px; border-radius: 8px; font-family: var(--sans, sans-serif); font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; border: none; }
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-secondary { background: var(--bg, #0C0E14); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); text-decoration: none; }
    .btn-secondary:hover { border-color: var(--text-3, #555B72); color: #fff; }

    /* Quick adjustment tool input layout grouping */
    .adjustment-tool-group { display: grid; grid-template-columns: 110px 1fr; gap: 10px; margin-bottom: 14px; }

    @media(max-width: 991px){ .form-layout { grid-template-columns: 1fr; } }
</style>

<div class="form-layout">
    
    {{-- ── LEFT PANEL: CONFIG QUANTITY LIMITS & WARNING THRESHOLDS ── --}}
    <div class="main-col">
        <form method="POST" action="{{ route('admin.stock.update', $stock) }}">
            @csrf @method('PUT')
            
            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-sliders" style="color: var(--text-3);"></i> Base Settings Parameters
                </div>
                <div class="admin-card-body">
                    
                    <div style="font-size: 16px; font-weight: 700; color: var(--text-1); margin-bottom: 4px;">
                        {{ $stock->product->name ?? 'Product Asset' }}
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <span style="font-size: 12px; color: var(--text-3); font-family: var(--mono);">SKU: {{ $stock->product->sku ?? '—' }}</span>
                        @if($stock->color || $stock->phoneStorage)
                            <span style="font-size:12px; color: var(--accent-2); margin-left: 10px; font-family: var(--mono);">
                                [Variant: {{ $stock->color->name ?? '' }} {{ $stock->phoneStorage->capacity ?? '' }}]
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Current Quantity On-Hand</label>
                        <input type="number" name="quantity" value="{{ old('quantity', $stock->quantity) }}" class="form-control" style="font-family: var(--mono); font-weight: 700;" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Low Stock Safety Trigger Warning Threshold</label>
                        <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $stock->low_stock_threshold) }}" class="form-control" style="font-family: var(--mono);" min="0" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Audit Adjustment Log Tracking Note</label>
                        <textarea name="adjustment_note" class="form-control" placeholder="Describe context for manual changes (e.g., Physical cycle count correction, inbound damages item processing...)"></textarea>
                    </div>

                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Apply Limits</button>
                <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary">Back to Stock</a>
            </div>
        </form>
    </div>

    {{-- ── RIGHT PANEL: QUICK MATHS ACCELERATION MODIFIER ENGINE ── --}}
    <div class="side-col">
        <div class="admin-card">
            <div class="admin-card-header">
                <i class="fa-solid fa-calculator" style="color: var(--accent-4);"></i> Quick Increment Adjuster
            </div>
            <div class="admin-card-body">
                
                <div class="meta-display-box">
                    <div class="meta-row">
                        <span class="meta-label">On Hand Balance:</span>
                        <span class="meta-val">{{ $stock->quantity }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Warning Line:</span>
                        <span class="meta-val" style="color: var(--accent-4);">≤ {{ $stock->low_stock_threshold }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.stock.adjust', $stock) }}">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Select Action Modifier Type</label>
                        <div class="adjustment-tool-group">
                            <select name="type" class="form-select" required>
                                <option value="add">Add (+)</option>
                                <option value="subtract">Subtract (-)</option>
                                <option value="set">Set to (=)</option>
                            </select>
                            <input type="number" name="amount" class="form-control" style="font-family: var(--mono);" min="0" placeholder="Amount units" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Adjustment Note</label>
                        <input type="text" name="note" class="form-control" placeholder="Inbound batch ID, shelf swap...">
                    </div>

                    <button type="submit" class="btn btn-primary" style="background: var(--surface); border: 1px solid var(--border); color: var(--text-1);">
                        <i class="fa-solid fa-bolt" style="color: var(--accent-2);"></i> Execute Adjustment
                    </button>
                </form>

            </div>
        </div>
    </div>

</div>

@endsection
