@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Category' : 'Create Category')
@section('page-title', isset($category) ? 'Edit Category' : 'New Category')

@section('content')

{{-- Page Header --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1 class="m-0">
                    {{ isset($category) ? 'Edit Category' : 'Create Category' }}
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.categories.index') }}">
                            Categories
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        {{ isset($category) ? 'Edit' : 'Create' }}
                    </li>
                </ol>
            </div>

        </div>
    </div>
</div>


{{-- Main Content --}}
<section class="content">

    <div class="container-fluid">

        <form
            method="POST"
            action="{{ isset($category)
                ? route('admin.categories.update', $category)
                : route('admin.categories.store') }}"
            enctype="multipart/form-data"
        >

            @csrf

            @if(isset($category))
                @method('PUT')
            @endif


            <div class="row">

                {{-- ========================================= --}}
                {{-- LEFT COLUMN --}}
                {{-- ========================================= --}}
                <div class="col-lg-8">

                    {{-- Basic Details --}}
                    <div class="card">

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
                                    name="name"
                                    id="name"
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

                                <label for="slug">
                                    Slug
                                </label>

                                <input
                                    type="text"
                                    name="slug"
                                    id="slug"
                                    value="{{ old('slug', $category->slug ?? '') }}"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    placeholder="Auto-generated if blank"
                                >

                                @error('slug')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror

                                <small class="form-text text-muted">
                                    Example: smartphones
                                </small>

                            </div>


                            {{-- Description --}}
                            <div class="form-group mb-0">

                                <label for="description">
                                    Description
                                </label>

                                <textarea
                                    name="description"
                                    id="description"
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
                    <div class="card">

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
                                class="border rounded bg-light d-flex align-items-center justify-content-center mb-3"
                                style="
                                    width: 100%;
                                    height: 220px;
                                    overflow: hidden;
                                "
                            >

                                @if(isset($category) && $category->image)

                                    <img
                                        id="image-preview"
                                        src="{{ asset('storage/' . $category->image) }}"
                                        alt="{{ $category->name }}"
                                        style="
                                            max-width: 100%;
                                            max-height: 100%;
                                            object-fit: contain;
                                        "
                                    >

                                    <i
                                        id="image-icon"
                                        class="fas fa-image fa-3x text-muted"
                                        style="display: none;"
                                    ></i>

                                @else

                                    <i
                                        id="image-icon"
                                        class="far fa-image fa-3x text-muted"
                                    ></i>

                                    <img
                                        id="image-preview"
                                        src=""
                                        alt="Image preview"
                                        style="
                                            display: none;
                                            max-width: 100%;
                                            max-height: 100%;
                                            object-fit: contain;
                                        "
                                    >

                                @endif

                            </div>


                            {{-- Image Upload --}}
                            <div class="form-group mb-0">

                                <label for="image">
                                    Upload Image
                                </label>

                                <div class="custom-file">

                                    <input
                                        type="file"
                                        name="image"
                                        id="image-input"
                                        accept="image/*"
                                        class="custom-file-input @error('image') is-invalid @enderror"
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

                                <small class="form-text text-muted">
                                    Recommended formats: JPG, JPEG, PNG, WEBP.
                                </small>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ========================================= --}}
                {{-- RIGHT COLUMN --}}
                {{-- ========================================= --}}
                <div class="col-lg-4">

                    {{-- Publish --}}
                    <div class="card">

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
                                        name="is_active"
                                        value="1"
                                        id="is_active"
                                        class="custom-control-input"
                                        {{ old(
                                            'is_active',
                                            isset($category)
                                                ? $category->is_active
                                                : true
                                        ) ? 'checked' : '' }}
                                    >

                                    <label
                                        class="custom-control-label"
                                        for="is_active"
                                    >
                                        Active
                                    </label>

                                </div>

                                <small class="form-text text-muted">
                                    Active categories are visible on the website.
                                </small>

                            </div>

                        </div>

                        <div class="card-footer">

                            <button
                                type="submit"
                                class="btn btn-primary btn-block"
                            >
                                <i class="fas fa-save mr-1"></i>

                                {{ isset($category)
                                    ? 'Update Category'
                                    : 'Save Category'
                                }}
                            </button>

                            <a
                                href="{{ route('admin.categories.index') }}"
                                class="btn btn-secondary btn-block"
                            >
                                <i class="fas fa-times mr-1"></i>
                                Cancel
                            </a>

                        </div>

                    </div>


                    {{-- Hierarchy --}}
                    <div class="card">

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
                                </label>

                                <select
                                    name="parent_id"
                                    id="parent_id"
                                    class="form-control @error('parent_id') is-invalid @enderror"
                                >

                                    <option value="">
                                        None (Top Level)
                                    </option>

                                    @foreach($parentCategories ?? [] as $parent)

                                        @if(!isset($category) || $category->id !== $parent->id)

                                            <option
                                                value="{{ $parent->id }}"
                                                @selected(
                                                    old(
                                                        'parent_id',
                                                        $category->parent_id ?? ''
                                                    ) == $parent->id
                                                )
                                            >
                                                {{ $parent->name }}
                                            </option>

                                        @endif

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
                                    name="sort_order"
                                    id="sort_order"
                                    value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                                    class="form-control @error('sort_order') is-invalid @enderror"
                                    placeholder="0"
                                    min="0"
                                >

                                @error('sort_order')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror

                                <small class="form-text text-muted">
                                    Lower numbers appear first.
                                </small>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

</section>

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

                // Update filename
                const label = imageInput.nextElementSibling;

                if (file && label) {
                    label.textContent = file.name;
                }

                if (!file) {
                    return;
                }

                // Preview image
                const reader = new FileReader();

                reader.onload = function (e) {

                    if (imagePreview) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }

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