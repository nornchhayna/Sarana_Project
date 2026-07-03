<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Usage in routes:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,webmaster')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('auth.login');
        }

        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'You do not have access to this area.');
        }

        return $next($request);
    }
}
