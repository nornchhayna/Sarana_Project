<style>
    *{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Inter,sans-serif;
    background:#f5f7fb;
}

.wm-login{
    display:flex;
    min-height:100vh;
}

/* Left */

.wm-left{
    width:45%;
    background:linear-gradient(135deg,#3b82f6,#1e3a8a);
    color:#fff;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    padding:60px;
}

.wm-logo{
    width:90px;
    height:90px;
    border-radius:50%;
    background:rgba(255,255,255,.2);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:42px;
    margin-bottom:25px;
}

.wm-left h2{
    font-size:38px;
    margin-bottom:15px;
}

.wm-left p{
    font-size:18px;
    line-height:1.8;
    max-width:420px;
}

/* Right */

.wm-right{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px;
}

.wm-card{
    width:100%;
    max-width:450px;
    background:#fff;
    border-radius:18px;
    padding:40px;
    box-shadow:0 20px 50px rgba(0,0,0,.08);
}

.wm-card h1{
    font-size:32px;
    color:#111827;
}

.wm-subtitle{
    color:#6b7280;
    margin:10px 0 30px;
}

.form-group{
    margin-bottom:20px;
}

.form-group label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
}

.form-group input{
    width:100%;
    padding:14px 16px;
    border:1px solid #d1d5db;
    border-radius:10px;
    font-size:15px;
    transition:.3s;
}

.form-group input:focus{
    outline:none;
    border-color:#2563eb;
}

.text-danger{
    color:#dc2626;
    font-size:13px;
}

.wm-options{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin:20px 0 25px;
    font-size:14px;
}

.wm-options a{
    text-decoration:none;
    color:#2563eb;
}

.btn-login{
    width:100%;
    padding:15px;
    border:none;
    border-radius:10px;
    background:#2563eb;
    color:#fff;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}

.btn-login:hover{
    background:#1d4ed8;
}

.alert{
    padding:12px 16px;
    border-radius:8px;
    margin-bottom:20px;
}

.success{
    background:#dcfce7;
    color:#166534;
}

.danger{
    background:#fee2e2;
    color:#991b1b;
}

.wm-footer{
    text-align:center;
    margin-top:25px;
    color:#6b7280;
}

.wm-footer a{
    color:#2563eb;
    text-decoration:none;
    font-weight:600;
}

/* Responsive */

@media(max-width:900px){

    .wm-login{
        flex-direction:column;
    }

    .wm-left{
        width:100%;
        padding:50px 30px;
    }

    .wm-right{
        padding:30px 20px;
    }

}
</style>

<div class="wm-login">

    {{-- Left Side --}}
    <div class="wm-left">
        <div class="wm-logo">
            ⬡
        </div>

        <h2>Webmaster Portal</h2>

        <p>
            Secure access for authorized webmasters to manage
            products, users, website content, and system settings.
        </p>
    </div>

    {{-- Right Side --}}
    <div class="wm-right">

        <div class="wm-card">

            <h1>Webmaster Sign In</h1>

            <p class="wm-subtitle">
                Restricted access. Authorized webmasters only.
            </p>

            @if(session('status'))
                <div class="alert success">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any() && !$errors->has('email'))
                <div class="alert danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('webmaster.login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Email Address</label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="webmaster@example.com">

                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password</label>

                    <input
                        type="password"
                        name="password"
                        placeholder="••••••••">

                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="wm-options">

                    <label>
                        <input
                            type="checkbox"
                            name="remember"
                            {{ old('remember') ? 'checked' : '' }}>

                        Remember me
                    </label>

                    <a href="{{ route('auth.password.forgot') }}">
                        Forgot password?
                    </a>

                </div>

                <button class="btn-login" type="submit">
                    Sign In →
                </button>

            </form>

            <div class="wm-footer">
                Need customer access?
                <a href="{{ route('auth.login') }}">
                    Customer Login
                </a>
            </div>

        </div>

    </div>

</div>
