<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in as admin or webmaster
        if (Auth::check()) {
            $user = Auth::user();
            
            // If user is admin or webmaster, redirect them to appropriate dashboard
            if (in_array($user->role, ['admin', 'webmaster'], true)) {
                // Don't redirect if already on admin or webmaster routes
                if (!str_starts_with($request->path(), 'admin') && !str_starts_with($request->path(), 'webmaster')) {
                    if ($user->role === 'admin') {
                        return redirect()->route('admin.dashboard')->with('info', 'You are logged in as Admin. Please use the Admin panel.');
                    } else {
                        return redirect()->route('webmaster.dashboard')->with('info', 'You are logged in as Webmaster. Please use the Webmaster panel.');
                    }
                }
            }
        }

        return $next($request);
    }
}
