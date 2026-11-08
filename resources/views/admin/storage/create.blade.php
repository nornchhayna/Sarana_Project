@extends('layouts.admin')

@section('title', isset($storage) ? 'Edit Storage' : 'Create Storage')
@section('page-title', isset($storage) ? 'Edit Storage' : 'New Storage Option')

@section('content')

<form method="POST"
      action="{{ isset($storage)
          ? route('admin.storages.update', ['phoneStorage' => $storage])
          : route('admin.storages.store') }}">

    @csrf

    @if(isset($storage))
        @method('PUT')
    @endif

    <div class="row">

        {{-- LEFT COLUMN --}}
        <div class="col-md-8">

            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-database mr-2"></i>
                        Capacity Matrix
                    </h3>
                </div>

                <div class="card-body">

                    {{-- Capacity --}}
                    <div class="form-group">

                        <label for="capacity">
                            Storage Value Capacity
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="capacity"
                            id="capacity"
                            value="{{ old('capacity', $storage->capacity ?? '') }}"
                            class="form-control @error('capacity') is-invalid @enderror"
                            placeholder="e.g. 128GB, 512GB, 1TB"
                            required
                        >

                        @error('capacity')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                        <small class="form-text text-muted">
                            Enter the storage capacity used for product variants.
                            Example: 64GB, 128GB, 256GB, 512GB, 1TB.
                        </small>

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

                        <label for="is_active">
                            Variant Active
                        </label>

                        <div class="custom-control custom-switch">

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
                                {{ old('is_active', $storage->is_active ?? true) ? 'checked' : '' }}
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
                            class="btn btn-primary btn-block">

                        <i class="fas fa-save mr-1"></i>

                        {{ isset($storage)
                            ? 'Update Option'
                            : 'Save Option' }}

                    </button>

                    <a href="{{ route('admin.storages.index') }}"
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