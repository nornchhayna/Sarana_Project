{{-- resources/views/auth/register.blade.php --}}
<style>
    <style>
    /* public/css/auth.css — include in your layouts/app.blade.php */

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f0f9ff 0%, #f0fdf4 100%);
        padding: 24px 16px;
    }

    .auth-card {
        width: 100%;
        max-width: 440px;
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
        padding: 40px 40px 32px;
    }

    /* Header */
    .auth-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .auth-header h1 {
        font-size: 26px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 6px;
    }

    .auth-header p {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }

    .otp-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    /* Alerts */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .alert-info {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    .alert-success {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Form */
    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"],
    .form-group input[type="tel"] {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        font-size: 15px;
        color: #111827;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
        background: #FFFFFF;
    }

    .form-group input:focus {
        border-color: #007FFF;
        box-shadow: 0 0 0 3px rgba(0, 127, 255, 0.15);
    }

    .has-error input {
        border-color: #ef4444;
    }

    .error-msg {
        display: block;
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
    }

    .hint {
        color: #9ca3af;
        font-size: 12px;
        display: block;
        margin-top: 4px;
    }

    /* OTP input */
    .otp-input {
        text-align: center !important;
        letter-spacing: 8px !important;
        font-size: 28px !important;
        font-weight: 700 !important;
        color: #007FFF !important;
        padding: 14px !important;
    }

    /* Phone input */
    .phone-input-wrap {
        display: flex;
        align-items: center;
        gap: 0;
    }

    .phone-prefix {
        background: #f3f4f6;
        border: 1.5px solid #d1d5db;
        border-right: none;
        border-radius: 8px 0 0 8px;
        padding: 10px 12px;
        font-size: 14px;
        color: #374151;
        white-space: nowrap;
    }

    .phone-input-wrap input {
        border-radius: 0 8px 8px 0 !important;
        flex: 1;
    }

    /* Password wrap */
    .password-wrap {
        position: relative;
    }

    .password-wrap input {
        padding-right: 44px !important;
    }

    .toggle-pw {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 0;
        color: #6b7280;
    }

    /* Inline form-group (remember me) */
    .form-group-inline label {
        font-weight: normal;
        cursor: pointer;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #374151;
    }

    .checkbox-label input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #007FFF;
    }

    /* Forgot link */
    .forgot-link {
        margin-left: auto;
        font-size: 13px;
        color: #007FFF;
        text-decoration: none;
        font-weight: normal;
    }

    .forgot-link:hover {
        text-decoration: underline;
    }

    /* Badges */
    .required {
        color: #ef4444;
    }

    .badge-optional {
        font-size: 11px;
        font-weight: 500;
        padding: 2px 7px;
        background: #f3f4f6;
        color: #6b7280;
        border-radius: 99px;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 20px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all .2s;
        text-decoration: none;
    }

    .btn-primary {
        background: #007FFF;
        color: #FFFFFF;
    }

    .btn-primary:hover {
        background: #0066cc;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 127, 255, 0.3);
    }

    .btn-block {
        width: 100%;
    }

    .btn-link {
        background: none;
        border: none;
        color: #007FFF;
        font-size: 14px;
        cursor: pointer;
        padding: 0;
        font-weight: 600;
    }

    .btn-link:hover {
        text-decoration: underline;
    }

    /* Resend section */
    .resend-section {
        text-align: center;
        margin-top: 20px;
    }

    .resend-section p {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 4px;
    }

    /* Footer */
    .auth-footer {
        text-align: center;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #f3f4f6;
    }

    .auth-footer p,
    .auth-footer a {
        font-size: 14px;
        color: #6b7280;
        text-decoration: none;
    }

    .auth-footer a {
        color: #007FFF;
        font-weight: 600;
    }

    .auth-footer a:hover {
        text-decoration: underline;
    }

    /* Password strength */
    .pw-strength-wrap {
        margin-bottom: 16px;
    }

    .pw-strength-bar {
        background: #e5e7eb;
        border-radius: 99px;
        height: 6px;
        overflow: hidden;
        margin-bottom: 4px;
    }

    .pw-strength-fill {
        height: 100%;
        border-radius: 99px;
        transition: width .3s, background .3s;
    }

    /* ── Register Tabs ─────────────────────────────────────────── */
    .register-tabs {
        display: flex;
        gap: 0;
        margin-bottom: 24px;
        border-radius: 10px;
        overflow: hidden;
        border: 1.5px solid #e5e7eb;
    }

    .reg-tab {
        flex: 1;
        padding: 11px 16px;
        background: #f9fafb;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        color: #6b7280;
        transition: background .2s, color .2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .reg-tab:first-child {
        border-right: 1.5px solid #e5e7eb;
    }

    .reg-tab.active {
        background: #007FFF;
        color: #FFFFFF;
    }

    .reg-tab:not(.active):hover {
        background: #f3f4f6;
        color: #374151;
    }

    .tab-panel {
        animation: fadeIn .2s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(4px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
</style>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Create Account</h1>
            <p>Join us — it only takes a minute.</p>
        </div>

        @if (session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        {{-- ── Tab Switcher ──────────────────────────── --}}
        <div class="register-tabs">
            <button type="button" class="reg-tab active" data-tab="email">
                ✉️ Register with Email
            </button>
            <button type="button" class="reg-tab" data-tab="phone">
                📱 Register with Phone
            </button>
        </div>

        {{-- ══════════════════════════════════
        TAB 1 — EMAIL REGISTRATION
        ══════════════════════════════════ --}}
        <div id="tab-email" class="tab-panel">
            <form method="POST" action="{{ route('auth.register.email') }}" novalidate>
                @csrf
                <input type="hidden" name="_tab" value="email">

                <div class="form-group @error('name') has-error @enderror">
                    <label for="name-email">Full Name <span class="required">*</span></label>
                    <input type="text" id="name-email" name="name" value="{{ old('name') }}" placeholder="Sokha Chan"
                        autocomplete="name" autofocus required>
                    @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group @error('email') has-error @enderror">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com"
                        autocomplete="email" required>
                    <small class="hint">We'll send a 6-digit code to verify this email.</small>
                    @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group @error('password') has-error @enderror">
                    <label for="password-email">Password <span class="required">*</span></label>
                    <div class="password-wrap">
                        <input type="password" id="password-email" name="password"
                            placeholder="Min 8 chars, upper, lower, number" autocomplete="new-password" required>
                        <button type="button" class="toggle-pw" data-target="password-email">👁</button>
                    </div>
                    @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="pw-confirm-email">Confirm Password <span class="required">*</span></label>
                    <div class="password-wrap">
                        <input type="password" id="pw-confirm-email" name="password_confirmation"
                            placeholder="Repeat your password" autocomplete="new-password" required>
                        <button type="button" class="toggle-pw" data-target="pw-confirm-email">👁</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Send Email Verification Code →
                </button>
            </form>
        </div>

        {{-- ══════════════════════════════════
        TAB 2 — PHONE REGISTRATION
        ══════════════════════════════════ --}}
        <div id="tab-phone" class="tab-panel" style="display:none">
            <form method="POST" action="{{ route('auth.register.phone') }}" novalidate>
                @csrf
                <input type="hidden" name="_tab" value="phone">

                <div class="form-group @error('name') has-error @enderror">
                    <label for="name-phone">Full Name <span class="required">*</span></label>
                    <input type="text" id="name-phone" name="name" value="{{ old('name') }}" placeholder="Sokha Chan"
                        autocomplete="name" required>
                    @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group @error('phone') has-error @enderror">
                    <label for="phone">Phone Number <span class="required">*</span></label>
                    <div class="phone-input-wrap">
                        <span class="phone-prefix">🇰🇭 +855</span>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="0xx xxx xxxx"
                            autocomplete="tel" required>
                    </div>
                    <small class="hint">Cambodia number only (Cellcard, Smart, Metfone…). We'll send an SMS
                        code.</small>
                    @error('phone') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group @error('password') has-error @enderror">
                    <label for="password-phone">Password <span class="required">*</span></label>
                    <div class="password-wrap">
                        <input type="password" id="password-phone" name="password"
                            placeholder="Min 8 chars, upper, lower, number" autocomplete="new-password" required>
                        <button type="button" class="toggle-pw" data-target="password-phone">👁</button>
                    </div>
                    @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="pw-confirm-phone">Confirm Password <span class="required">*</span></label>
                    <div class="password-wrap">
                        <input type="password" id="pw-confirm-phone" name="password_confirmation"
                            placeholder="Repeat your password" autocomplete="new-password" required>
                        <button type="button" class="toggle-pw" data-target="pw-confirm-phone">👁</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Send SMS Verification Code →
                </button>
            </form>
        </div>

        {{-- ── Social Sign-up ───────────────────────── --}}
        @if (config('services.google.client_id') || config('services.facebook.client_id'))
            <div class="social-divider">
                <span>or sign up with</span>
            </div>

            <div class="social-buttons">
                @if (config('services.google.client_id'))
                    <a href="{{ route('social.redirect', 'google') }}" class="btn-social btn-google">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
                            <path fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" />
                            <path fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        Google
                    </a>
                @endif

                @if (config('services.facebook.client_id'))
                    <a href="{{ route('social.redirect', 'facebook') }}" class="btn-social btn-facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
                            <path fill="#1877F2"
                                d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.268h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z" />
                        </svg>
                        Facebook
                    </a>
                @endif
            </div>
        @endif

        <div class="auth-footer">
            <p>Already have an account? <a href="{{ route('auth.login') }}">Sign in</a></p>
        </div>
    </div>
</div>

<style>
    .social-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.25rem 0;
        color: #6b7280;
        font-size: 0.875rem;
    }

    .social-divider::before,
    .social-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e5e7eb;
    }

    .social-divider span {
        padding: 0 0.75rem;
    }

    .social-buttons {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .btn-social {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        color: #374151;
        background: #fff;
        text-decoration: none;
        transition: background 0.2s, border-color 0.2s;
    }

    .btn-social:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }
</style>

<script>
    // ── Tab switching ─────────────────────────────────
    const tabs = document.querySelectorAll('.reg-tab');

    function switchTab(tabName) {
        tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === tabName));
        document.getElementById('tab-email').style.display = tabName === 'email' ? 'block' : 'none';
        document.getElementById('tab-phone').style.display = tabName === 'phone' ? 'block' : 'none';
    }

    tabs.forEach(tab => tab.addEventListener('click', () => switchTab(tab.dataset.tab)));

    // Restore tab if validation failed and page reloaded
    @if (old('_tab') === 'phone' || $errors->has('phone'))
        switchTab('phone');
    @endif

    // ── Show / hide password ──────────────────────────
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.textContent = input.type === 'password' ? '👁' : '🙈';
        });
    });
</script>