@extends('layouts.admin')

@section('title', isset($color) ? 'Edit Color' : 'Create Color')
@section('page-title', isset($color) ? 'Edit Color' : 'New Color')

@section('content')

<style>
    .form-layout { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }
    
    .admin-card { background: var(--card, #1A1E2B); border: 1px solid var(--border, #252A3A); border-radius: var(--radius, 12px); margin-bottom: 24px; overflow: hidden; }
    .admin-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border, #252A3A); font-weight: 700; font-size: 14px; color: var(--text-1, #E8EAF2); display: flex; align-items: center; gap: 8px; }
    .admin-card-body { padding: 24px 20px; }

    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-2, #8B90A7); margin-bottom: 8px; }
    .text-danger { color: var(--accent-3, #FF5C7A); }

    .form-control { width: 100%; background: var(--bg, #0C0E14); border: 1px solid var(--border, #252A3A); color: var(--text-1, #E8EAF2); padding: 10px 14px; border-radius: 8px; font-family: var(--sans, sans-serif); font-size: 14px; transition: border-color 0.2s; outline: none; }
    .form-control:focus { border-color: var(--accent, #5B6EF5); }
    
    .color-input-wrapper { display: flex; gap: 12px; align-items: center; }
    .native-picker { -webkit-appearance: none; -moz-appearance: none; appearance: none; width: 44px; height: 44px; background: none; border: none; cursor: pointer; padding: 0; }
    .native-picker::-webkit-color-swatch { border-radius: 8px; border: 1px solid var(--border, #252A3A); }
    .native-picker::-moz-color-swatch { border-radius: 8px; border: 1px solid var(--border, #252A3A); }

    /* Live preview swatch */
    .color-preview {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        border: 1px solid var(--border, #252A3A);
        flex-shrink: 0;
        transition: background-color 0.2s;
    }

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
      action="{{ isset($color) ? route('admin.colors.update', $color) : route('admin.colors.store') }}">
    @csrf
    @if(isset($color)) @method('PUT') @endif

    <div class="form-layout">
        
        {{-- ── LEFT PANELS ── --}}
        <div class="main-col">
            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-palette" style="color: var(--text-3);"></i> Color Properties
                </div>
                <div class="admin-card-body">
                    
                    <div class="form-group">
                        <label class="form-label">Color Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $color->name ?? '') }}"
                            class="form-control" placeholder="e.g. Space Gray, Titanium Violet" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Hex Color Code</label>
                        <div class="color-input-wrapper">

                            {{-- Live preview box (driven by JS, not a form field) --}}
                            <div class="color-preview" id="color-preview"
                                style="background-color: {{ old('hex_code', $color->hex_code ?? '#cccccc') }};"></div>

                            {{-- Type the hex here — this is the only form field that saves --}}
                            <input type="text" name="hex_code" id="hex-text-input"
                                value="{{ old('hex_code', $color->hex_code ?? '') }}"
                                class="form-control"
                                placeholder="#FFFFFF"
                                style="font-family: monospace; text-transform: uppercase;"
                                maxlength="7">

                            {{-- Native picker as a helper only — syncs to text input --}}
                            <input type="color" id="picker-element"
                                value="{{ old('hex_code', $color->hex_code ?? '#cccccc') }}"
                                class="native-picker"
                                title="Pick a color">

                        </div>
                        @error('hex_code')
                            <p style="color: var(--accent-3, #FF5C7A); font-size: 12px; margin-top: 6px;">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- ── RIGHT CONTROL SIDEBAR ── --}}
        <div class="side-col">
            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-rocket" style="color: var(--accent-4);"></i> Control Status
                </div>
                <div class="admin-card-body">
                    
                    <div class="switch-wrap">
                        <span class="switch-label">Variant Visible</span>
                        <label class="switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', ($color->is_active ?? true)) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="btn-group-vertical">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> {{ isset($color) ? 'Update Color' : 'Save Color' }}
                        </button>
                        <a href="{{ route('admin.colors.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const textInput   = document.getElementById('hex-text-input');
    const colorPicker = document.getElementById('picker-element');
    const preview     = document.getElementById('color-preview');

    if (!textInput || !colorPicker) return;

    function updatePreview(hex) {
        if (/^#[0-9A-Fa-f]{6}$/.test(hex) && preview) {
            preview.style.backgroundColor = hex;
        }
    }

    function syncFromPicker(e) {
        const val = e.target.value.toUpperCase();
        textInput.value = val;
        updatePreview(val);
    }

    colorPicker.addEventListener('input',  syncFromPicker);
    colorPicker.addEventListener('change', syncFromPicker);

    textInput.addEventListener('input', function () {
        let val = textInput.value.trim().toUpperCase();
        if (!val.startsWith('#')) val = '#' + val;
        textInput.value = val;
        updatePreview(val);
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
            colorPicker.value = val;
        }
    });
});
</script>

@endsection