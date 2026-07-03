@extends('layouts.admin')

@section('title', isset($promotion) ? 'Edit Campaign' : 'Create Campaign')
@section('page-title', isset($promotion) ? 'Edit Promotion' : 'New Promotion')

@section('content')

{{-- ── FORM FRAMEWORK STYLES ── --}}
<style>
    .form-layout { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }
    
    .admin-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: 12px; margin-bottom: 24px; overflow: hidden; }
    .admin-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border, #252A3A); font-weight: 700; font-size: 14px; color: var(--text-1, #E8EAF2); display: flex; align-items: center; gap: 8px; }
    .admin-card-body { padding: 24px 20px; }

    .form-group { margin-bottom: 20px; }
    .form-group-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
    .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-2, #8B90A7); margin-bottom: 8px; }
    .text-danger { color: var(--accent-3, #FF5C7A); }

    .form-control, .form-select { width: 100%; background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 10px 14px; border-radius: 8px; font-family: var(--sans, sans-serif); font-size: 14px; transition: border-color 0.2s; outline: none; }
    .form-control:focus, .form-select:focus { border-color: var(--accent, #5B6EF5); }
    .form-select option { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); }
    textarea.form-control { resize: vertical; min-height: 80px; }

    .switch-wrap { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; }
    .switch-label { font-size: 13px; font-weight: 500; color: var(--text-1, #E8EAF2); }
    .switch { position: relative; display: inline-block; width: 40px; height: 22px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--border, #252A3A); transition: .3s; border-radius: 22px; }
    .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: #fff; transition: .3s; border-radius: 50%; }
    input:checked + .slider { background-color: var(--accent, #5B6EF5); }
    input:checked + .slider:before { transform: translateX(18px); }

    .btn-group-vertical { display: flex; flex-direction: column; gap: 12px; margin-top: 20px; }
    .btn { width: 100%; padding: 12px; border-radius: 8px; font-family: var(--sans, sans-serif); font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; border: none; }
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-secondary { background: var(--bg, #0C0E14); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); text-decoration: none; }
    .btn-secondary:hover { border-color: var(--text-3, #555B72); color: #fff; }
    .btn-warning-outline { background: rgba(245, 166, 35, 0.03); color: #F5A623; border: 1px dashed rgba(245, 166, 35, 0.2); font-size: 12px; padding: 8px; margin-top: 10px; }
    .btn-warning-outline:hover { background: rgba(245, 166, 35, 0.1); }

    @media(max-width: 991px){ .form-layout { grid-template-columns: 1fr; } }
</style>

@if ($errors->any())
    <div style="background: rgba(255, 92, 122, 0.1); border: 1px solid var(--accent-3, #FF5C7A); color: #FF5C7A; padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
        @foreach ($errors->all() as $error)
            <div><i class="fa-solid fa-triangle-exclamation"></i> {{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST"
      action="{{ isset($promotion) ? route('admin.promotions.update', $promotion) : route('admin.promotions.store') }}">
    @csrf
    @if(isset($promotion)) @method('PUT') @endif

    <div class="form-layout">
        
        {{-- ── LEFT PANEL: CALCULATION CONFIGS ── --}}
        <div class="main-col">
            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-tags" style="color: var(--text-3);"></i> General Parameters
                </div>
                <div class="admin-card-body">
                    
                    <div class="form-group">
                        <label class="form-label">Campaign Public Title Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $promotion->name ?? '') }}" class="form-control" placeholder="e.g. Summer Launch Kickoff, Black Friday Deal" required>
                    </div>

                    <div class="form-group-grid">
                        <div>
                            <label class="form-label">Coupon String Code</label>
                            <input type="text" name="code" value="{{ old('code', $promotion->code ?? '') }}" class="form-control" placeholder="e.g. SUMMER50, FREESHIP" style="font-family: var(--mono); text-transform: uppercase;">
                        </div>
                        <div>
                            <label class="form-label">Discount Computation Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="percentage" @selected(old('type', $promotion->type ?? 'percentage') === 'percentage')>Percentage (%)</option>
                                <option value="fixed" @selected(old('type', $promotion->type ?? '') === 'fixed')>Fixed Amount ($)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-grid">
                        <div>
                            <label class="form-label">Discount Numeric Face Value <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="value" value="{{ old('value', $promotion->value ?? '') }}" class="form-control" style="font-family: var(--mono);" placeholder="e.g. 10 or 15.50" required>
                        </div>
                        <div>
                            <label class="form-label">Usage Structural Limit Capacity</label>
                            <input type="number" name="usage_limit" value="{{ old('usage_limit', $promotion->usage_limit ?? '') }}" class="form-control" style="font-family: var(--mono);" placeholder="Leave empty for unconstrained" min="1">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Description Summary Summary</label>
                        <textarea name="description" class="form-control" placeholder="Describe the application rules context details...">{{ old('description', $promotion->description ?? '') }}</textarea>
                    </div>

                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-sliders" style="color: var(--text-3);"></i> Threshold Constraints Matrix
                </div>
                <div class="admin-card-body">
                    <div class="form-group-grid" style="margin-bottom: 0;">
                        <div>
                            <label class="form-label">Minimum Order Requirement ($)</label>
                            <input type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', $promotion->min_order_amount ?? '') }}" class="form-control" style="font-family: var(--mono);" placeholder="0.00">
                        </div>
                        <div>
                            <label class="form-label">Maximum Ceiling Discount Cap ($)</label>
                            <input type="number" step="0.01" name="max_discount_amount" value="{{ old('max_discount_amount', $promotion->max_discount_amount ?? '') }}" class="form-control" style="font-family: var(--mono);" placeholder="No upper roof boundary">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RIGHT PANEL: PUBLISH CONTROLS ── --}}
        <div class="side-col">
            
            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-rocket" style="color: var(--accent-4);"></i> Control Parameters
                </div>
                <div class="admin-card-body">
                    
                    <div class="switch-wrap">
                        <span class="switch-label">Promotion Active</span>
                        <label class="switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', ($promotion->is_active ?? true)) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>

                    @if(isset($promotion))
                        <div style="font-size: 13px; color: var(--text-3); margin-top: 14px; border-top: 1px solid var(--border); padding-top: 14px;">
                            Current Usage: <strong style="color: var(--text-1); font-family: var(--mono);">{{ $promotion->usage_count ?? 0 }}</strong> units checked out.
                        </div>
                    @endif

                    <div class="btn-group-vertical">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> {{ isset($promotion) ? 'Update Strategy' : 'Publish Strategy' }}
                        </button>
                        <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-calendar-days" style="color: var(--text-3);"></i> Chronology Validity
                </div>
                <div class="admin-card-body">
                    <div class="form-group">
                        <label class="form-label">Activation Windows From Date</label>
                        <input type="date" name="starts_at" value="{{ old('starts_at', isset($promotion->starts_at) ? \Carbon\Carbon::parse($promotion->starts_at)->format('Y-m-d') : '') }}" class="form-control">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Expiration Deadline Date</label>
                        <input type="date" name="expires_at" value="{{ old('expires_at', isset($promotion->expires_at) ? \Carbon\Carbon::parse($promotion->expires_at)->format('Y-m-d') : '') }}" class="form-control">
                    </div>
                </div>
            </div>

            @if(isset($promotion) && ($promotion->usage_count ?? 0) > 0)
                <div class="admin-card" style="border-color: rgba(245, 166, 35, 0.15);">
                    <div class="admin-card-header" style="color: #F5A623;"><i class="fa-solid fa-arrows-rotate"></i> Ledger Counter Tool</div>
                    <div class="admin-card-body" style="padding-top: 0; padding-bottom: 16px;">
                        <a href="{{ route('admin.promotions.resetUsage', $promotion) }}" class="btn btn-warning-outline" onclick="event.preventDefault(); if(confirm('Reset current utilization logs counters back to 0 completely?')) { document.getElementById('reset-usage-form').submit(); }">
                            Reset Total Performance Counters
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</form>

@if(isset($promotion))
    <form id="reset-usage-form" action="{{ route('admin.promotions.resetUsage', $promotion) }}" method="POST" style="display:none;">@csrf @method('PATCH')</form>
@endif

@endsection