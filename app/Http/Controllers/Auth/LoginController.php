<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            // Block deactivated accounts
            if (! Auth::guard('customer')->user()->is_active) {
                Auth::guard('customer')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated. Please contact support.',
                ]);
            }
            session(['show_welcome_modal' => true]);
            $request->session()->regenerate();

            return $this->redirectByRole(Auth::guard('customer')->user());
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function showAdminLoginForm(): View
    {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        Log::info('Admin login attempt', ['email' => $request->email]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('admin')->user();
            Log::info('Auth successful', ['email' => $user->email, 'role' => $user->role]);

            // Block deactivated accounts
            if (! $user->is_active) {
                Auth::guard('admin')->logout();
                Auth::guard('web')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated. Please contact support.',
                ]);
            }

            // Only allow admin role
            if ($user->role !== User::ROLE_ADMIN) {
                Log::info('Role check failed', ['role' => $user->role]);
                Auth::guard('admin')->logout();
                Auth::guard('web')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Access denied. Admin login is for administrators only.',
                ]);
            }

            // Sync with web guard so default auth()->user() and Blade views work properly
            Auth::guard('web')->login($user, $request->boolean('remember'));
            Auth::shouldUse('admin');

            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        Log::info('Auth failed', ['email' => $request->email]);
        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function showWebmasterLoginForm(): View
    {
        return view('auth.webmaster-login');
    }

    public function webmasterLogin(Request $request): RedirectResponse
    {
        Log::info('Webmaster login attempt', ['email' => $request->email]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('webmaster')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('webmaster')->user();
            Log::info('Auth successful', ['email' => $user->email, 'role' => $user->role]);

            // Block deactivated accounts
            if (! $user->is_active) {
                Auth::guard('webmaster')->logout();
                Auth::guard('web')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated. Please contact support.',
                ]);
            }

            // Only allow webmaster role
            if ($user->role !== User::ROLE_WEBMASTER) {
                Log::info('Role check failed', ['role' => $user->role]);
                Auth::guard('webmaster')->logout();
                Auth::guard('web')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Access denied. Webmaster login is for webmasters only.',
                ]);
            }

            // Sync with web guard
            Auth::guard('web')->login($user, $request->boolean('remember'));
            Auth::shouldUse('webmaster');

            $request->session()->regenerate();

            return redirect()->route('webmaster.dashboard');
        }

        Log::info('Auth failed', ['email' => $request->email]);
        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

 public function logout(Request $request): RedirectResponse
{
    $user = Auth::guard('admin')->user() ?? Auth::guard('webmaster')->user() ?? Auth::guard('web')->user();

    Auth::guard('web')->logout();
    Auth::guard('customer')->logout();
    Auth::guard('admin')->logout();
    Auth::guard('webmaster')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Redirect to the appropriate login page based on user role
    if ($user && $user->role === 'admin') {
        return redirect()->route('admin.login')->with('success', 'You have been logged out.');
    } elseif ($user && $user->role === 'webmaster') {
        return redirect()->route('webmaster.login')->with('success', 'You have been logged out.');
    }

    return redirect()->route('auth.login')->with('success', 'You have been logged out.');
}
}
