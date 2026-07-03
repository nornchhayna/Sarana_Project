@extends('layouts.app')
@section('title', 'Set New Password')

@section('content')
    <div class="auth-shell">
        <div class="auth-side">
            <div class="auth-orb"><span class="auth-logo">⬡</span></div>
            <p class="auth-tagline">
                Choose a <strong>strong password</strong><br>
                to keep your account secure.
            </p>
        </div>

        <div class="auth-panel">
            <div class="auth-box">
                <h1>New password</h1>
                <p class="sub">Almost there — set your new password below.</p>

                @if($errors->any())
                    <div class="flash flash-error">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('auth.password.reset') }}" method="POST">
                    @csrf
                    {{-- <input type="hidden" name="token" value="{{ $token }}"> --}}

                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" value="{{ $email ?? old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com"
                            autocomplete="email" autofocus>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">New password</label>
                        <input id="password" name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="At least 8 characters"
                            autocomplete="new-password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                            placeholder="Repeat password" autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary">Update password →</button>
                </form>
            </div>
        </div>
    </div>
@endsection