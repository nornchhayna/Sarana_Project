@extends('layouts.admin')

@section('title', 'Create RAM')
@section('page-title', 'New RAM Option')

@section('content')

<form method="POST" action="{{ route('admin.rams.store') }}">

    @csrf

    <div class="row">

        {{-- LEFT COLUMN --}}
        <div class="col-md-8">

            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-microchip mr-2"></i>
                        RAM Capacity
                    </h3>
                </div>

                <div class="card-body">

                    {{-- RAM Capacity --}}
                    <div class="form-group">

                        <label for="capacity">
                            RAM Capacity
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="capacity"
                            id="capacity"
                            value="{{ old('capacity') }}"
                            class="form-control @error('capacity') is-invalid @enderror"
                            placeholder="e.g. 6GB, 8GB, 12GB"
                            required
                        >

                        @error('capacity')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="form-text text-muted">
                            Example: 4GB, 6GB, 8GB, 12GB, 16GB
                        </small>

                    </div>


                    {{-- Label --}}
                    <div class="form-group">

                        <label for="label">
                            Label
                            <span class="text-muted">(Optional)</span>
                        </label>

                        <input
                            type="text"
                            name="label"
                            id="label"
                            value="{{ old('label') }}"
                            class="form-control @error('label') is-invalid @enderror"
                            placeholder="e.g. 6GB RAM, 12GB RAM"
                        >

                        @error('label')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- RIGHT COLUMN --}}
        <div class="col-md-4">

            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-rocket mr-2"></i>
                        Control Status
                    </h3>
                </div>

                <div class="card-body">

                    {{-- Active Status --}}
                    <div class="form-group">

                        <label>
                            Variant Active
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
                                value="1"
                                id="is_active"
                                class="custom-control-input"
                                {{ old('is_active', true) ? 'checked' : '' }}
                            >

                            <label
                                class="custom-control-label"
                                for="is_active"
                            >
                                Active
                            </label>

                        </div>

                    </div>

                </div>

                <div class="card-footer">

                    <button type="submit"
                            class="btn btn-primary btn-block mb-2">

                        <i class="fas fa-save mr-1"></i>
                        Save Option

                    </button>

                    <a href="{{ route('admin.rams.index') }}"
                       class="btn btn-secondary btn-block">

                        <i class="fas fa-times mr-1"></i>
                        Cancel

                    </a>

                </div>

            </div>

        </div>

    </div>

</form>

@endsection