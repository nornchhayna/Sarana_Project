<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterPhoneRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\OtpService;
use App\Services\SpeedSmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    // ═══════════════════════════════════════════════════════
    //  REGISTER — Show page (with both tabs)
    // ═══════════════════════════════════════════════════════

    public function showRegister(): View
    {
        return view('auth.register');
    }

    // ───────────────────────────────────────────────────────
    //  REGISTER — Tab 1: Email
    // ───────────────────────────────────────────────────────

    public function registerWithEmail(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $request->session()->put('register_data', [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => null,
            'password' => Hash::make($data['password']),
            'method' => 'email',
        ]);

        $sent = $this->otpService->sendEmailOtp($data['email'], 'register');

        if (! $sent) {
            return back()->withErrors(['email' => 'Failed to send verification email. Please try again.']);
        }

        return redirect()->route('auth.verify-email.show')
            ->with('info', 'A 6-digit code was sent to '.$data['email'].'. Please check your inbox.');
    }

    // ───────────────────────────────────────────────────────
    //  REGISTER — Tab 2: Phone
    // ───────────────────────────────────────────────────────

    public function registerWithPhone(RegisterPhoneRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $sms = app(SpeedSmsService::class);
        $phone = $sms->normalizePhone($data['phone']);

        // Optional: Validate phone
        if (! $this->otpService->isValidCambodianPhone($phone)) {
            return back()->withInput()->withErrors(['phone' => 'Invalid Cambodian phone number.']);
        }

        $request->session()->put('register_data', [
            'name' => $data['name'],
            'email' => null,
            'phone' => $phone,
            'password' => Hash::make($data['password']),
            'method' => 'phone',
        ]);

        $sent = $this->otpService->sendPhoneOtp($phone, 'register');

        if (! $sent) {
            $error = $this->otpService->getLastError()
                ?? 'Failed to send SMS. Please check your number and try again.';

            return back()
                ->withInput()
                ->withErrors(['phone' => $error]);
        }

        return redirect()->route('auth.verify-phone')
            ->with('info', 'A 6-digit SMS code was sent to '.$data['phone'].'.');
    }
    // ═══════════════════════════════════════════════════════
    //  EMAIL OTP VERIFY (email-registered users)
    // ═══════════════════════════════════════════════════════

    public function showVerifyEmail(): View|RedirectResponse
    {
        $data = session('register_data');
        if (! $data || $data['method'] !== 'email') {
            return redirect()->route('auth.register');
        }

        return view('auth.verify-email', ['email' => $data['email']]);
    }

    public function verifyEmail(Request $request): RedirectResponse
    {
        $request->validate(['otp' => ['required', 'digits:6']]);

        $data = $request->session()->get('register_data');

        if (! $data || $data['method'] !== 'email') {
            return redirect()->route('auth.register')
                ->withErrors(['otp' => 'Session expired. Please register again.']);
        }

        $valid = $this->otpService->verify($data['email'], 'email', 'register', $request->otp);

        if (! $valid) {
            return back()->withErrors(['otp' => 'The code is incorrect or has expired.']);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => null,
            'password' => $data['password'],
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $request->session()->forget('register_data');
        Auth::login($user);

        return redirect()->intended(route('customer.dashboard'))
            ->with('success', 'Welcome, '.$user->name.'! Your account is ready.');
    }

    // ═══════════════════════════════════════════════════════
    //  PHONE OTP VERIFY (phone-registered users)
    // ═══════════════════════════════════════════════════════

    public function showVerifyPhone(): View|RedirectResponse
    {
        $data = session('register_data');

        // Also handle post-login phone verify (user ID stored in session)
        $userId = session('phone_verify_user_id');

        if ($data && $data['method'] === 'phone') {
            return view('auth.verify-phone', ['phone' => $data['phone']]);
        }

        if ($userId) {
            $user = User::find($userId);

            return view('auth.verify-phone', ['phone' => $user?->phone]);
        }

        return redirect()->route('auth.register');
    }

    public function verifyPhone(Request $request): RedirectResponse
    {
        $request->validate(['otp' => ['required', 'digits:6']]);

        $data = $request->session()->get('register_data');
        $userId = $request->session()->get('phone_verify_user_id');

        // ── Path A: registering via phone ──────────────────────
        if ($data && isset($data['method']) && $data['method'] === 'phone') {
            $valid = $this->otpService->verify($data['phone'], 'phone', 'register', $request->otp);

            if (! $valid) {
                return back()->withErrors(['otp' => 'The code is incorrect or has expired.']);
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => null,
                'phone' => $data['phone'],
                'password' => $data['password'],
                'phone_verified_at' => now(),
                'is_active' => true,
            ]);

            $request->session()->forget('register_data');
            Auth::login($user);

            return redirect()->intended(route('home'))
                ->with('success', 'Welcome, '.$user->name.'! Your phone is verified.');
        }

        // ── Path B: verifying phone after email registration ───
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('auth.register')
                ->withErrors(['otp' => 'Session expired. Please register again.']);
        }

        $valid = $this->otpService->verify($user->phone, 'phone', 'phone_verify', $request->otp);

        if (! $valid) {
            return back()->withErrors(['otp' => 'The code is incorrect or has expired.']);
        }

        $user->update(['phone_verified_at' => now()]);
        $request->session()->forget('phone_verify_user_id');
        Auth::login($user);

        return redirect()->intended(route('customer.dashboard'))
            ->with('success', 'Welcome, '.$user->name.'! Your account is fully verified.');
    }

    // ═══════════════════════════════════════════════════════
    //  RESEND OTPs
    // ═══════════════════════════════════════════════════════

    public function resendEmailOtp(Request $request): RedirectResponse
    {
        $key = 'resend_email_otp_'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors(['otp' => "Too many attempts. Try again in {$seconds} seconds."]);
        }
        RateLimiter::hit($key, 60);

        $data = $request->session()->get('register_data');
        if (! $data) {
            return redirect()->route('auth.register');
        }

        $this->otpService->sendEmailOtp($data['email'], 'register');

        return back()->with('info', 'A new code was sent to your email.');
    }

    public function resendPhoneOtp(Request $request): RedirectResponse
    {
        $key = 'resend_phone_otp_'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors(['otp' => "Too many attempts. Try again in {$seconds} seconds."]);
        }
        RateLimiter::hit($key, 60);

        $data = $request->session()->get('register_data');
        $userId = $request->session()->get('phone_verify_user_id');

        if ($data && $data['method'] === 'phone') {
            $this->otpService->sendPhoneOtp($data['phone'], 'register');
        } elseif ($userId) {
            $user = User::find($userId);
            if ($user) {
                $this->otpService->sendPhoneOtp($user->phone, 'phone_verify');
            }
        } else {
            return redirect()->route('auth.register');
        }

        return back()->with('info', 'A new SMS code was sent to your phone.');
    }

    // ═══════════════════════════════════════════════════════
    //  LOGIN  (email OR phone)
    // ═══════════════════════════════════════════════════════

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $key = 'login_'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'login' => "Too many login attempts. Please wait {$seconds} seconds.",
            ]);
        }

        $loginField = $request->loginField(); // email or phone
        $loginValue = $request->input('login');

        if ($loginField === 'phone') {
            $sms = app(SpeedSmsService::class);
            $loginValue = $sms->normalizePhone($loginValue);
        }

        $credentials = [
            $loginField => $loginValue,
            'password' => $request->input('password'),
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            return back()->withErrors([
                'login' => 'These credentials do not match our records.',
            ])->onlyInput('login');
        }

        RateLimiter::clear($key);

        $request->session()->regenerate();

        $user = Auth::user();

        // 🚫 block wrong role login pages
        if (in_array($user->role, ['admin', 'webmaster']) && $request->routeIs('auth.login.post')) {
            Auth::logout();

            return back()->withErrors([
                'login' => 'Please use the correct login page for your role.',
            ]);
        }

        // 🚫 inactive account
        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors([
                'login' => 'Your account is deactivated. Please contact support.',
            ]);
        }

        // 🚫 phone not verified
        if (! $user->email && $user->phone && ! $user->isPhoneVerified()) {
            Auth::logout();

            return back()->withErrors([
                'login' => 'Please verify your phone number before logging in.',
            ]);
        }

        // ✅ ROLE-BASED REDIRECT (FIXED)
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'webmaster' => redirect()->route('webmaster.dashboard'),
            default => redirect()->route('home'),
        };
    }

    /*
|--------------------------------------------------------------------------
| LOGOUT (FIXED)
|--------------------------------------------------------------------------
*/

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('info', 'You have been logged out.');
    }
}
