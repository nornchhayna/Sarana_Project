<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WebmasterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('webmaster')->check()) {
            return redirect()->route('webmaster.login')
                ->with('error', 'Please log in to access the webmaster panel.');
        }

        if (! in_array(Auth::guard('webmaster')->user()->role, ['webmaster'], true)) {
            abort(403, 'You do not have permission to access the webmaster panel.');
        }

        return $next($request);
    }
}
