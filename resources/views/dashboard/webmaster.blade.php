@extends('layouts.app')
@section('title', 'Webmaster Dashboard')

@section('content')
    <nav class="topbar">
        <span class="topbar-brand">Ne<span>xus</span></span>
        <div style="display:flex;align-items:center;gap:1rem;">
            <div class="user-chip">
                <img src="{{ auth()->user()->avatar_url }}" alt="">
                <div class="info">
                    <div class="name">{{ auth()->user()->name }}</div>
                    <span class="role-badge role-badge-customer">Customer</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Sign out</button>
            </form>
        </div>
    </nav>

    <div class="dash-content">
        <h2 style="margin-bottom:1.5rem;">Welcome back, {{ auth()->user()->name }} 👋</h2>

        <div class="dash-grid">
            <div class="stat-card">
                <div class="stat-label">Your Orders</div>
                <div class="stat-value" style="color:var(--accent2)">0</div>
                <div class="stat-sub">No orders yet</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Wishlist</div>
                <div class="stat-value" style="color:var(--success)">0</div>
                <div class="stat-sub">Items saved</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Account type</div>
                <div class="stat-value" style="font-size:1.2rem;padding-top:.5rem;">
                    @if(auth()->user()->provider)
                        {{ ucfirst(auth()->user()->provider) }} account
                    @else
                        Email / Password
                    @endif
                </div>
                <div class="stat-sub">Login method</div>
            </div>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Detail</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Name</td>
                        <td>{{ auth()->user()->name }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ auth()->user()->email }}</td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td>{{ auth()->user()->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Gender</td>
                        <td style="text-transform:capitalize">{{ auth()->user()->gender ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Date of birth</td>
                        <td>{{ auth()->user()->date_of_birth?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>{{ auth()->user()->full_address ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td>Role</td>
                        <td><span class="role-badge role-badge-customer">Customer</span></td>
                    </tr>
                    <tr>
                        <td>Member since</td>
                        <td>{{ auth()->user()->created_at->format('M d, Y') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection