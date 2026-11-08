@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User — ' . $user->name)

@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-edit mr-2"></i>
                    Edit User Profile
                </h3>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

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
                                value="{{ old('name', $user->name) }}"
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
                                value="{{ old('email', $user->email) }}"
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
                                value="{{ old('phone', $user->phone) }}"
                            >
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div class="col-md-6 form-group">
                            <label for="role">Role <span class="text-danger">*</span></label>
                            <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="customer" @selected(old('role', $user->role) === 'customer')>Customer</option>
                                <option value="webmaster" @selected(old('role', $user->role) === 'webmaster')>Webmaster</option>
                                <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- PASSWORD UPDATE (OPTIONAL) --}}
                    <div class="card card-outline card-secondary mt-2 mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title text-sm">
                                <i class="fas fa-key mr-1"></i> Change Password (Leave blank to keep existing password)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group mb-0">
                                    <label for="password">New Password</label>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Leave blank to keep current"
                                    >
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mb-0">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="form-control"
                                        placeholder="Repeat new password"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Gender --}}
                        <div class="col-md-4 form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" class="form-control @error('gender') is-invalid @enderror">
                                <option value="">Select Gender</option>
                                <option value="male" @selected(old('gender', $user->gender) === 'male')>Male</option>
                                <option value="female" @selected(old('gender', $user->gender) === 'female')>Female</option>
                                <option value="other" @selected(old('gender', $user->gender) === 'other')>Other</option>
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
                                value="{{ old('city', $user->city) }}"
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
                                value="{{ old('province', $user->province) }}"
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
                        >{{ old('address', $user->address) }}</textarea>
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
                                {{ old('is_active', $user->is_active) ? 'checked' : '' }}
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
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-check mr-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
