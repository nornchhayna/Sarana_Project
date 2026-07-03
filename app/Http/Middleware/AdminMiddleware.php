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
        // Not logged in → send to admin login page
        if (! Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please log in to access the admin panel.');
        }

        // Logged in but not admin or webmaster → 403, do NOT redirect to admin dashboard
        if (! in_array(Auth::user()->role, ['admin', 'webmaster'], true)) {
            abort(403, 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}
