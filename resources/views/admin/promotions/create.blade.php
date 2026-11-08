@extends('layouts.admin')

@section('title', isset($promotion) ? 'Edit Campaign' : 'Create Campaign')
@section('page-title', isset($promotion) ? 'Edit Promotion' : 'New Promotion')

@section('content')

<div class="container-fluid">

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <h6>
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Please fix the following errors:
            </h6>

            <ul class="mb-0 pl-4">
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

            {{-- =========================================================
                 LEFT COLUMN
            ========================================================== --}}
            <div class="col-lg-8">

                {{-- General Parameters --}}
                <div class="card card-outline card-primary">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tags mr-1"></i>
                            General Parameters
                        </h3>
                    </div>

                    <div class="card-body">

                        {{-- Campaign Name --}}
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
                                class="form-control"
                                placeholder="e.g. Summer Launch Kickoff, Black Friday Deal"
                                required
                            >

                        </div>


                        {{-- Code + Type --}}
                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group">

                                    <label for="code">
                                        Coupon String Code
                                    </label>

                                    <input
                                        type="text"
                                        id="code"
                                        name="code"
                                        value="{{ old('code', $promotion->code ?? '') }}"
                                        class="form-control text-uppercase"
                                        placeholder="e.g. SUMMER50, FREESHIP"
                                    >

                                    <small class="form-text text-muted">
                                        Leave empty if this promotion is automatic.
                                    </small>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <div class="form-group">

                                    <label for="type">
                                        Discount Computation Type
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        id="type"
                                        name="type"
                                        class="form-control"
                                        required
                                    >

                                        <option value="percentage"
                                            @selected(old(
                                                'type',
                                                $promotion->type ?? 'percentage'
                                            ) === 'percentage')>
                                            Percentage (%)
                                        </option>

                                        <option value="fixed"
                                            @selected(old(
                                                'type',
                                                $promotion->type ?? ''
                                            ) === 'fixed')>
                                            Fixed Amount ($)
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>


                        {{-- Value + Usage Limit --}}
                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group">

                                    <label for="value">
                                        Discount Numeric Value
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-percent"></i>
                                            </span>
                                        </div>

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

                            <label for="description">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="form-control"
                                placeholder="Describe the promotion and its application rules..."
                            >{{ old('description', $promotion->description ?? '') }}</textarea>

                        </div>

                    </div>

                </div>


                {{-- Threshold Constraints --}}
                <div class="card card-outline card-primary">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sliders-h mr-1"></i>
                            Threshold Constraints
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            {{-- Minimum Order --}}
                            <div class="col-md-6">

                                <div class="form-group mb-md-0">

                                    <label for="min_order_amount">
                                        Minimum Order Requirement ($)
                                    </label>

                                    <div class="input-group">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>

                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            id="min_order_amount"
                                            name="min_order_amount"
                                            value="{{ old(
                                                'min_order_amount',
                                                $promotion->min_order_amount ?? ''
                                            ) }}"
                                            class="form-control"
                                            placeholder="0.00"
                                        >

                                    </div>

                                    <small class="form-text text-muted">
                                        Minimum cart value required to use this promotion.
                                    </small>

                                </div>

                            </div>


                            {{-- Maximum Discount --}}
                            <div class="col-md-6">

                                <div class="form-group mb-md-0">

                                    <label for="max_discount_amount">
                                        Maximum Discount Cap ($)
                                    </label>

                                    <div class="input-group">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>

                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            id="max_discount_amount"
                                            name="max_discount_amount"
                                            value="{{ old(
                                                'max_discount_amount',
                                                $promotion->max_discount_amount ?? ''
                                            ) }}"
                                            class="form-control"
                                            placeholder="No maximum"
                                        >

                                    </div>

                                    <small class="form-text text-muted">
                                        Useful for percentage promotions.
                                    </small>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =========================================================
                 RIGHT COLUMN
            ========================================================== --}}
            <div class="col-lg-4">

                {{-- Control Parameters --}}
                <div class="card card-outline card-primary">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-rocket mr-1"></i>
                            Control Parameters
                        </h3>
                    </div>

                    <div class="card-body">

                        {{-- Active --}}
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


                        {{-- Current Usage --}}
                        @if(isset($promotion))

                            <div class="callout callout-info">

                                <h6>
                                    <i class="fas fa-chart-line mr-1"></i>
                                    Current Usage
                                </h6>

                                <strong>
                                    {{ $promotion->usage_count ?? 0 }}
                                </strong>

                                times used.

                            </div>

                        @endif


                        {{-- Buttons --}}
                        <button
                            type="submit"
                            class="btn btn-primary btn-block"
                        >

                            <i class="fas fa-save mr-1"></i>

                            {{ isset($promotion)
                                ? 'Update Promotion'
                                : 'Create Promotion'
                            }}

                        </button>


                        <a
                            href="{{ route('admin.promotions.index') }}"
                            class="btn btn-secondary btn-block"
                        >
                            <i class="fas fa-times mr-1"></i>
                            Cancel
                        </a>

                    </div>

                </div>


                {{-- Validity --}}
                <div class="card card-outline card-primary">

                    <div class="card-header">

                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Promotion Validity
                        </h3>

                    </div>

                    <div class="card-body">

                        {{-- Start Date --}}
                        <div class="form-group">

                            <label for="starts_at">
                                Activation Date
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

                            <small class="form-text text-muted">
                                Leave empty to activate immediately.
                            </small>

                        </div>


                        {{-- Expiration --}}
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

                            <small class="form-text text-muted">
                                Leave empty for no expiration.
                            </small>

                        </div>

                    </div>

                </div>


                {{-- Reset Usage --}}
                @if(isset($promotion) && ($promotion->usage_count ?? 0) > 0)

                    <div class="card card-outline card-warning">

                        <div class="card-header">

                            <h3 class="card-title">
                                <i class="fas fa-sync-alt mr-1"></i>
                                Usage Counter
                            </h3>

                        </div>

                        <div class="card-body">

                            <p class="text-muted small">
                                Current promotion usage:
                                <strong>
                                    {{ $promotion->usage_count }}
                                </strong>
                            </p>

                            <form
                                id="reset-usage-form"
                                action="{{ route('admin.promotions.resetUsage', $promotion) }}"
                                method="POST"
                            >

                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="btn btn-warning btn-block"
                                    onclick="return confirm('Reset promotion usage counter back to 0?')"
                                >

                                    <i class="fas fa-redo mr-1"></i>
                                    Reset Usage Counter

                                </button>

                            </form>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </form>

</div>

@endsection