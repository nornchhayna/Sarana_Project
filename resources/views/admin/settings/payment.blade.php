@extends('layouts.admin')

@section('title', 'Payment Settings')
@section('page-title', 'Payment Settings')

@section('content')

    @include('admin.partials.settings-tabs')

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('admin.settings.payment.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Left column: Payment gateways & currency --}}
            <div class="col-lg-8">

                {{-- Currency Card --}}
                <div class="card card-primary card-outline mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-money-bill-wave mr-2 text-primary"></i> Store Currency
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label for="currency" class="font-weight-semibold">Default Store Currency</label>
                            <select name="currency" id="currency" class="form-control select2bs4 @error('currency') is-invalid @enderror">
                                <option value="USD" @selected(old('currency', $settings['currency'] ?? 'USD') === 'USD')>
                                    USD - United States Dollar ($) [Recommended for Bakong USD]
                                </option>
                                <option value="KHR" @selected(old('currency', $settings['currency'] ?? '') === 'KHR')>
                                    KHR - Cambodian Riel (៛)
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                Bakong KHQR dynamic generation will generate QR codes in this currency (USD 840 or KHR 116).
                            </small>
                            @error('currency')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Bakong KHQR Card --}}
                <div class="card card-success card-outline mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-qrcode mr-2 text-success"></i> Bakong KHQR Integration
                        </h3>
                        <div class="custom-control custom-switch ml-auto">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="khqr_enabled"
                                   name="khqr_enabled"
                                   value="1"
                                   @checked(old('khqr_enabled', $settings['khqr_enabled'] ?? true))>
                            <label class="custom-control-label font-weight-bold text-success" for="khqr_enabled">
                                Enable KHQR
                            </label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="bakong_account_id" class="font-weight-semibold">
                                    Bakong Account ID <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                    </div>
                                    <input type="text"
                                           name="bakong_account_id"
                                           id="bakong_account_id"
                                           class="form-control @error('bakong_account_id') is-invalid @enderror"
                                           value="{{ old('bakong_account_id', $settings['bakong_account_id'] ?? 'non_chhayna@bkrt') }}"
                                           placeholder="e.g. your_account@bank"
                                           required>
                                </div>
                                <small class="form-text text-muted">e.g. <code>non_chhayna@bkrt</code> or bank-linked account ID</small>
                                @error('bakong_account_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="bakong_merchant_name" class="font-weight-semibold">
                                    Merchant Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-store"></i></span>
                                    </div>
                                    <input type="text"
                                           name="bakong_merchant_name"
                                           id="bakong_merchant_name"
                                           class="form-control @error('bakong_merchant_name') is-invalid @enderror"
                                           value="{{ old('bakong_merchant_name', $settings['bakong_merchant_name'] ?? 'Ashahi Store') }}"
                                           placeholder="e.g. Ashahi Store"
                                           required>
                                </div>
                                <small class="form-text text-muted">Displayed on the customer's banking app scan screen</small>
                                @error('bakong_merchant_name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="bakong_merchant_city" class="font-weight-semibold">
                                    Merchant City <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                                    </div>
                                    <input type="text"
                                           name="bakong_merchant_city"
                                           id="bakong_merchant_city"
                                           class="form-control @error('bakong_merchant_city') is-invalid @enderror"
                                           value="{{ old('bakong_merchant_city', $settings['bakong_merchant_city'] ?? 'Phnom Penh') }}"
                                           placeholder="e.g. Phnom Penh"
                                           required>
                                </div>
                                @error('bakong_merchant_city')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="font-weight-semibold">Bakong API Status</label>
                                <div>
                                    @if(!empty($settings['bakong_api_token']))
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle"></i> Token Configured</span>
                                    @else
                                        <span class="badge badge-warning px-2 py-1"><i class="fas fa-exclamation-triangle"></i> Token Missing</span>
                                    @endif
                                    <small class="d-block text-muted mt-1">Automatic verification checks Bakong server MD5 status</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label for="bakong_api_token" class="font-weight-semibold">
                                Bakong API Token (JWT)
                            </label>
                            <textarea name="bakong_api_token"
                                      id="bakong_api_token"
                                      rows="3"
                                      class="form-control font-monospace @error('bakong_api_token') is-invalid @enderror"
                                      placeholder="Paste your renewed Bakong Bearer Token (eyJhbGciOi...)">{{ old('bakong_api_token', $settings['bakong_api_token'] ?? '') }}</textarea>
                            <small class="form-text text-muted">
                                Renewed tokens can be pasted directly here. Saving will instantly update the application configuration and <code>.env</code> file.
                            </small>
                            @error('bakong_api_token')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Cash on Delivery Card --}}
                <div class="card card-warning card-outline mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-hand-holding-usd mr-2 text-warning"></i> Cash on Delivery (COD)
                        </h3>
                        <div class="custom-control custom-switch ml-auto">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="cod_enabled"
                                   name="cod_enabled"
                                   value="1"
                                   @checked(old('cod_enabled', $settings['cod_enabled'] ?? true))>
                            <label class="custom-control-label font-weight-bold text-dark" for="cod_enabled">
                                Enable COD
                            </label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label for="cod_instructions" class="font-weight-semibold">COD Instructions for Customers</label>
                            <textarea name="cod_instructions"
                                      id="cod_instructions"
                                      rows="2"
                                      class="form-control"
                                      placeholder="e.g. Please prepare the exact cash amount for our delivery driver upon order arrival.">{{ old('cod_instructions', $settings['cod_instructions'] ?? 'Pay with cash directly to our delivery courier upon receiving your package.') }}</textarea>
                            <small class="form-text text-muted">
                                Displayed during checkout when customer selects Cash on Delivery.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="text-right mb-4">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-save mr-1"></i> Save Payment Settings
                    </button>
                </div>

            </div>

            {{-- Right column: Information & Tips --}}
            <div class="col-lg-4">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-info-circle mr-1"></i> KHQR Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-success mb-3">
                            <h6 class="font-weight-bold"><i class="fas fa-bolt text-success"></i> Live Bakong Verification</h6>
                            <p class="small text-muted mb-0">
                                When customers scan the KHQR on checkout, the system polls the National Bank of Cambodia (NBC) Bakong API in real-time. Once the transfer matches the MD5 hash, the order automatically transitions to <strong>Confirmed / Paid</strong>.
                            </p>
                        </div>

                        <div class="callout callout-info mb-3">
                            <h6 class="font-weight-bold"><i class="fas fa-key text-info"></i> Token Expiry & Renewal</h6>
                            <p class="small text-muted mb-0">
                                NBC Bakong developer tokens expire periodically. Whenever you renew your API token from the Bakong Developer Portal, simply paste it in the <strong>Bakong API Token</strong> field on this page and click Save.
                            </p>
                        </div>

                        <div class="callout callout-warning mb-0">
                            <h6 class="font-weight-bold"><i class="fas fa-shield-alt text-warning"></i> Checkout Redundancy</h6>
                            <p class="small text-muted mb-0">
                                Both KHQR and Cash on Delivery can be toggled on or off. If KHQR is disabled or undergoing maintenance, customers can seamlessly order via COD.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
