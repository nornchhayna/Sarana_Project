{{-- resources/views/auth/verify-phone.blade.php --}}
@extends('layouts.app')

@section('title', 'Verify Your Phone')

@section('content')
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="otp-icon">📱</div>
                <h1>Verify Your Phone</h1>
                <p>We sent a <strong>6-digit code</strong> via SMS to<br>
                    <strong>{{ $phone }}</strong>
                </p>
            </div>

            @if (session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            <form method="POST" action="{{ route('auth.verify-phone') }}">
                @csrf

                <div class="form-group @error('otp') has-error @enderror">
                    <label for="otp">Enter SMS Code</label>
                    <input type="text" id="otp" name="otp" class="otp-input" maxlength="6" inputmode="numeric"
                        pattern="[0-9]{6}" placeholder="— — — — — —" autocomplete="one-time-code" autofocus required>
                    @error('otp')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                    <small class="hint">The code expires in <strong>10 minutes</strong>.</small>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Verify Phone →
                </button>
            </form>

            <div class="resend-section">
                <p>Didn't receive the SMS?</p>
                <form method="POST" action="{{ route('auth.verify-phone.resend') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-link">Resend SMS</button>
                </form>
            </div>

            <div class="auth-footer">
                <p>You can also <a href="{{ route('customer.dashboard') }}">skip for now</a> and verify later.</p>
            </div>
        </div>
    </div>

    <script>
        const otpInput = document.getElementById('otp');
        otpInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
            if (e.target.value.length === 6) {
                e.target.closest('form').submit();
            }
        });
    </script>
@endsection