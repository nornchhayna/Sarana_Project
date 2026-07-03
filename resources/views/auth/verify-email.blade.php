{{-- resources/views/auth/verify-email.blade.php --}}
@extends('layouts.app')

@section('title', 'Verify Your Email')

@section('content')
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="otp-icon">✉️</div>
                <h1>Check Your Email</h1>
                <p>We sent a <strong>6-digit code</strong> to<br>
                    <strong>{{ $email }}</strong>
                </p>
            </div>

            @if (session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('auth.verify-email') }}">
                @csrf

                <div class="form-group @error('otp') has-error @enderror">
                    <label for="otp">Enter Verification Code</label>
                    <input type="text" id="otp" name="otp" class="otp-input" maxlength="6" inputmode="numeric"
                        pattern="[0-9]{6}" placeholder="— — — — — —" autocomplete="one-time-code" autofocus required>
                    @error('otp')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                    <small class="hint">The code expires in <strong>10 minutes</strong>.</small>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Verify Email →
                </button>
            </form>

            <div class="resend-section">
                <p>Didn't receive the code?</p>
                <form method="POST" action="{{ route('auth.verify-email.resend') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-link">Resend Code</button>
                </form>
            </div>

            <div class="auth-footer">
                <a href="{{ route('auth.register') }}">← Back to Register</a>
            </div>
        </div>
    </div>

    <script>
        // Auto-format OTP input: only digits, auto-submit at 6
        const otpInput = document.getElementById('otp');
        otpInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
            if (e.target.value.length === 6) {
                e.target.closest('form').submit();
            }
        });
    </script>
@endsection