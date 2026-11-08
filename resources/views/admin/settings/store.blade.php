@extends('layouts.admin')

@section('title', 'Store Settings')
@section('subtitle', 'Configure your store contact information')

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

        {{-- Store Settings Form --}}
        <div class="col-lg-8">

            <form action="{{ route('admin.settings.store.update') }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="card card-primary card-outline mb-4">

                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-store mr-2 text-primary"></i>
                            Store Information
                        </h3>
                    </div>

                    <div class="card-body">

                        {{-- Store Email --}}
                        <div class="form-group">
                            <label for="store_email" class="font-weight-semibold">
                                Store Email <span class="text-danger">*</span>
                            </label>

                            <input
                                type="email"
                                name="store_email"
                                id="store_email"
                                value="{{ old('store_email', $settings['store_email'] ?? 'contact@ashahi.com') }}"
                                class="form-control @error('store_email') is-invalid @enderror"
                                placeholder="store@example.com"
                                required
                            >

                            @error('store_email')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror

                            <small class="form-text text-muted">
                                Customers use this email to contact your store. Displayed on footers and invoices.
                            </small>
                        </div>


                        {{-- Store Phone --}}
                        <div class="form-group">
                            <label for="store_phone" class="font-weight-semibold">
                                Store Phone
                            </label>

                            <input
                                type="text"
                                name="store_phone"
                                id="store_phone"
                                value="{{ old('store_phone', $settings['store_phone'] ?? '+855 96 717 7746') }}"
                                class="form-control @error('store_phone') is-invalid @enderror"
                                placeholder="+855 12 345 678"
                            >

                            @error('store_phone')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror

                            <small class="form-text text-muted">
                                Phone number displayed on your storefront header, footer, and contact pages.
                            </small>
                        </div>


                        {{-- Store Address --}}
                        <div class="form-group mb-0">
                            <label for="store_address" class="font-weight-semibold">
                                Store Address
                            </label>

                            <textarea
                                name="store_address"
                                id="store_address"
                                rows="3"
                                class="form-control @error('store_address') is-invalid @enderror"
                                placeholder="Enter your store address"
                            >{{ old('store_address', $settings['store_address'] ?? 'Phnom Penh, Cambodia') }}</textarea>

                            @error('store_address')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror

                            <small class="form-text text-muted">
                                Your physical store or business address.
                            </small>
                        </div>

                    </div>

                </div>

                {{-- Social & Communication Channels --}}
                <div class="card card-info card-outline mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-share-alt mr-2 text-info"></i>
                            Social Media & Channels
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="store_telegram" class="font-weight-semibold">
                                <i class="fab fa-telegram text-primary mr-1"></i> Telegram Channel / Link
                            </label>
                            <input type="text"
                                   name="store_telegram"
                                   id="store_telegram"
                                   value="{{ old('store_telegram', $settings['store_telegram'] ?? '') }}"
                                   class="form-control"
                                   placeholder="https://t.me/yourchannel">
                            <small class="form-text text-muted">Direct link for customer chat and support</small>
                        </div>

                        <div class="form-group">
                            <label for="store_facebook" class="font-weight-semibold">
                                <i class="fab fa-facebook text-primary mr-1"></i> Facebook Page URL
                            </label>
                            <input type="text"
                                   name="store_facebook"
                                   id="store_facebook"
                                   value="{{ old('store_facebook', $settings['store_facebook'] ?? '') }}"
                                   class="form-control"
                                   placeholder="https://facebook.com/yourpage">
                        </div>

                        <div class="form-group mb-0">
                            <label for="store_tiktok" class="font-weight-semibold">
                                <i class="fab fa-tiktok mr-1"></i> TikTok Profile URL
                            </label>
                            <input type="text"
                                   name="store_tiktok"
                                   id="store_tiktok"
                                   value="{{ old('store_tiktok', $settings['store_tiktok'] ?? '') }}"
                                   class="form-control"
                                   placeholder="https://tiktok.com/@yourprofile">
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Save Changes
                        </button>
                    </div>
                </div>

            </form>

        </div>


        {{-- Information Panel --}}
        <div class="col-lg-4">

            <div class="card card-info card-outline">

                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-info-circle mr-2"></i>
                        Store Information
                    </h3>
                </div>

                <div class="card-body">

                    <p class="text-muted">
                        These details are used throughout your ecommerce
                        website.
                    </p>

                    <ul class="list-unstyled mb-0 text-sm">
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Shown in the website footer
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Used on the Contact Us page
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Included in customer email notifications
                        </li>
                        <li>
                            <i class="fas fa-check text-success mr-2"></i>
                            Printed on order invoices and receipts
                        </li>
                    </ul>

                </div>

            </div>

        </div>

    </div>

@endsection
