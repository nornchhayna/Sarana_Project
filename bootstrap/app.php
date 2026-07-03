<?php

// use Illuminate\Foundation\Application;
// use Illuminate\Foundation\Configuration\Exceptions;
// use Illuminate\Foundation\Configuration\Middleware;

// return Application::configure(basePath: dirname(__DIR__))
//     ->withRouting(
//         web: __DIR__.'/../routes/web.php',
//         api: __DIR__.'/../routes/api.php',
//         then: function () {
//             Route::middleware(['web', 'auth', 'admin'])
//                  ->prefix('admin')
//                  ->name('admin.')
//                  ->group(base_path('routes/admin.php'));
//         },
//     )
//     ->withMiddleware(function (Middleware $middleware): void {
//         $middleware->alias([
//             'role'  => \App\Http\Middleware\CheckRole::class,
//             'admin' => \App\Http\Middleware\AdminMiddleware::class,
//         ]);

//         $middleware->redirectGuestsTo(function ($request) {
//             if (str_starts_with($request->path(), 'dashboard/admin')) {
//                 return route('admin.login');
//             }
//             if (str_starts_with($request->path(), 'dashboard/webmaster')) {
//                 return route('webmaster.login');
//             }
//             return route('auth.login');
//         });
//     })
//     ->withExceptions(function (Exceptions $exceptions): void {
//         //
//     })->create();

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
     ->withRouting(
         web: __DIR__.'/../routes/web.php',
         api: __DIR__.'/../routes/api.php',
         then: function () {
             // Admin panel — requires login + admin/webmaster role
             Route::middleware(['web', 'auth', 'admin'])
                 ->prefix('admin')
                 ->name('admin.')
                 ->group(base_path('routes/admin.php'));

             // Webmaster panel (optional — add routes/webmaster.php if needed)
             // Route::middleware(['web', 'auth', 'role:webmaster,admin'])
             //      ->prefix('webmaster')
             //      ->name('webmaster.')
             //      ->group(base_path('routes/webmaster.php'));
         },
     )
     ->withMiddleware(function (Middleware $middleware): void {
         $middleware->alias([
             'role' => CheckRole::class,
             'admin' => AdminMiddleware::class,
         ]);

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
