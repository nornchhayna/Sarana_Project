@extends('layouts.admin')

@section('title', isset($model) ? 'Edit Model' : 'Create Model')
@section('page-title', isset($model) ? 'Edit Model' : 'New Model')

@section('content')

{{-- ── FORM CONTROL STYLES ── --}}
<style>
    .form-layout { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }
    
    .admin-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: var(--radius, 12px); margin-bottom: 24px; overflow: hidden; }
    .admin-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border, #252A3A); font-weight: 700; font-size: 14px; color: var(--text-1, #E8EAF2); display: flex; align-items: center; gap: 8px; }
    .admin-card-body { padding: 24px 20px; }

    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-2, #8B90A7); margin-bottom: 8px; }
    .text-danger { color: var(--accent-3, #FF5C7A); }

    .form-control, .form-select { width: 100%; background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 10px 14px; border-radius: 8px; font-family: var(--sans, sans-serif); font-size: 14px; transition: border-color 0.2s; outline: none; }
    .form-control:focus, .form-select:focus { border-color: var(--accent, #5B6EF5); }
    .form-select option { background: var(--card, #1A1E2B); color: var(--text-1, #E8EAF2); }
    textarea.form-control { resize: vertical; min-height: 120px; }

    .switch-wrap { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; }
    .switch-label { font-size: 13px; font-weight: 500; color: var(--text-1, #E8EAF2); }
    .switch { position: relative; display: inline-block; width: 40px; height: 22px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--border, #252A3A); transition: .3s; border-radius: 22px; }
    .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: #fff; transition: .3s; border-radius: 50%; }
    input:checked + .slider { background-color: var(--accent, #5B6EF5); }
    input:checked + .slider:before { transform: translateX(18px); }

    .btn-group-vertical { display: flex; flex-direction: column; gap: 12px; margin-top: 20px; }
    .btn { width: 100%; padding: 12px; border-radius: 8px; font-family: var(--sans, sans-serif); font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; border: none; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(91, 110, 245, 0.2); }
    .btn-secondary { background: var(--bg, #0C0E14); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); text-decoration: none; }
    .btn-secondary:hover { border-color: var(--text-3, #555B72); color: #fff; }

    @media(max-width: 991px){ .form-layout { grid-template-columns: 1fr; } }
</style>

<form method="POST"
      action="{{ isset($model) ? route('admin.models.update', $model) : route('admin.models.store') }}">
    @csrf
    @if(isset($model)) @method('PUT') @endif

    <div class="form-layout">
        
        {{-- ── LEFT PANEL ── --}}
        <div class="main-col">
            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-mobile" style="color: var(--text-3);"></i> Configuration Specs
                </div>
                <div class="admin-card-body">
                    
                    <div class="form-group">
                        <label class="form-label">Model Name Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $model->name ?? '') }}" class="form-control" placeholder="e.g. iPhone 15 Pro Max, Galaxy S24 Ultra" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Description Notes</label>
                        <textarea name="description" class="form-control" placeholder="Optional notes regarding technical variations or release status details...">{{ old('description', $model->description ?? '') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── RIGHT CONTROLS PANEL ── --}}
        <div class="side-col">
            
            {{-- Publish Operations --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-rocket" style="color: var(--accent-4);"></i> Action Publish
                </div>
                <div class="admin-card-body">
                    
                    <div class="switch-wrap">
                        <span class="switch-label">Active Status</span>
                        <label class="switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', ($model->is_active ?? true)) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="btn-group-vertical">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> {{ isset($model) ? 'Update Model' : 'Save Model' }}
                        </button>
                        <a href="{{ route('admin.models.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>

            {{-- Brand Association Assignment Dropdown Selection --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-bookmark" style="color: var(--text-3);"></i> Brand Assignment
                </div>
                <div class="admin-card-body">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Parent Brand Parent <span class="text-danger">*</span></label>
                        <select name="brand_id" class="form-select" required>
                            <option value="">Select Target Brand...</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(old('brand_id', $model->brand_id ?? '') == $brand->id)>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection