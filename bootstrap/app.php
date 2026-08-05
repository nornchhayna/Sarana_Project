<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\SetAuthGuard;
use App\Http\Middleware\WebmasterMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        then: function () {
            // Admin panel — requires login + admin role
            Route::middleware(['web', 'auth:admin', 'admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            // Webmaster panel — requires login + webmaster role
            Route::middleware(['web', 'auth:webmaster', 'webmaster'])
                ->prefix('webmaster')
                ->name('webmaster.')
                ->group(base_path('routes/webmaster.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
            'admin' => AdminMiddleware::class,
            'webmaster' => WebmasterMiddleware::class,
            'auth.guard' => SetAuthGuard::class,
            'set.locale' => \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->web(\App\Http\Middleware\SetLocale::class);

        // Redirect unauthenticated users to the correct login page
        $middleware->redirectGuestsTo(function ($request) {
            if (str_starts_with($request->path(), 'admin')) {
                return route('admin.login');
            }
            if (str_starts_with($request->path(), 'webmaster')) {
                return route('webmaster.login');
            }

            return route('auth.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
