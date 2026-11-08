@extends('layouts.admin')

@section('title', 'Edit Webmaster')
@section('page-title', 'Edit Webmaster — ' . $webmaster->name)

@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-edit mr-2"></i>
                    Update Webmaster Details
                </h3>
                <div class="card-tools">
                    <span class="badge {{ $webmaster->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $webmaster->is_active ? 'Active' : 'Suspended' }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.webmasters.update', $webmaster) }}">
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
                                value="{{ old('name', $webmaster->name) }}"
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
                                value="{{ old('email', $webmaster->email) }}"
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
                                value="{{ old('phone', $webmaster->phone) }}"
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
                                <option value="male" @selected(old('gender', $webmaster->gender) === 'male')>Male</option>
                                <option value="female" @selected(old('gender', $webmaster->gender) === 'female')>Female</option>
                                <option value="other" @selected(old('gender', $webmaster->gender) === 'other')>Other</option>
                            </select>
                            @error('gender')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- PASSWORD UPDATE (OPTIONAL) --}}
                    <div class="card card-outline card-secondary mt-3 mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title text-sm">
                                <i class="fas fa-key mr-1"></i> Change Password (Leave blank to keep current)
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
                                        placeholder="Enter new password if changing"
                                    >
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mb-0">
                                    <label for="password_confirmation">Confirm New Password</label>
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
                        {{-- City --}}
                        <div class="col-md-6 form-group">
                            <label for="city">City</label>
                            <input
                                type="text"
                                id="city"
                                name="city"
                                class="form-control @error('city') is-invalid @enderror"
                                value="{{ old('city', $webmaster->city) }}"
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
                                value="{{ old('province', $webmaster->province) }}"
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
                        >{{ old('address', $webmaster->address) }}</textarea>
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
                                {{ old('is_active', $webmaster->is_active) ? 'checked' : '' }}
                            >
                            <label class="custom-control-label font-weight-normal" for="is_active">
                                Account is active
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="{{ route('admin.webmasters.index') }}" class="btn btn-default">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-check mr-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SIDEBAR ACTIONS / QUICK INFO --}}
    <div class="col-lg-4">
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt mr-2"></i> Account Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center font-weight-bold mr-3"
                         style="width: 50px; height: 50px; font-size: 18px;">
                        {{ strtoupper(substr($webmaster->name, 0, 2)) }}
                    </div>
                    <div>
                        <h6 class="mb-0 font-weight-bold">{{ $webmaster->name }}</h6>
                        <small class="text-muted">{{ $webmaster->email }}</small>
                    </div>
                </div>

                <ul class="list-group list-group-flush mb-3 text-sm">
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Account ID:</span>
                        <span class="font-weight-bold">#{{ $webmaster->id }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Role:</span>
                        <span class="badge badge-warning text-dark">Webmaster</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Created:</span>
                        <span>{{ $webmaster->created_at ? $webmaster->created_at->format('M d, Y') : '—' }}</span>
                    </li>
                </ul>

                @if($webmaster->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.webmasters.toggleStatus', $webmaster) }}" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-block {{ $webmaster->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                            <i class="fas {{ $webmaster->is_active ? 'fa-user-slash' : 'fa-user-check' }} mr-1"></i>
                            {{ $webmaster->is_active ? 'Suspend Account' : 'Activate Account' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.webmasters.destroy', $webmaster) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-block btn-outline-danger"
                                onclick="return confirm('Permanently delete webmaster &quot;{{ $webmaster->name }}&quot;? This cannot be undone.')">
                            <i class="fas fa-trash mr-1"></i> Delete Webmaster
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
