@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card p-4 text-center border-0 shadow-sm" style="background: var(--card, #1A1E2B);">
                <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('default-avatar.png') }}" 
                     class="rounded-circle mb-3 border" width="120" height="120" style="object-fit:cover;">
                <h4 class="text-white">{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100 mb-2">Edit Profile</a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-4 border-0 shadow-sm" style="background: var(--card, #1A1E2B);">
                <h5 class="text-white mb-3">Account Details</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                        <span>Phone:</span> <b>{{ $user->phone ?? 'Not set' }}</b>
                    </li>
                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                        <span>Gender:</span> <b class="text-capitalize">{{ $user->gender ?? 'Not set' }}</b>
                    </li>
                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                        <span>DOB:</span> <b>{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d M, Y') : 'Not set' }}</b>
                    </li>
                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                        <span>Address:</span> <b>{{ $user->address ?? 'Not set' }}</b>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection