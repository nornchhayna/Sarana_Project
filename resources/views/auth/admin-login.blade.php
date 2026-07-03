
<style>
   .auth-shell{
    min-height:100vh;
    display:flex;
    background:#f5f7fb;
}

.auth-side{
    flex:1;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    padding:40px;
}

.auth-panel{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px;
}

.auth-box{
    width:100%;
    max-width:450px;
    background:#fff;
    padding:40px;
    border-radius:16px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.form-control{
    width:100%;
    padding:12px;
    border:1px solid #d1d5db;
    border-radius:8px;
}

.form-control:focus{
    outline:none;
    border-color:#2563eb;
}

.flash{
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
}

.flash-success{
    background:#ecfdf5;
    color:#065f46;
}

.flash-error{
    background:#fef2f2;
    color:#991b1b;
}

.invalid-feedback{
    color:#dc2626;
    font-size:13px;
    margin-top:5px;
}

.check-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.link{
    color:#2563eb;
    text-decoration:none;
}

.link:hover{
    text-decoration:underline;
}
</style>

<div class="auth-shell">

    {{-- Left decorative panel --}}
    <div class="auth-side">
        <div class="auth-orb">
            <span class="auth-logo">⬡</span>
        </div>
        <p class="auth-tagline">
            <strong>Admin Portal</strong><br>
            Secure access for administrators<br>
            and webmasters.
        </p>
    </div>

    {{-- Right form panel --}}
    <div class="auth-panel">
        <div class="auth-box">
            <h1>Admin Sign in</h1>
            <p class="sub">Restricted access. Authorized personnel only.</p>

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

            <form action="{{ route('admin.login') }}" method="POST" novalidate>
                @csrf
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="admin@example.com" autocomplete="email" autofocus>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••" autocomplete="current-password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="check-row">
                    <label class="check-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                    </label>
<a href="{{ route('auth.password.forgot') }}" class="link">Forgot password?</a>                </div>

                <button type="submit" class="btn btn-primary">Sign in →</button>
            </form>

            <div class="auth-footer">
                <p>Need customer access? <a href="{{ route('admin.login') }}" class="link">Go to customer login</a></p>
            </div>
        </div>
    </div>
</div>

