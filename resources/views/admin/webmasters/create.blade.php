@extends('layouts.admin')

@section('title', 'Add New Webmaster')
@section('page-title', 'Create Webmaster Account')

@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-plus mr-2"></i>
                    Webmaster Account Information
                </h3>
            </div>

            <form method="POST" action="{{ route('admin.webmasters.store') }}">
                @csrf

                <div class="card-body">
                    <div class="row">
                        {{-- Name --}}
                        <div class="col-md-6 form-group">
                            <label for="name">Full Name <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                placeholder="e.g. John Doe"
                                required
                            >
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6 form-group">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="webmaster@example.com"
                                required
                            >
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- Phone --}}
                        <div class="col-md-6 form-group">
                            <label for="phone">Phone Number</label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone') }}"
                                placeholder="e.g. 012345678"
                            >
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Gender --}}
                        <div class="col-md-6 form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" class="form-control @error('gender') is-invalid @enderror">
                                <option value="">Select Gender</option>
                                <option value="male" @selected(old('gender') === 'male')>Male</option>
                                <option value="female" @selected(old('gender') === 'female')>Female</option>
                                <option value="other" @selected(old('gender') === 'other')>Other</option>
                            </select>
                            @error('gender')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- Password --}}
                        <div class="col-md-6 form-group">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="At least 8 characters"
                                required
                            >
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Password Confirmation --}}
                        <div class="col-md-6 form-group">
                            <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Repeat the password"
                                required
                            >
                        </div>
                    </div>

                    <div class="row">
                        {{-- City --}}
                        <div class="col-md-6 form-group">
                            <label for="city">City</label>
                            <input
                                type="text"
                                id="city"
                                name="city"
                                class="form-control @error('city') is-invalid @enderror"
                                value="{{ old('city') }}"
                                placeholder="e.g. Phnom Penh"
                            >
                            @error('city')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Province --}}
                        <div class="col-md-6 form-group">
                            <label for="province">Province</label>
                            <input
                                type="text"
                                id="province"
                                name="province"
                                class="form-control @error('province') is-invalid @enderror"
                                value="{{ old('province') }}"
                                placeholder="e.g. Kandal"
                            >
                            @error('province')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="form-group">
                        <label for="address">Street Address</label>
                        <textarea
                            id="address"
                            name="address"
                            class="form-control @error('address') is-invalid @enderror"
                            rows="2"
                            placeholder="Full address details"
                        >{{ old('address') }}</textarea>
                        @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="is_active"
                                name="is_active"
                                value="1"
                                {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                            >
                            <label class="custom-control-label font-weight-normal" for="is_active">
                                Account is active (can log into the webmaster portal immediately)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="{{ route('admin.webmasters.index') }}" class="btn btn-default">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Create Webmaster
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ROLE PERMISSIONS INFO --}}
    <div class="col-lg-4">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Webmaster Role Capabilities
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Webmasters are staff members granted access to the dedicated <code>/webmaster</code> portal to oversee store operations:
                </p>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong>Catalog:</strong> Products, Categories, Brands, Colors, Storage variants
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong>Inventory:</strong> Stock quantities and stock adjustments
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong>Marketing:</strong> Banners, Sliders & Promotions
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong>Orders:</strong> Processing, status updates, invoice downloads
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong>Customer Relations:</strong> Product reviews & inquiries
                    </li>
                    <li class="mb-0 text-muted">
                        <i class="fas fa-times-circle text-danger mr-2"></i>
                        <em>Cannot access Admin panel system settings or manage other admins/webmasters.</em>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
