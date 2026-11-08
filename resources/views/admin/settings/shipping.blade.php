@extends('layouts.admin')

@section('title', 'Shipping Settings')
@section('subtitle', 'Configure shipping rates and free shipping')

@section('content')

    {{-- Settings Navigation --}}
    @include('admin.partials.settings-tabs')

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">

        {{-- Shipping Settings Form --}}
        <div class="col-lg-8">

            <form action="{{ route('admin.settings.shipping.update') }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="card card-primary card-outline">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-truck mr-2 text-primary"></i>
                            Shipping & Delivery Rules
                        </h3>
                        <div class="custom-control custom-switch ml-auto">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="shipping_enabled"
                                   name="shipping_enabled"
                                   value="1"
                                   @checked(old('shipping_enabled', $settings['shipping_enabled'] ?? true))>
                            <label class="custom-control-label font-weight-bold text-primary" for="shipping_enabled">
                                Shipping Calculation Active
                            </label>
                        </div>
                    </div>

                    <div class="card-body">

                        {{-- Flat Shipping Rate --}}
                        <div class="form-group">

                            <label for="flat_shipping_rate" class="font-weight-semibold">
                                Standard Flat Shipping Fee
                            </label>

                            <div class="input-group">

                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>

                                <input
                                    type="number"
                                    name="flat_shipping_rate"
                                    id="flat_shipping_rate"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('flat_shipping_rate', $settings['flat_shipping_rate'] ?? 0) }}"
                                    class="form-control @error('flat_shipping_rate') is-invalid @enderror"
                                    placeholder="0.00"
                                >

                            </div>

                            @error('flat_shipping_rate')
                                <span class="text-danger d-block mt-1">
                                    {{ $message }}
                                </span>
                            @enderror

                            <small class="form-text text-muted">
                                Standard delivery fee charged per checkout order (e.g. $1.50 or $2.00). Set to <strong>0</strong> for completely free shipping.
                            </small>

                        </div>


                        {{-- Free Shipping Threshold --}}
                        <div class="form-group">

                            <label for="free_shipping_threshold" class="font-weight-semibold">
                                Free Shipping Threshold Amount
                            </label>

                            <div class="input-group">

                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>

                                <input
                                    type="number"
                                    name="free_shipping_threshold"
                                    id="free_shipping_threshold"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? 0) }}"
                                    class="form-control @error('free_shipping_threshold') is-invalid @enderror"
                                    placeholder="0.00"
                                >

                            </div>

                            @error('free_shipping_threshold')
                                <span class="text-danger d-block mt-1">
                                    {{ $message }}
                                </span>
                            @enderror

                            <small class="form-text text-muted">
                                Orders with a subtotal at or above this amount automatically receive <strong>Free Shipping</strong> (e.g. $50.00). Set to <strong>0</strong> to disable the free shipping threshold.
                            </small>

                        </div>

                        {{-- Delivery Estimation Note --}}
                        <div class="form-group mb-0">
                            <label for="shipping_estimated_days" class="font-weight-semibold">
                                Estimated Delivery Timeframe
                            </label>
                            <input type="text"
                                   name="shipping_estimated_days"
                                   id="shipping_estimated_days"
                                   value="{{ old('shipping_estimated_days', $settings['shipping_estimated_days'] ?? '1-2 business days in Phnom Penh, 2-4 days across provinces') }}"
                                   class="form-control"
                                   placeholder="e.g. 1-2 business days">
                            <small class="form-text text-muted">Shown to customers on the checkout page under the shipping method.</small>
                        </div>

                    </div>

                    {{-- Card Footer --}}
                    <div class="card-footer d-flex justify-content-end">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="fas fa-save mr-1"></i>
                            Save Changes
                        </button>

                    </div>

                </div>

            </form>

        </div>


        {{-- Shipping Information --}}
        <div class="col-lg-4">

            <div class="card card-info card-outline">

                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-info-circle mr-2"></i>
                        Shipping Information
                    </h3>
                </div>

                <div class="card-body">

                    <p class="text-muted">
                        Configure how shipping fees are calculated for your customers in real-time.
                    </p>

                    <div class="callout callout-info mb-3">
                        <h6 class="font-weight-bold">Flat Rate</h6>
                        <p class="small text-muted mb-0">
                            A single flat shipping rate charged across all eligible checkout orders regardless of item count.
                        </p>
                    </div>

                    <div class="callout callout-success mb-0">
                        <h6 class="font-weight-bold">Free Shipping</h6>
                        <p class="small text-muted mb-0">
                            If the customer's cart subtotal meets or exceeds the threshold, shipping fee automatically zeroes out and displays as <strong>Free</strong>.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
