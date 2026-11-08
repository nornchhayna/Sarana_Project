<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('web')->user();

        // Not logged in → send to admin login page
        if (! $user) {
            return redirect()->route('admin.login')
                ->with('error', 'Please log in to access the admin panel.');
        }

        // Logged in but not admin or webmaster → 403
        if (! in_array($user->role, ['admin', 'webmaster'], true)) {
            abort(403, 'You do not have permission to access the admin panel.');
        }

        // Keep both guards in sync
        if (! Auth::guard('admin')->check()) {
            Auth::guard('admin')->login($user);
        }
        if (! Auth::guard('web')->check()) {
            Auth::guard('web')->login($user);
        }
        Auth::shouldUse('admin');

        return $next($request);
    }
}
