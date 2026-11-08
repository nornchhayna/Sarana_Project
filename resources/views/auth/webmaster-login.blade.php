
<style>
    .wm-login-page,
    .wm-login-page * {
        box-sizing: border-box;
    }

    .wm-login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        position: relative;
        overflow: hidden;
        background: linear-gradient(145deg, #e5f0e9 0%, #edf4fc 48%, #c6dcf4 100%);
        font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: #10264a;
    }

    .wm-login-page::before,
    .wm-login-page::after {
        content: "";
        position: absolute;
        border: 1px solid rgba(37, 99, 235, .08);
        border-radius: 50%;
        pointer-events: none;
    }

    .wm-login-page::before {
        width: 480px;
        height: 480px;
        top: -250px;
        right: -150px;
    }

    .wm-login-page::after {
        width: 350px;
        height: 350px;
        bottom: -200px;
        left: -100px;
    }

    .wm-login-card {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 440px;
        margin-top: 40px;
        padding: 78px 38px 35px;
        border: 1px solid rgba(255, 255, 255, .9);
        border-radius: 28px;
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 25px 55px rgba(35, 76, 125, .15);
    }

    .wm-avatar {
        position: absolute;
        top: -54px;
        left: 50%;
        width: 108px;
        height: 108px;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 10px solid #dce8f4;
        border-radius: 50%;
        background: #092c55;
        color: white;
    }

    .wm-avatar svg {
        width: 48px;
        height: 48px;
        stroke: currentColor;
        fill: none;
        stroke-width: 1.5;
    }

    .wm-heading {
        text-align: center;
        margin-bottom: 30px;
    }

    .wm-heading h1 {
        margin: 0;
        color: #10264a;
        font-size: 28px;
        font-weight: 750;
        letter-spacing: -.8px;
    }

    .wm-heading p {
        margin: 10px 0 0;
        color: #718096;
        font-size: 13px;
        line-height: 1.7;
    }

    .wm-alert {
        padding: 12px 14px;
        margin-bottom: 18px;
        border-radius: 9px;
        font-size: 13px;
        line-height: 1.6;
    }

    .wm-alert-success {
        color: #166534;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    .wm-alert-danger {
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .wm-field {
        margin-bottom: 19px;
    }

    .wm-field label {
        display: block;
        margin-bottom: 8px;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    .wm-input-wrap {
        display: flex;
        height: 50px;
        overflow: hidden;
        border: 1px solid #dce5f0;
        border-radius: 9px;
        background: #f8fafc;
        transition: border-color .2s, box-shadow .2s;
    }

    .wm-input-wrap:focus-within {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
    }

    .wm-input-icon {
        width: 48px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 1px solid #e2e8f0;
        color: #526783;
    }

    .wm-input-icon svg {
        width: 19px;
        height: 19px;
        fill: currentColor;
    }

    .wm-input-wrap input {
        width: 100%;
        min-width: 0;
        padding: 0 14px;
        border: 0;
        outline: none;
        background: transparent;
        color: #10264a;
        font: inherit;
        font-size: 13px;
    }

    .wm-input-wrap input::placeholder {
        color: #94a3b8;
    }

    .wm-input-wrap input.is-invalid {
        background: #fff7f7;
    }

    .wm-error {
        display: block;
        margin-top: 7px;
        color: #dc2626;
        font-size: 12px;
        line-height: 1.5;
    }

    .wm-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin: 3px 0 24px;
    }

    .wm-remember {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #64748b;
        font-size: 12px;
        cursor: pointer;
    }

    .wm-remember input {
        width: 14px;
        height: 14px;
        margin: 0;
        accent-color: #2563eb;
    }

    .wm-link {
        color: #64748b;
        font-size: 12px;
        text-decoration: none;
    }

    .wm-link:hover {
        color: #2563eb;
        text-decoration: underline;
    }

    .wm-login-button {
        width: 100%;
        min-height: 49px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        border: none;
        border-radius: 8px;
        background: #092c55;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1px;
        cursor: pointer;
        transition: background .2s, transform .2s;
    }

    .wm-login-button:hover {
        background: #174a83;
    }

    .wm-login-button:active {
        transform: scale(.99);
    }

    .wm-login-button svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    .wm-footer {
        margin-top: 24px;
        text-align: center;
        color: #94a3b8;
        font-size: 11px;
        line-height: 1.7;
    }

    @media (max-width: 480px) {
        .wm-login-page {
            padding: 35px 18px;
        }

        .wm-login-card {
            max-width: 400px;
            padding: 72px 23px 28px;
            border-radius: 22px;
        }

        .wm-heading h1 {
            font-size: 25px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .wm-input-wrap,
        .wm-login-button {
            transition: none;
        }
    }
</style>

<div class="wm-login-page">
    <main class="wm-login-card">

        <div class="wm-avatar" aria-hidden="true">
            <svg viewBox="0 0 48 48">
                <circle cx="24" cy="15" r="9"/>
                <path d="M6 40c0-10 7-16 18-16s18 6 18 16z"/>
            </svg>
        </div>

        <header class="wm-heading">
            <h1>Webmaster Login</h1>
            <p>Sign in to access your webmaster panel.</p>
        </header>

        @if(session('status'))
            <div class="wm-alert wm-alert-success" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if(session('success'))
            <div class="wm-alert wm-alert-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
            <div class="wm-alert wm-alert-danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('webmaster.login.post') }}" method="POST">
            @csrf

            <div class="wm-field">
                <label for="wm-email">Email address</label>

                <div class="wm-input-wrap">
                    <span class="wm-input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 12a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Zm-8 9a8 8 0 0 1 16 0H4Z"/>
                        </svg>
                    </span>

                    <input
                        id="wm-email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="@error('email') is-invalid @enderror"
                        placeholder="Enter your email"
                        autocomplete="username"
                        autofocus
                        required
                    >
                </div>

                @error('email')
                    <small class="wm-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="wm-field">
                <label for="wm-password">Password</label>

                <div class="wm-input-wrap">
                    <span class="wm-input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M7 10V7a5 5 0 0 1 10 0v3M5 10h14v11H5z"/>
                            <circle cx="12" cy="15" r="1.2"/>
                        </svg>
                    </span>

                    <input
                        id="wm-password"
                        type="password"
                        name="password"
                        class="@error('password') is-invalid @enderror"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                @error('password')
                    <small class="wm-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="wm-options">
                <label class="wm-remember" for="wm-remember">
                    <input
                        id="wm-remember"
                        type="checkbox"
                        name="remember"
                        value="1"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    Remember me
                </label>

                <a href="{{ route('auth.password.forgot') }}"
                   class="wm-link">
                    Forgot Password?
                </a>
            </div>

            <button type="submit" class="wm-login-button">
                LOGIN
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 12h14M13 5l7 7-7 7"/>
                </svg>
            </button>
        </form>

        

    </main>
</div>
