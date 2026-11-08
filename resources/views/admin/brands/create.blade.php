@extends('layouts.admin')

@section('title', isset($brand) ? 'Edit Brand' : 'Create Brand')
@section('page-title', isset($brand) ? 'Edit Brand' : 'New Brand')

@section('content')

<form method="POST"
      action="{{ isset($brand)
            ? route('admin.brands.update', $brand)
            : route('admin.brands.store') }}"
      enctype="multipart/form-data">

    @csrf

    @if(isset($brand))
        @method('PUT')
    @endif

    <div class="row">

        {{-- =========================
             LEFT COLUMN
        ========================== --}}
        <div class="col-lg-8">

            {{-- Brand Details --}}
            <div class="card card-primary card-outline">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-1"></i>
                        Brand Details
                    </h3>
                </div>

                <div class="card-body">

                    {{-- Brand Name --}}
                    <div class="form-group">

                        <label for="name">
                            Brand Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $brand->name ?? '') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="e.g. Samsung, Apple"
                            required
                        >

                        @error('name')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- Description --}}
                    <div class="form-group mb-0">

                        <label for="description">
                            Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="6"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Describe the brand profile or origin summary..."
                        >{{ old('description', $brand->description ?? '') }}</textarea>

                        @error('description')
                            <span class="invalid-feedback">
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

            {{-- Publish Status --}}
            <div class="card card-success card-outline">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-rocket mr-1"></i>
                        Publish Status
                    </h3>

                </div>

                <div class="card-body">

                    {{-- Active --}}
                    <div class="form-group">

                        <div class="custom-control custom-switch">

                            {{-- Default value when unchecked --}}
                            <input
                                type="hidden"
                                name="is_active"
                                value="0"
                            >

                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="is_active"
                                name="is_active"
                                value="1"
                                {{ old(
                                    'is_active',
                                    $brand->is_active ?? true
                                ) ? 'checked' : '' }}
                            >

                            <label
                                class="custom-control-label"
                                for="is_active"
                            >
                                Active Listing
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

                            {{ isset($brand)
                                ? 'Update Brand'
                                : 'Save Brand'
                            }}

                        </button>


                        <a
                            href="{{ route('admin.brands.index') }}"
                            class="btn btn-default btn-block"
                        >

                            <i class="fas fa-times mr-1"></i>

                            Cancel

                        </a>

                    </div>

                </div>

            </div>


            {{-- Brand Logo --}}
            <div class="card card-primary card-outline">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-image mr-1"></i>
                        Brand Logo
                    </h3>

                </div>


                <div class="card-body">

                    {{-- Logo Preview --}}
                    <div
                        id="logo-preview-container"
                        class="border rounded d-flex align-items-center justify-content-center mb-3"
                        style="
                            height: 220px;
                            background-color: #f8f9fa;
                            overflow: hidden;
                        "
                    >

                        @if(isset($brand) && $brand->logo)

                            <img
                                id="logo-preview"
                                src="{{ asset('storage/' . $brand->logo) }}"
                                alt="{{ $brand->name }}"
                                style="
                                    max-width: 100%;
                                    max-height: 100%;
                                    object-fit: contain;
                                "
                            >

                            <i
                                id="logo-icon"
                                class="far fa-bookmark text-muted"
                                style="display:none; font-size:45px;"
                            ></i>

                        @else

                            <i
                                id="logo-icon"
                                class="far fa-bookmark text-muted"
                                style="font-size:45px;"
                            ></i>

                            <img
                                id="logo-preview"
                                src=""
                                alt="Logo Preview"
                                style="
                                    display:none;
                                    max-width:100%;
                                    max-height:100%;
                                    object-fit:contain;
                                "
                            >

                        @endif

                    </div>


                    {{-- Logo Upload --}}
                    <div class="form-group mb-0">

                        <label for="logo-input">
                            Brand Logo
                        </label>

                        <div class="custom-file">

                            <input
                                type="file"
                                name="logo"
                                id="logo-input"
                                class="custom-file-input @error('logo') is-invalid @enderror"
                                accept="image/*"
                            >

                            <label
                                class="custom-file-label"
                                for="logo-input"
                            >
                                Choose logo
                            </label>

                        </div>

                        <small class="form-text text-muted">
                            Recommended: PNG or JPG with transparent background.
                        </small>

                        @error('logo')
                            <span class="text-danger small">
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

    const logoInput = document.getElementById('logo-input');
    const logoPreview = document.getElementById('logo-preview');
    const logoIcon = document.getElementById('logo-icon');

    if (logoInput) {

        logoInput.addEventListener('change', function (event) {

            const file = event.target.files[0];

            if (!file) {
                return;
            }

            // Update file name
            const fileLabel = this.nextElementSibling;

            if (fileLabel) {
                fileLabel.textContent = file.name;
            }

            // Preview
            const reader = new FileReader();

            reader.onload = function (e) {

                logoPreview.src = e.target.result;
                logoPreview.style.display = 'block';

                if (logoIcon) {
                    logoIcon.style.display = 'none';
                }

            };

            reader.readAsDataURL(file);

        });

    }

});
</script>

@endpush