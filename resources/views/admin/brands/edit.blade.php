@extends('layouts.admin')

@section('title', isset($brand) ? 'Edit Brand' : 'Create Brand')
@section('page-title', isset($brand) ? 'Edit Brand' : 'New Brand')

@section('content')

{{-- ── FORM STYLES ── --}}
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
    textarea.form-control { resize: vertical; min-height: 120px; }

    .switch-wrap { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; }
    .switch-label { font-size: 13px; font-weight: 500; color: var(--text-1, #E8EAF2); }
    .switch { position: relative; display: inline-block; width: 40px; height: 22px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--border, #252A3A); transition: .3s; border-radius: 22px; }
    .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: #fff; transition: .3s; border-radius: 50%; }
    input:checked + .slider { background-color: var(--accent, #5B6EF5); }
    input:checked + .slider:before { transform: translateX(18px); }

    .image-preview-wrapper { width: 100%; height: 200px; background: var(--bg, #0C0E14); border: 2px dashed var(--border, #252A3A); border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-bottom: 16px; overflow: hidden; }
    .image-preview-wrapper img { max-width: 100%; max-height: 100%; object-fit: contain; padding: 10px; }
    .image-preview-icon { font-size: 40px; color: var(--border, #252A3A); }

    .btn-group-vertical { display: flex; flex-direction: column; gap: 12px; margin-top: 20px; }
    .btn { width: 100%; padding: 12px; border-radius: 8px; font-family: var(--sans, sans-serif); font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-primary { background: var(--accent, #5B6EF5); color: #fff; border: none; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(91, 110, 245, 0.2); }
    .btn-secondary { background: var(--bg, #0C0E14); color: var(--text-1, #E8EAF2); border: 1px solid var(--border, #252A3A); text-decoration: none; }
    .btn-secondary:hover { border-color: var(--text-3, #555B72); color: #fff; }

    @media(max-width: 991px){ .form-layout { grid-template-columns: 1fr; } }
</style>

<form method="POST"
      action="{{ isset($brand) ? route('admin.brands.update', $brand) : route('admin.brands.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if(isset($brand)) @method('PUT') @endif

    <div class="form-layout">
        
        {{-- ── LEFT MAIN FORM PANEL ── --}}
        <div class="main-col">
            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-circle-info" style="color: var(--text-3);"></i> Brand Details
                </div>
                <div class="admin-card-body">
                    
                    <div class="form-group">
                        <label class="form-label">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $brand->name ?? '') }}" class="form-control" placeholder="e.g. Samsung, Apple" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" placeholder="Describe the brand profile or origin summary...">{{ old('description', $brand->description ?? '') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── RIGHT CONTROLS PANEL ── --}}
        <div class="side-col">
            
            {{-- Action & Status --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-rocket" style="color: var(--accent-4);"></i> Publish Status
                </div>
                <div class="admin-card-body">
                    
                    <div class="switch-wrap">
                        <span class="switch-label">Active Listing</span>
                        <label class="switch">
                            {{-- Explicitly casting to hidden input first to protect boolean verification parameters --}}
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', ($brand->is_active ?? true)) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="btn-group-vertical">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> {{ isset($brand) ? 'Update Brand' : 'Save Brand' }}
                        </button>
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>

            {{-- Asset Logo upload --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <i class="fa-solid fa-image" style="color: var(--text-3);"></i> Brand Logo
                </div>
                <div class="admin-card-body">
                    <div class="image-preview-wrapper">
                        @if(isset($brand) && $brand->logo)
                            <img id="logo-preview" src="{{ asset('storage/'.$brand->logo) }}" alt="Logo">
                        @else
                            <i id="logo-icon" class="fa-regular fa-bookmark image-preview-icon"></i>
                            <img id="logo-preview" src="" alt="Logo" style="display: none;">
                        @endif
                    </div>
                    <input type="file" name="logo" id="logo-input" class="form-control" accept="image/*">
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('logo-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('logo-preview');
                const icon = document.getElementById('logo-icon');
                
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                if(icon) icon.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush