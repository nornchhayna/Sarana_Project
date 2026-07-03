@extends('layouts.app')
@section('title', 'Edit Profile')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        {{-- Profile Info --}}
        <div class="col-lg-8">
            <div class="card p-4 border-0 shadow-sm" style="background: var(--card, #1A1E2B);">
                <h4 class="text-white mb-4">Edit Profile</h4>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-white">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="text-white">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-white">Gender</label>
                            <select name="gender" class="form-control">
                                <option value="">Select...</option>
                                <option value="male" @selected($user->gender == 'male')>Male</option>
                                <option value="female" @selected($user->gender == 'female')>Female</option>
                                <option value="other" @selected($user->gender == 'other')>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="text-white">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ $user->date_of_birth }}">
                        </div>
                        <div class="col-md-4">
                            <label class="text-white">Avatar</label>
                            <input type="file" name="avatar" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    @if($user->avatar)
                        <a href="{{ route('profile.avatar.remove') }}" class="btn btn-danger">Remove Avatar</a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Password Change --}}
        <div class="col-lg-4">
            <div class="card p-4 border-0 shadow-sm" style="background: var(--card, #1A1E2B);">
                <h4 class="text-white mb-4">Change Password</h4>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="text-white">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white">New Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection