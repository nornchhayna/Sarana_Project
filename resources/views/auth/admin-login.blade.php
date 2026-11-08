
<style>
    /* ===== Page layout ===== */
    .auth-page {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        overflow: hidden;
        background: #f1f1f1;
        font-family: Arial, Helvetica, sans-serif;
    }

    /* ===== Blue wave ===== */
    .auth-wave {
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 56%;
        min-height: 260px;
        z-index: 0;
    }

    /* ===== Login card ===== */
    .auth-card {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 330px;
        margin-top: 35px;
        padding: 62px 32px 24px;
        border-radius: 4px;
        background: #e7e7e7;
      
    }

    /* ===== Circular user icon ===== */
    .auth-avatar {
        position: absolute;
        top: -41px;
        left: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 82px;
        height: 82px;
        transform: translateX(-50%);
        border-radius: 50%;
        background: #3868b3;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .auth-avatar svg {
        width: 38px;
        height: 38px;
        stroke: #d6dce7;
        stroke-width: 2;
        fill: none;
    }

    /* ===== Heading ===== */
    .auth-heading {
        margin-bottom: 22px;
        text-align: center;
    }

    .auth-heading h1 {
        margin: 0;
        color: #333333;
        font-size: 21px;
        font-weight: 600;
    }

    .auth-heading p {
        margin: 7px 0 0;
        color: #777777;
        font-size: 12px;
        line-height: 1.6;
    }

    /* ===== Form fields ===== */
    .auth-field {
        position: relative;
        margin-bottom: 12px;
    }

    .auth-input {
        box-sizing: border-box;
        display: block;
        width: 100%;
        height: 43px;
        padding: 0 12px;
        border: 1px solid #d5d5d9;
        border-radius: 3px;
        background: #f8f8fa;
        color: #333333;
        font-family: inherit;
        font-size: 12px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .auth-input::placeholder {
        color: #555555;
        opacity: 1;
    }

    .auth-input:focus {
        outline: none;
        border-color: #3868b3;
        box-shadow: 0 0 0 3px rgba(56, 104, 179, 0.12);
    }

    .auth-input.is-invalid {
        border-color: #dc2626;
    }

    /* ===== Remember / forgot ===== */
    .auth-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin: 5px 0 22px;
    }

    .auth-remember {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #555555;
        font-size: 10px;
        cursor: pointer;
    }

    .auth-remember input {
        width: 12px;
        height: 12px;
        margin: 0;
        accent-color: #3868b3;
    }

    .auth-link {
        color: #555555;
        font-size: 10px;
        text-decoration: none;
    }

    .auth-link:hover {
        color: #24549b;
        text-decoration: underline;
    }

    /* ===== Login button ===== */
    .auth-submit {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 42px;
        padding: 10px 15px;
        border: none;
        border-radius: 3px;
        background: #3868b3;
        color: #ffffff;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.2px;
        cursor: pointer;
        transition: background 0.2s, transform 0.2s;
    }

    .auth-submit:hover {
        background: #2b579b;
    }

    .auth-submit:active {
        transform: scale(0.99);
    }

    .auth-submit:focus-visible {
        outline: 3px solid rgba(56, 104, 179, 0.3);
        outline-offset: 3px;
    }

    /* ===== Register / footer ===== */
    .auth-footer {
        margin-top: 15px;
        text-align: center;
    }

    .auth-footer p {
        margin: 0;
        color: #666666;
        font-size: 10px;
    }

    .auth-footer .auth-link {
        color: #3868b3;
    }

    /* ===== Alerts ===== */
    .auth-alert {
        padding: 10px 12px;
        margin-bottom: 14px;
        border: 1px solid transparent;
        border-radius: 4px;
        font-size: 11px;
        line-height: 1.5;
    }

    .auth-alert-success {
        color: #166534;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .auth-alert-error {
        color: #991b1b;
        background: #fef2f2;
        border-color: #fecaca;
    }

    .auth-error {
        margin-top: 5px;
        color: #dc2626;
        font-size: 11px;
        line-height: 1.5;
    }

    /* ===== Mobile responsive ===== */
    @media (max-width: 480px) {
        .auth-page {
            padding: 35px 20px;
        }

        .auth-wave {
            height: 42%;
            min-height: 220px;
        }

        .auth-card {
            max-width: 330px;
            padding-right: 25px;
            padding-left: 25px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .auth-input,
        .auth-submit {
            transition: none;
        }
    }
</style>

<div class="auth-page">

   

    {{-- Login card --}}
    <div class="auth-card">

        {{-- User avatar --}}
        <div class="auth-avatar">
            <svg viewBox="0 0 40 40" aria-hidden="true">
                <circle cx="20" cy="12" r="5.5"/>
                <path d="M10 31v-2.5a10 10 0 0 1 20 0V31z"
                      stroke-linejoin="round"/>
            </svg>
        </div>

        <div class="auth-heading">
            <h1>Admin Login</h1>
            <p>Sign in to access your administration panel.</p>
        </div>

        {{-- Success messages --}}
        @if(session('status'))
            <div class="auth-alert auth-alert-success" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if(session('success'))
            <div class="auth-alert auth-alert-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        {{-- General errors --}}
        @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
            <div class="auth-alert auth-alert-error" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf

            {{-- Email --}}
            <div class="auth-field">
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    class="auth-input @error('email') is-invalid @enderror"
                    placeholder="Email address"
                    autocomplete="username"
                    aria-label="Email address"
                    aria-describedby="email-error"
                    autofocus
                    required
                >

                @error('email')
                    <div id="email-error" class="auth-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="auth-field">
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="auth-input @error('password') is-invalid @enderror"
                    placeholder="Password"
                    autocomplete="current-password"
                    aria-label="Password"
                    aria-describedby="password-error"
                    required
                >

                @error('password')
                    <div id="password-error" class="auth-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Remember / forgot --}}
            <div class="auth-options">
                <label class="auth-remember" for="remember">
                    <input
                        id="remember"
                        type="checkbox"
                        name="remember"
                        value="1"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    Remember me
                </label>

            </div>

            {{-- Submit --}}
            <button type="submit" class="auth-submit">
                SIGN IN &nbsp; &rarr;
            </button>
        </form>

       

    </div>
</div>
