@extends('layouts.admin')

@section('title', isset($promotion) ? 'Edit Campaign' : 'Create Campaign')
@section('page-title', isset($promotion) ? 'Edit Promotion' : 'New Promotion')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <h5>
            <i class="fas fa-exclamation-triangle"></i>
            Please fix the following errors
        </h5>

        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ isset($promotion)
            ? route('admin.promotions.update', $promotion)
            : route('admin.promotions.store') }}">

    @csrf

    @if(isset($promotion))
        @method('PUT')
    @endif

    <div class="row">

        {{-- LEFT --}}
        <div class="col-lg-8">

            {{-- GENERAL --}}
            <div class="card card-primary card-outline">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tags mr-2"></i>
                        General Parameters
                    </h3>
                </div>

                <div class="card-body">

                    {{-- Name --}}
                    <div class="form-group">
                        <label for="name">
                            Campaign Public Title Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $promotion->name ?? '') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="e.g. Summer Launch Kickoff, Black Friday Deal"
                            required
                        >

                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Code + Type --}}
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">Coupon Code</label>

                                <input
                                    type="text"
                                    id="code"
                                    name="code"
                                    value="{{ old('code', $promotion->code ?? '') }}"
                                    class="form-control text-uppercase"
                                    placeholder="e.g. SUMMER50"
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">
                                    Discount Type
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    id="type"
                                    name="type"
                                    class="form-control"
                                    required
                                >
                                    <option value="percentage"
                                        @selected(old('type', $promotion->type ?? 'percentage') === 'percentage')>
                                        Percentage (%)
                                    </option>

                                    <option value="fixed"
                                        @selected(old('type', $promotion->type ?? '') === 'fixed')>
                                        Fixed Amount ($)
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>

                    {{-- Value + Usage --}}
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="value">
                                    Discount Value
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    id="value"
                                    name="value"
                                    value="{{ old('value', $promotion->value ?? '') }}"
                                    class="form-control"
                                    placeholder="e.g. 10 or 15.50"
                                    required
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usage_limit">
                                    Usage Limit
                                </label>

                                <input
                                    type="number"
                                    min="1"
                                    id="usage_limit"
                                    name="usage_limit"
                                    value="{{ old('usage_limit', $promotion->usage_limit ?? '') }}"
                                    class="form-control"
                                    placeholder="Leave empty for unlimited"
                                >
                            </div>
                        </div>

                    </div>

                    {{-- Description --}}
                    <div class="form-group mb-0">
                        <label for="description">Description</label>

                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="form-control"
                            placeholder="Describe the promotion..."
                        >{{ old('description', $promotion->description ?? '') }}</textarea>
                    </div>

                </div>
            </div>


            {{-- THRESHOLDS --}}
            <div class="card card-primary card-outline">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sliders-h mr-2"></i>
                        Discount Constraints
                    </h3>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label for="min_order_amount">
                                    Minimum Order Amount ($)
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    id="min_order_amount"
                                    name="min_order_amount"
                                    value="{{ old('min_order_amount', $promotion->min_order_amount ?? '') }}"
                                    class="form-control"
                                    placeholder="0.00"
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="max_discount_amount">
                                    Maximum Discount ($)
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    id="max_discount_amount"
                                    name="max_discount_amount"
                                    value="{{ old('max_discount_amount', $promotion->max_discount_amount ?? '') }}"
                                    class="form-control"
                                    placeholder="No maximum"
                                >
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>


        {{-- RIGHT --}}
        <div class="col-lg-4">

            {{-- CONTROL --}}
            <div class="card card-primary card-outline">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-rocket mr-2"></i>
                        Control Parameters
                    </h3>
                </div>

                <div class="card-body">

                    <div class="form-group">

                        <div class="custom-control custom-switch">

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
                                    $promotion->is_active ?? true
                                ) ? 'checked' : '' }}
                            >

                            <label
                                class="custom-control-label"
                                for="is_active"
                            >
                                Promotion Active
                            </label>

                        </div>

                    </div>


                    @if(isset($promotion))

                        <div class="alert alert-light border">
                            <small class="text-muted">
                                Current Usage
                            </small>

                            <h4 class="mb-0">
                                {{ $promotion->usage_count ?? 0 }}
                            </h4>
                        </div>

                    @endif


                    <button
                        type="submit"
                        class="btn btn-primary btn-block"
                    >
                        <i class="fas fa-save mr-1"></i>

                        {{ isset($promotion)
                            ? 'Update Promotion'
                            : 'Create Promotion' }}
                    </button>

                    <a
                        href="{{ route('admin.promotions.index') }}"
                        class="btn btn-default btn-block"
                    >
                        Cancel
                    </a>

                </div>
            </div>


            {{-- DATES --}}
            <div class="card card-primary card-outline">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Promotion Validity
                    </h3>
                </div>

                <div class="card-body">

                    <div class="form-group">
                        <label for="starts_at">
                            Start Date
                        </label>

                        <input
                            type="date"
                            id="starts_at"
                            name="starts_at"
                            value="{{ old(
                                'starts_at',
                                isset($promotion->starts_at)
                                    ? \Carbon\Carbon::parse($promotion->starts_at)->format('Y-m-d')
                                    : ''
                            ) }}"
                            class="form-control"
                        >
                    </div>

                    <div class="form-group mb-0">
                        <label for="expires_at">
                            Expiration Date
                        </label>

                        <input
                            type="date"
                            id="expires_at"
                            name="expires_at"
                            value="{{ old(
                                'expires_at',
                                isset($promotion->expires_at)
                                    ? \Carbon\Carbon::parse($promotion->expires_at)->format('Y-m-d')
                                    : ''
                            ) }}"
                            class="form-control"
                        >
                    </div>

                </div>
            </div>


            {{-- RESET USAGE --}}
            @if(isset($promotion) && ($promotion->usage_count ?? 0) > 0)

                <div class="card card-warning card-outline">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Usage Counter
                        </h3>
                    </div>

                    <div class="card-body">

                        <p class="text-muted">
                            Reset the current promotion usage counter.
                        </p>

                        <button
                            type="button"
                            class="btn btn-warning btn-block"
                            onclick="
                                if(confirm('Reset usage counter to 0?')) {
                                    document.getElementById('reset-usage-form').submit();
                                }
                            "
                        >
                            <i class="fas fa-redo mr-1"></i>
                            Reset Usage Counter
                        </button>

                    </div>
                </div>

            @endif

        </div>

    </div>

</form>


@if(isset($promotion))

    <form
        id="reset-usage-form"
        action="{{ route('admin.promotions.resetUsage', $promotion) }}"
        method="POST"
        style="display:none;"
    >
        @csrf
        @method('PATCH')
    </form>

@endif

@endsection