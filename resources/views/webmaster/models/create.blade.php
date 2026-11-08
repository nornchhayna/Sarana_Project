@extends('layouts.webmaster')

@section('title', isset($model) ? 'Edit Model' : 'Create Model')
@section('page-title', isset($model) ? 'Edit Model' : 'New Model')

@section('content')

<form method="POST"
      action="{{ isset($model)
            ? route('webmaster.models.update', $model)
            : route('webmaster.models.store') }}">

    @csrf

    @if(isset($model))
        @method('PUT')
    @endif


    <div class="row">

        {{-- =========================================
             LEFT COLUMN
        ========================================== --}}
        <div class="col-lg-8">

            {{-- Configuration Specs --}}
            <div class="card card-primary card-outline">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-mobile-alt mr-1"></i>
                        Configuration Specs
                    </h3>

                </div>


                <div class="card-body">

                    {{-- Model Name --}}
                    <div class="form-group">

                        <label for="name">
                            Model Name Title
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $model->name ?? '') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="e.g. iPhone 15 Pro Max, Galaxy S24 Ultra"
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
                            Description Notes
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            rows="5"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Optional notes regarding technical variations or release status details..."
                        >{{ old('description', $model->description ?? '') }}</textarea>

                        @error('description')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================
             RIGHT COLUMN
        ========================================== --}}
        <div class="col-lg-4">

            {{-- =====================================
                 PUBLISH STATUS
            ====================================== --}}
            <div class="card card-success card-outline">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-rocket mr-1"></i>
                        Action Publish
                    </h3>

                </div>


                <div class="card-body">

                    {{-- Active Status --}}
                    <div class="form-group">

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
                                {{ old(
                                    'is_active',
                                    $model->is_active ?? true
                                ) ? 'checked' : '' }}
                            >

                            <label
                                class="custom-control-label"
                                for="is_active"
                            >
                                Active Status
                            </label>

                        </div>

                    </div>


                    {{-- Save --}}
                    <button
                        type="submit"
                        class="btn btn-primary btn-block"
                    >

                        <i class="fas fa-save mr-1"></i>

                        {{ isset($model)
                            ? 'Update Model'
                            : 'Save Model'
                        }}

                    </button>


                    {{-- Cancel --}}
                    <a
                        href="{{ route('webmaster.models.index') }}"
                        class="btn btn-default btn-block mt-2"
                    >

                        <i class="fas fa-times mr-1"></i>
                        Cancel

                    </a>

                </div>

            </div>


            {{-- =====================================
                 BRAND ASSIGNMENT
            ====================================== --}}
            <div class="card card-info card-outline">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-bookmark mr-1"></i>
                        Brand Assignment
                    </h3>

                </div>


                <div class="card-body">

                    <div class="form-group mb-0">

                        <label for="brand_id">
                            Parent Brand
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="brand_id"
                            id="brand_id"
                            class="form-control @error('brand_id') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                Select Target Brand...
                            </option>

                            @foreach($brands as $brand)

                                <option
                                    value="{{ $brand->id }}"
                                    @selected(
                                        old(
                                            'brand_id',
                                            $model->brand_id ?? ''
                                        ) == $brand->id
                                    )
                                >
                                    {{ $brand->name }}
                                </option>

                            @endforeach

                        </select>


                        @error('brand_id')

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