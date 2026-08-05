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
            Log::info('Auth successful', ['email' => Auth::guard('admin')->user()->email, 'role' => Auth::guard('admin')->user()->role]);

            // Block deactivated accounts
            if (! Auth::guard('admin')->user()->is_active) {
                Auth::guard('admin')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated. Please contact support.',
                ]);
            }

            // Only allow admin role
            if (Auth::guard('admin')->user()->role !== User::ROLE_ADMIN) {
                Log::info('Role check failed', ['role' => Auth::guard('admin')->user()->role]);
                Auth::guard('admin')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Access denied. Admin login is for administrators only.',
                ]);
            }

            $request->session()->regenerate();

            return $this->redirectByRole(Auth::guard('admin')->user());
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
            Log::info('Auth successful', ['email' => Auth::guard('webmaster')->user()->email, 'role' => Auth::guard('webmaster')->user()->role]);

            // Block deactivated accounts
            if (! Auth::guard('webmaster')->user()->is_active) {
                Auth::guard('webmaster')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated. Please contact support.',
                ]);
            }

            // Only allow webmaster role
            if (Auth::guard('webmaster')->user()->role !== User::ROLE_WEBMASTER) {
                Log::info('Role check failed', ['role' => Auth::guard('webmaster')->user()->role]);
                Auth::guard('webmaster')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Access denied. Webmaster login is for webmasters only.',
                ]);
            }

            $request->session()->regenerate();

            return $this->redirectByRole(Auth::guard('webmaster')->user());
        }

        Log::info('Auth failed', ['email' => $request->email]);
        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

 public function logout(Request $request): RedirectResponse
{
    Auth::guard('web')->logout();
    Auth::guard('customer')->logout();
    Auth::guard('admin')->logout();
    Auth::guard('webmaster')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('auth.login')->with('success', 'You have been logged out.');
}

    public function redirectByRole(User $user): RedirectResponse
    {
        Log::info('Redirecting user', ['email' => $user->email, 'role' => $user->role]);

        return match ($user->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_WEBMASTER => redirect()->route('webmaster.dashboard'),
            default => redirect()->route('customer.dashboard'),
        };
    }
}
