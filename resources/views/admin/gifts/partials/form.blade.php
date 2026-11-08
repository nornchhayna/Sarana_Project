@php
    $gift = $gift ?? null;
@endphp

{{-- Validation Errors --}}
@if ($errors->any())

    <div class="alert alert-danger">
        <h5>
            <i class="fas fa-exclamation-triangle mr-1"></i>
            Please fix the following errors:
        </h5>

        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

@endif


{{-- Gift Form Card --}}
<div class="card card-primary">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-gift mr-2"></i>

            Gift Information

        </h3>

    </div>


    <div class="card-body">

        {{-- Name --}}
        <div class="form-group">

            <label for="name">
                Gift Name
            </label>

            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $gift->name ?? '') }}"
                placeholder="e.g. Silicone Phone Case"
                class="form-control @error('name') is-invalid @enderror"
            >

            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Description --}}
        <div class="form-group">

            <label for="description">
                Description
            </label>

            <textarea
                name="description"
                id="description"
                rows="3"
                placeholder="Optional short description shown to admins"
                class="form-control @error('description') is-invalid @enderror"
            >{{ old('description', $gift->description ?? '') }}</textarea>

            @error('description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Image --}}
        <div class="form-group">

            <label for="image">
                Image
            </label>


            {{-- Current Image --}}
            @if (!empty($gift?->image))

                <div class="mb-3">

                    <div class="d-flex align-items-center">

                        <img
                            src="{{ asset('storage/' . $gift->image) }}"
                            alt="{{ $gift->name }}"
                            class="img-thumbnail mr-3"
                            style="width: 100px; height: 100px; object-fit: cover;"
                        >

                        <div>

                            <div class="text-muted">
                                <i class="fas fa-image mr-1"></i>
                                Current image
                            </div>

                            <small class="text-muted">
                                Upload a new image to replace it.
                            </small>

                        </div>

                    </div>

                </div>

            @endif


            {{-- Upload --}}
            <div class="custom-file">

                <input
                    type="file"
                    name="image"
                    id="image"
                    accept="image/png,image/jpeg,image/webp"
                    class="custom-file-input @error('image') is-invalid @enderror"
                >

                <label
                    class="custom-file-label"
                    for="image">

                    Choose image

                </label>

            </div>

            <small class="form-text text-muted">
                JPG, PNG or WEBP. Max 2MB.
            </small>

            @error('image')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Active Toggle --}}
        <div class="form-group mb-0">

            <label>
                Status
            </label>

            <div class="custom-control custom-switch">

                {{-- Send 0 when unchecked --}}
                <input
                    type="hidden"
                    name="is_active"
                    value="0"
                >

                <input
                    type="checkbox"
                    name="is_active"
                    id="is_active"
                    value="1"
                    class="custom-control-input"
                    {{ old('is_active', $gift->is_active ?? true) ? 'checked' : '' }}
                >

                <label
                    class="custom-control-label"
                    for="is_active">

                    Active

                </label>

            </div>

            <small class="form-text text-muted">
                Visible for admins to attach to products.
            </small>

        </div>

    </div>


    {{-- Actions --}}
    <div class="card-footer d-flex justify-content-between">

        <a
            href="{{ route('admin.gifts.index') }}"
            class="btn btn-secondary">

            <i class="fas fa-times mr-1"></i>
            Cancel

        </a>

        <button
            type="submit"
            class="btn btn-primary">

            <i class="fas fa-save mr-1"></i>

            {{ $gift ? 'Update Gift' : 'Create Gift' }}

        </button>

    </div>

</div>


{{-- Optional: Show selected filename --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const imageInput = document.getElementById('image');

        if (imageInput) {
            imageInput.addEventListener('change', function () {

                const fileName = this.files.length
                    ? this.files[0].name
                    : 'Choose image';

                const label = this.nextElementSibling;

                if (label) {
                    label.textContent = fileName;
                }

            });
        }

    });
</script>
@endpush