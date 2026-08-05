<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Usage in routes:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,webmaster')
     */
    // public function handle(Request $request, Closure $next, string ...$roles): Response
    // {
    //     // Check admin guard first (for admin routes on admin panel)
    //     if (Auth::guard('admin')->check()) {
    //         if (! in_array(Auth::guard('admin')->user()->role, $roles, true)) {
    //             abort(403, 'You do not have access to this area.');
    //         }

    //         return $next($request);
    //     }

    //     // Check webmaster guard (for webmaster routes)
    //     if (Auth::guard('webmaster')->check()) {
    //         if (! in_array(Auth::guard('webmaster')->user()->role, $roles, true)) {
    //             abort(403, 'You do not have access to this area.');
    //         }

    //         return $next($request);
    //     }

    //     // Check customer guard (for customer routes)
    //     if (Auth::guard('customer')->check()) {
    //         if (! in_array(Auth::guard('customer')->user()->role, $roles, true)) {
    //             abort(403, 'You do not have access to this area.');
    //         }

    //         return $next($request);
    //     }

    //     // Check default web guard (fallback)
    //     if (! $request->user()) {
    //         return redirect()->route('auth.login');
    //     }

    //     if (! in_array($request->user()->role, $roles, true)) {
    //         abort(403, 'You do not have access to this area.');
    //     }

    //     return $next($request);
    // }
      /**
     * Usage in routes:
     *   ->middleware('role:admin')
     *   ->middleware('role:webmaster')
     *   ->middleware('role:customer')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $guard = match (true) {
            $request->is('admin*') => 'admin',
            $request->is('webmaster*') => 'webmaster',
            default => 'web',
        };

        if (! Auth::guard($guard)->check()) {
            return redirect()->route('auth.login');
        }

        if (! in_array(Auth::guard($guard)->user()->role, $roles, true)) {
            abort(403, 'You do not have access to this area.');
        }

        return $next($request);
    }
}
