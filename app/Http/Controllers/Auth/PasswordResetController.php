<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    // ─── Step 1: Show "Forgot Password" form ─────────────────────────────────

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    // ─── Step 2: Send OTP to email ────────────────────────────────────────────

    public function sendResetOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'We could not find an account with that email address.',
        ]);

        $key = 'pwd_reset_'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors(['email' => "Too many attempts. Try again in {$seconds} seconds."]);
        }
        RateLimiter::hit($key, 60);

        $email = $request->input('email');
        $user = User::where('email', $email)->firstOrFail();

        if (! $user->is_active) {
            return back()->withErrors(['email' => 'This account is deactivated. Contact support.']);
        }

        $this->otpService->sendEmailOtp($email, 'reset_password');

        $request->session()->put('password_reset_email', $email);

        return redirect()->route('auth.password.verify-otp')
            ->with('info', 'A 6-digit reset code was sent to '.$email.'.');
    }

    // ─── Step 3: Show OTP input ───────────────────────────────────────────────

    public function showVerifyOtp(): View
    {
        if (! session()->has('password_reset_email')) {
            return redirect()->route('auth.password.forgot');
        }

        return view('auth.reset-verify-otp', [
            'email' => session('password_reset_email'),
        ]);
    }

    // ─── Step 4: Verify OTP ───────────────────────────────────────────────────

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate(['otp' => ['required', 'digits:6']]);

        $email = $request->session()->get('password_reset_email');
        if (! $email) {
            return redirect()->route('auth.password.forgot')
                ->withErrors(['otp' => 'Session expired. Please try again.']);
        }

        $valid = $this->otpService->verify($email, 'email', 'reset_password', $request->otp);

        if (! $valid) {
            return back()->withErrors(['otp' => 'The code is incorrect or has expired.']);
        }

        // Mark OTP as verified — allow password change
        $request->session()->put('password_reset_verified', true);

        return redirect()->route('auth.password.reset-form');
    }

    // ─── Step 5: Show new password form ──────────────────────────────────────

    public function showResetForm(): View
    {
        if (! session()->get('password_reset_verified') || ! session()->has('password_reset_email')) {
            return redirect()->route('auth.password.forgot');
        }

        return view('auth.reset-password');
    }

    // ─── Step 6: Set new password ─────────────────────────────────────────────

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->uncompromised(),
            ],
        ]);

        $email = $request->session()->get('password_reset_email');
        $verified = $request->session()->get('password_reset_verified');

        if (! $email || ! $verified) {
            return redirect()->route('auth.password.forgot')
                ->withErrors(['password' => 'Session expired. Please start over.']);
        }

        $user = User::where('email', $email)->firstOrFail();
        $user->update([
            'password' => Hash::make($request->password),
            'remember_token' => null,
        ]);

        // Clear all reset session keys
        $request->session()->forget(['password_reset_email', 'password_reset_verified']);

        return redirect()->route('auth.login')
            ->with('success', 'Your password has been reset. Please log in.');
    }

    // ─── Resend reset OTP ─────────────────────────────────────────────────────

    public function resendOtp(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('auth.password.forgot');
        }

        $key = 'pwd_resend_'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors(['otp' => "Too many attempts. Try again in {$seconds} seconds."]);
        }
        RateLimiter::hit($key, 60);

        $this->otpService->sendEmailOtp($email, 'reset_password');

        return back()->with('info', 'A new code has been sent to your email.');
    }
}
