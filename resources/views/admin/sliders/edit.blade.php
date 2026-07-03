@extends('layouts.admin')

@section('title', 'Edit Slider')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Edit Slider</h1>
    <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $slider->title) }}">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="subtitle" class="form-label">Subtitle</label>
                <textarea name="subtitle" id="subtitle" rows="3" class="form-control @error('subtitle') is-invalid @enderror">{{ old('subtitle', $slider->subtitle) }}</textarea>
                @error('subtitle')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Current Image</label>
                <div>
                    <img src="{{ Storage::url($slider->image) }}" alt="{{ $slider->title }}" class="img-thumbnail" style="max-height: 120px;">
                </div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Replace Image</label>
                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                <small class="text-muted">Max size: 4MB. Leave empty to keep current image.</small>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="link" class="form-label">Link URL</label>
                <input type="url" name="link" id="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $slider->link) }}" placeholder="https://example.com">
                @error('link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="button_text" class="form-label">Button Text</label>
                <input type="text" name="button_text" id="button_text" class="form-control @error('button_text') is-invalid @enderror" value="{{ old('button_text', $slider->button_text) }}">
                @error('button_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="sort_order" class="form-label">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $slider->sort_order) }}" min="0" max="255">
                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $slider->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">Active</label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection