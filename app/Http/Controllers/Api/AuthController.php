<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Customer login.
     * POST /api/login
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['sometimes', 'string'], // optional label for the token
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact support.'],
            ]);
        }

        // Only regular customers may use this endpoint
        if ($user->role !== User::ROLE_CUSTOMER) {
            throw ValidationException::withMessages([
                'email' => ['Please use the appropriate login endpoint for your account type.'],
            ]);
        }

        $token = $user->createToken($request->input('device_name', 'api-token'))->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $this->transformUser($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Admin login.
     * POST /api/admin/login
     */
    public function adminLogin(Request $request): JsonResponse
    {
        Log::info('Admin API login attempt', ['email' => $request->email]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['sometimes', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            Log::info('Admin API auth failed', ['email' => $credentials['email']]);
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact support.'],
            ]);
        }

        if ($user->role !== User::ROLE_ADMIN) {
            Log::info('Admin API role check failed', ['role' => $user->role]);
            throw ValidationException::withMessages([
                'email' => ['Access denied. Admin login is for administrators only.'],
            ]);
        }

        Log::info('Admin API auth successful', ['email' => $user->email, 'role' => $user->role]);

        $token = $user->createToken($request->input('device_name', 'admin-api-token'), ['admin'])->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $this->transformUser($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Webmaster login.
     * POST /api/webmaster/login
     */
    public function webmasterLogin(Request $request): JsonResponse
    {
        Log::info('Webmaster API login attempt', ['email' => $request->email]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['sometimes', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            Log::info('Webmaster API auth failed', ['email' => $credentials['email']]);
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact support.'],
            ]);
        }

        if ($user->role !== User::ROLE_WEBMASTER) {
            Log::info('Webmaster API role check failed', ['role' => $user->role]);
            throw ValidationException::withMessages([
                'email' => ['Access denied. Webmaster login is for webmasters only.'],
            ]);
        }

        Log::info('Webmaster API auth successful', ['email' => $user->email, 'role' => $user->role]);

        $token = $user->createToken($request->input('device_name', 'webmaster-api-token'), ['webmaster'])->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $this->transformUser($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get the currently authenticated user.
     * GET /api/me
     * (requires Authorization: Bearer {token} header)
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->transformUser($request->user()),
        ]);
    }

    /**
     * Revoke the token used for the current request.
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Revoke ALL tokens for the current user (log out of every device).
     * POST /api/logout-all
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out from all devices.',
        ]);
    }

    /**
     * Shape the user payload consistently across all endpoints.
     */
    private function transformUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => (bool) $user->is_active,
            'avatar_url' => $user->avatar_url ?? null,
        ];
    }
}