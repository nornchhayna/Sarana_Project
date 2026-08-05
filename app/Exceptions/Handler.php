<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            // Return JSON for all API routes
            if ($request->is('api/*') || $request->expectsJson()) {
                return $this->handleApiException($e, $request);
            }
        });
    }
    private function handleApiException(Throwable $e, $request)
    {
        if ($e instanceof AuthenticationException) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return response()->json(['success' => false, 'message' => 'Resource not found.'], 404);
        }

        return response()->json([
            'success' => false,
            'message' => app()->isProduction() ? 'Server error.' : $e->getMessage(),
        ], 500);
    }
}
