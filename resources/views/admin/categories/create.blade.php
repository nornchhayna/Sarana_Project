@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Category' : 'Create Category')
@section('page-title', isset($category) ? 'Edit Category' : 'New Category')

@push('styles')
<style>
    .form-layout { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }
    
    .admin-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 24px; overflow: hidden; }
    .admin-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border); font-weight: 700; font-size: 14px; color: var(--text-1); display: flex; align-items: center; gap: 8px; }
    .admin-card-body { padding: 24px 20px; }

    .form-group { margin-bottom: 20px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
    
    .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-2); margin-bottom: 8px; }
    .text-danger { color: var(--accent-3); }

    .form-control, .form-select { width: 100%; background: var(--bg); border: 1px solid var(--border); color: var(--text-1); padding: 10px 14px; border-radius: 8px; font-family: var(--sans); font-size: 14px; transition: border-color 0.2s; outline: none; }
    .form-control:focus, .form-select:focus { border-color: var(--accent); }
    .form-control::placeholder { color: var(--text-3); }
    textarea.form-control { resize: vertical; min-height: 100px; }
    .font-mono { font-family: var(--mono); font-size: 13px; }

    .switch-wrap { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; }
    .switch-label { font-size: 13px; font-weight: 500; color: var(--text-1); }
    .switch { position: relative; display: inline-block; width: 40px; height: 22px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--border); transition: .3s; border-radius: 22px; }
    .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: #fff; transition: .3s; border-radius: 50%; }
    input:checked + .slider { background-color: var(--accent); }
    input:checked + .slider:before { transform: translateX(18px); }

    .image-preview-wrapper { width: 100%; height: 200px; background: var(--bg); border: 2px dashed var(--border); border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-bottom: 16px; overflow: hidden; }
    .image-preview-wrapper img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .image-preview-icon { font-size: 40px; color: var(--border); }

    .btn-group-vertical { display: flex; flex-direction: column; gap: 12px; margin-top: 20px; }
    .btn { width: 100%; padding: 12px; border-radius: 8px; font-family: var(--sans); font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-primary { background: var(--accent); color: #fff; border: none; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(91, 110, 245, 0.2); }
    .btn-secondary { background: var(--bg); color: var(--text-1); border: 1px solid var(--border); text-decoration: none; }
    .btn-secondary:hover { border-color: var(--text-3); color: #fff; }

    @media(max-width: 991px){ .form-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<form method="POST"
      action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if(isset($category)) @method('PUT') @endif

    <div class="form-layout">
        
        {{-- ── LEFT COLUMN ── --}}
        <div class="main-col">
            
            <div class="admin-card">
                <div class="admin-card-header"><i class="fa-solid fa-circle-info" style="color: var(--text-3);"></i> Basic Details</div>
                <div class="admin-card-body">
                    
                    <div class="form-group">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" class="form-control" placeholder="e.g. Smartphones" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $category->slug ?? '') }}" class="form-control font-mono" placeholder="Auto-generated if blank">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" placeholder="Category description...">{{ old('description', $category->description ?? '') }}</textarea>
                    </div>

                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><i class="fa-solid fa-image" style="color: var(--text-3);"></i> Category Image</div>
                <div class="admin-card-body" style="margin-bottom: 0;">
                    <div class="image-preview-wrapper" id="image-preview-container">
                        @if(isset($category) && $category->image)
                            <img id="image-preview" src="{{ asset('storage/'.$category->image) }}" alt="Image">
                        @else
                            <i id="image-icon" class="fa-regular fa-image image-preview-icon"></i>
                            <img id="image-preview" src="" alt="Image" style="display: none;">
                        @endif
                    </div>
                    <input type="file" name="image" id="image-input" class="form-control" accept="image/*">
                </div>
            </div>

        </div>

        {{-- ── RIGHT COLUMN ── --}}
        <div class="side-col">
            
            <div class="admin-card">
                <div class="admin-card-header"><i class="fa-solid fa-rocket" style="color: var(--accent-4);"></i> Publish</div>
                <div class="admin-card-body">
                    
                    <div class="switch-wrap">
                        <span class="switch-label">Active (Visible)</span>
                        <label class="switch">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', ($category->is_active ?? true) ? '1' : '') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="btn-group-vertical">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> {{ isset($category) ? 'Update Category' : 'Save Category' }}
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><i class="fa-solid fa-folder-tree" style="color: var(--text-3);"></i> Hierarchy</div>
                <div class="admin-card-body">
                    
                    <div class="form-group">
                        <label class="form-label">Parent Category</label>
                        <select name="parent_id" class="form-select">
                            <option value="">None (Top Level)</option>
                            @foreach($parentCategories ?? [] as $parent)
                                {{-- Prevent selecting itself as a parent --}}
                                @if(!isset($category) || $category->id !== $parent->id)
                                    <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id ?? '') == $parent->id)>{{ $parent->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="form-control font-mono" placeholder="0">
                    </div>

                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('image-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('image-preview');
                const icon = document.getElementById('image-icon');
                
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                if(icon) icon.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush