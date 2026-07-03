@extends('layouts.app')
@section('title', 'Webmaster Sign In')

@section('content')
    <div class="auth-shell">

        {{-- Left decorative panel --}}
        <div class="auth-side">
            <div class="auth-orb">
                <span class="auth-logo">⬡</span>
            </div>
            <p class="auth-tagline">
                <strong>Webmaster Portal</strong><br>
                Secure access for webmasters<br>
                to manage content.
            </p>
        </div>

        {{-- Right form panel --}}
        <div class="auth-panel">
            <div class="auth-box">
                <h1>Webmaster Sign in</h1>
                <p class="sub">Restricted access. Authorized webmasters only.</p>

                {{-- Flash messages --}}
                @if(session('status'))
                    <div class="flash flash-success">{{ session('status') }}</div>
                @endif
                @if(session('success'))
                    <div class="flash flash-success">{{ session('success') }}</div>
                @endif
                @if($errors->any() && !$errors->has('email'))
                    <div class="flash flash-error">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('webmaster.login') }}" method="POST" novalidate>
                    @csrf
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="webmaster@example.com"
                            autocomplete="email" autofocus>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="••••••••"
                            autocomplete="current-password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="check-row">
                        <label class="check-label">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            Remember me
                        </label>
                        <a href="{{ route('auth.password.forgot') }}" class="link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary">Sign in →</button>
                </form>

                <div class="auth-footer">
                    <p>Need customer access? <a href="{{ route('webmaster.login') }}" class="link">Go to customer login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection