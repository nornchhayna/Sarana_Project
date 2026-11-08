@extends('layouts.webmaster')

@section('title', isset($color) ? 'Edit Color' : 'Create Color')
@section('page-title', isset($color) ? 'Edit Color' : 'New Color')

@section('content')

<form method="POST"
      action="{{ isset($color)
            ? route('webmaster.colors.update', $color)
            : route('webmaster.colors.store') }}">

    @csrf

    @if(isset($color))
        @method('PUT')
    @endif

    <div class="row">

        {{-- =========================================
             LEFT COLUMN
        ========================================== --}}
        <div class="col-lg-8">

            <div class="card card-primary card-outline">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-palette mr-1"></i>
                        Color Properties
                    </h3>
                </div>

                <div class="card-body">

                    {{-- Color Name --}}
                    <div class="form-group">

                        <label for="name">
                            Color Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $color->name ?? '') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="e.g. Space Gray, Titanium Violet"
                            required
                        >

                        @error('name')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- HEX Color --}}
                    <div class="form-group mb-0">

                        <label for="hex-text-input">
                            Hex Color Code
                        </label>

                        <div class="row align-items-center">

                            {{-- Color Preview --}}
                            <div class="col-auto">

                                <div
                                    id="color-preview"
                                    class="border rounded"
                                    style="
                                        width: 55px;
                                        height: 55px;
                                        background-color:
                                            {{ old(
                                                'hex_code',
                                                $color->hex_code ?? '#cccccc'
                                            ) }};
                                    "
                                ></div>

                            </div>


                            {{-- HEX Input --}}
                            <div class="col">

                                <input
                                    type="text"
                                    name="hex_code"
                                    id="hex-text-input"
                                    value="{{ old(
                                        'hex_code',
                                        $color->hex_code ?? ''
                                    ) }}"
                                    class="form-control @error('hex_code') is-invalid @enderror"
                                    placeholder="#FFFFFF"
                                    maxlength="7"
                                    style="
                                        font-family: monospace;
                                        text-transform: uppercase;
                                    "
                                >

                                @error('hex_code')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>


                            {{-- Native Color Picker --}}
                            <div class="col-auto">

                                <input
                                    type="color"
                                    id="picker-element"
                                    value="{{ old(
                                        'hex_code',
                                        $color->hex_code ?? '#cccccc'
                                    ) }}"
                                    class="form-control p-1"
                                    style="
                                        width: 55px;
                                        height: 38px;
                                        cursor: pointer;
                                    "
                                    title="Pick a color"
                                >

                            </div>

                        </div>

                        <small class="form-text text-muted">
                            Enter a HEX color such as #FFFFFF or choose a color from the picker.
                        </small>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================
             RIGHT COLUMN
        ========================================== --}}
        <div class="col-lg-4">

            {{-- Control Status --}}
            <div class="card card-success card-outline">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-rocket mr-1"></i>
                        Control Status
                    </h3>

                </div>

                <div class="card-body">

                    {{-- Active Switch --}}
                    <div class="form-group">

                        <div class="custom-control custom-switch">

                            {{-- Value when unchecked --}}
                            <input
                                type="hidden"
                                name="is_active"
                                value="0"
                            >

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                id="is_active"
                                class="custom-control-input"
                                {{ old(
                                    'is_active',
                                    $color->is_active ?? true
                                ) ? 'checked' : '' }}
                            >

                            <label
                                class="custom-control-label"
                                for="is_active"
                            >
                                Variant Visible
                            </label>

                        </div>

                    </div>


                    {{-- Save --}}
                    <button
                        type="submit"
                        class="btn btn-primary btn-block"
                    >
                        <i class="fas fa-save mr-1"></i>

                        {{ isset($color)
                            ? 'Update Color'
                            : 'Save Color'
                        }}
                    </button>


                    {{-- Cancel --}}
                    <a
                        href="{{ route('webmaster.colors.index') }}"
                        class="btn btn-default btn-block mt-2"
                    >
                        <i class="fas fa-times mr-1"></i>
                        Cancel
                    </a>

                </div>

            </div>

        </div>

    </div>

</form>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const textInput   = document.getElementById('hex-text-input');
    const colorPicker = document.getElementById('picker-element');
    const preview     = document.getElementById('color-preview');

    if (!textInput || !colorPicker) {
        return;
    }


    // Update preview
    function updatePreview(hex) {

        if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {

            if (preview) {
                preview.style.backgroundColor = hex;
            }

        }

    }


    // Color picker -> HEX input
    function syncFromPicker(event) {

        const value = event.target.value.toUpperCase();

        textInput.value = value;

        updatePreview(value);

    }


    colorPicker.addEventListener(
        'input',
        syncFromPicker
    );

    colorPicker.addEventListener(
        'change',
        syncFromPicker
    );


    // HEX input -> Preview + Color picker
    textInput.addEventListener('input', function () {

        let value = textInput.value
            .trim()
            .toUpperCase();


        // Add # automatically
        if (value && !value.startsWith('#')) {
            value = '#' + value;
        }

        textInput.value = value;


        // Update preview
        updatePreview(value);


        // Update color picker
        if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
            colorPicker.value = value;
        }

    });

});
</script>

@endpush