@extends('layouts.admin')

@section('title', 'Add New User')
@section('page-title', 'Create User Account')

@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-plus mr-2"></i>
                    User Information
                </h3>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}">
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
                                placeholder="Full Name"
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
                                placeholder="user@example.com"
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

                        {{-- Role --}}
                        <div class="col-md-6 form-group">
                            <label for="role">Role <span class="text-danger">*</span></label>
                            <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="customer" @selected(old('role') === 'customer')>Customer</option>
                                <option value="webmaster" @selected(old('role') === 'webmaster')>Webmaster</option>
                                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                            </select>
                            @error('role')
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
                                placeholder="Repeat password"
                                required
                            >
                        </div>
                    </div>

                    <div class="row">
                        {{-- Gender --}}
                        <div class="col-md-4 form-group">
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

                        {{-- City --}}
                        <div class="col-md-4 form-group">
                            <label for="city">City</label>
                            <input
                                type="text"
                                id="city"
                                name="city"
                                class="form-control @error('city') is-invalid @enderror"
                                value="{{ old('city') }}"
                            >
                            @error('city')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Province --}}
                        <div class="col-md-4 form-group">
                            <label for="province">Province</label>
                            <input
                                type="text"
                                id="province"
                                name="province"
                                class="form-control @error('province') is-invalid @enderror"
                                value="{{ old('province') }}"
                            >
                            @error('province')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea
                            id="address"
                            name="address"
                            class="form-control @error('address') is-invalid @enderror"
                            rows="2"
                        >{{ old('address') }}</textarea>
                        @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Status Switch --}}
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
                                Active account
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-default">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
