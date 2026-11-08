@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Category' : 'Create Category')
@section('page-title', isset($category) ? 'Edit Category' : 'New Category')

@section('content')

<form method="POST"
      action="{{ isset($category)
            ? route('admin.categories.update', $category)
            : route('admin.categories.store') }}"
      enctype="multipart/form-data">

    @csrf

    @if(isset($category))
        @method('PUT')
    @endif

    <div class="row">

        {{-- =========================
             LEFT COLUMN
        ========================== --}}
        <div class="col-lg-8">

            {{-- Basic Details --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-1"></i>
                        Basic Details
                    </h3>
                </div>

                <div class="card-body">

                    {{-- Category Name --}}
                    <div class="form-group">
                        <label for="name">
                            Category Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $category->name ?? '') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="e.g. Smartphones"
                            required
                        >

                        @error('name')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Slug --}}
                    <div class="form-group">
                        <label for="slug">Slug</label>

                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            value="{{ old('slug', $category->slug ?? '') }}"
                            class="form-control @error('slug') is-invalid @enderror"
                            placeholder="Auto-generated if blank"
                        >

                        @error('slug')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="form-group mb-0">
                        <label for="description">Description</label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Category description..."
                        >{{ old('description', $category->description ?? '') }}</textarea>

                        @error('description')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                </div>
            </div>


            {{-- Category Image --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-image mr-1"></i>
                        Category Image
                    </h3>
                </div>

                <div class="card-body">

                    {{-- Image Preview --}}
                    <div
                        id="image-preview-container"
                        class="border rounded d-flex align-items-center justify-content-center mb-3"
                        style="height: 220px; background-color: #f8f9fa; overflow: hidden;"
                    >

                        @if(isset($category) && $category->image)

                            <img
                                id="image-preview"
                                src="{{ asset('storage/' . $category->image) }}"
                                alt="{{ $category->name }}"
                                style="max-width: 100%; max-height: 100%; object-fit: contain;"
                            >

                            <i
                                id="image-icon"
                                class="far fa-image text-muted"
                                style="font-size: 45px; display:none;"
                            ></i>

                        @else

                            <i
                                id="image-icon"
                                class="far fa-image text-muted"
                                style="font-size: 45px;"
                            ></i>

                            <img
                                id="image-preview"
                                src=""
                                alt="Image Preview"
                                style="
                                    display:none;
                                    max-width:100%;
                                    max-height:100%;
                                    object-fit:contain;
                                "
                            >

                        @endif

                    </div>


                    {{-- File Input --}}
                    <div class="form-group mb-0">

                        <label for="image">Category Image</label>

                        <div class="custom-file">

                            <input
                                type="file"
                                name="image"
                                id="image-input"
                                class="custom-file-input @error('image') is-invalid @enderror"
                                accept="image/*"
                            >

                            <label
                                class="custom-file-label"
                                for="image-input"
                            >
                                Choose image
                            </label>

                        </div>

                        @error('image')
                            <span class="text-danger small">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                </div>
            </div>

        </div>


        {{-- =========================
             RIGHT COLUMN
        ========================== --}}
        <div class="col-lg-4">

            {{-- Publish --}}
            <div class="card card-success card-outline">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-rocket mr-1"></i>
                        Publish
                    </h3>
                </div>

                <div class="card-body">

                    {{-- Active --}}
                    <div class="form-group">

                        <div class="custom-control custom-switch">

                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="is_active"
                                name="is_active"
                                value="1"
                                {{ old(
                                    'is_active',
                                    ($category->is_active ?? true) ? '1' : ''
                                ) ? 'checked' : '' }}
                            >

                            <label
                                class="custom-control-label"
                                for="is_active"
                            >
                                Active (Visible)
                            </label>

                        </div>

                    </div>


                    {{-- Buttons --}}
                    <div class="d-flex flex-column">

                        <button
                            type="submit"
                            class="btn btn-primary btn-block mb-2"
                        >
                            <i class="fas fa-save mr-1"></i>

                            {{ isset($category)
                                ? 'Update Category'
                                : 'Save Category'
                            }}
                        </button>


                        <a
                            href="{{ route('admin.categories.index') }}"
                            class="btn btn-default btn-block"
                        >
                            <i class="fas fa-times mr-1"></i>
                            Cancel
                        </a>

                    </div>

                </div>

            </div>


            {{-- Hierarchy --}}
            <div class="card card-secondary card-outline">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-folder-tree mr-1"></i>
                        Hierarchy
                    </h3>

                </div>


                <div class="card-body">

                    {{-- Parent Category --}}
                    <div class="form-group">

                        <label for="parent_id">
                            Parent Category

                            <small class="text-muted">
                                (leave empty for top-level)
                            </small>
                        </label>

                        <select
                            name="parent_id"
                            id="parent_id"
                            class="form-control @error('parent_id') is-invalid @enderror"
                        >

                            <option value="">
                                — None (Top-level category) —
                            </option>

                            @foreach($parentCategories as $parent)

                                <option
                                    value="{{ $parent->id }}"
                                    {{ old(
                                        'parent_id',
                                        $category->parent_id ?? ''
                                    ) == $parent->id ? 'selected' : '' }}
                                >
                                    {{ $parent->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('parent_id')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- Sort Order --}}
                    <div class="form-group mb-0">

                        <label for="sort_order">
                            Sort Order
                        </label>

                        <input
                            type="number"
                            id="sort_order"
                            name="sort_order"
                            value="{{ old(
                                'sort_order',
                                $category->sort_order ?? 0
                            ) }}"
                            class="form-control @error('sort_order') is-invalid @enderror"
                            min="0"
                            placeholder="0"
                        >

                        @error('sort_order')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                </div>

            </div>

        </div>

    </div>

</form>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');
    const imageIcon = document.getElementById('image-icon');

    if (imageInput) {

        imageInput.addEventListener('change', function (event) {

            const file = event.target.files[0];

            if (!file) {
                return;
            }

            // Update Bootstrap custom file label
            const fileLabel = this.nextElementSibling;

            if (fileLabel) {
                fileLabel.textContent = file.name;
            }

            // Preview image
            const reader = new FileReader();

            reader.onload = function (e) {

                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';

                if (imageIcon) {
                    imageIcon.style.display = 'none';
                }

            };

            reader.readAsDataURL(file);

        });

    }

});
</script>

@endpush