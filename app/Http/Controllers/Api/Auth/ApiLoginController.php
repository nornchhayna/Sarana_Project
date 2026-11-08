<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiLoginController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('mobile-token')->plainTextToken;
        $userData = $this->formatUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $userData,
            'data' => [
                'token' => $token,
                'user' => $userData,
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        return app(ApiRegisterController::class)->register($request);
    }

    public function googleLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'nullable|string',
            'id_token' => 'nullable|string',
            'google_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $fullName = trim((string) ($request->name ?: explode('@', $request->email)[0]));
        $parts = explode(' ', $fullName, 2);
        $firstName = $parts[0] ?? $fullName;
        $lastName = $parts[1] ?? '';
        $providerId = $request->input('google_id') ?? $request->input('id_token');
        if ($providerId && strlen($providerId) > 191) {
            if (str_contains($providerId, '.')) {
                $tokenParts = explode('.', $providerId);
                if (isset($tokenParts[1])) {
                    $payload = json_decode(base64_decode(strtr($tokenParts[1], '-_', '+/')), true);
                    $providerId = $payload['sub'] ?? substr($providerId, 0, 191);
                } else {
                    $providerId = substr($providerId, 0, 191);
                }
            } else {
                $providerId = substr($providerId, 0, 191);
            }
        }

        if (! $user) {
            $user = User::create([
                'name' => $fullName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->email,
                'password' => Hash::make(Str::random(40)),
                'role' => User::ROLE_CUSTOMER,
                'status' => 'active',
                'is_active' => true,
                'provider' => 'google',
                'provider_id' => $providerId,
                'avatar' => $request->avatar,
                'email_verified_at' => now(),
            ]);
        } else {
            $user->update([
                'provider' => 'google',
                'provider_id' => $providerId ?? $user->provider_id,
                'avatar' => $request->avatar ?? $user->avatar,
            ]);
        }

        $token = $user->createToken('mobile-token')->plainTextToken;
        $userData = $this->formatUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Google login successful',
            'token' => $token,
            'user' => $userData,
            'data' => [
                'token' => $token,
                'user' => $userData,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()?->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices',
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->currentAccessToken()?->delete();
        $token = $user->createToken('mobile-token')->plainTextToken;
        $userData = $this->formatUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed',
            'token' => $token,
            'user' => $userData,
            'data' => [
                'token' => $token,
                'user' => $userData,
            ],
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?: 'Valid email is required',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->input('email')));
        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with that email address.',
            ], 404);
        }

        $otp = (string) random_int(100000, 999999);

        try {
            \App\Models\OtpVerification::create([
                'identifier' => $email,
                'type' => 'email',
                'purpose' => 'password_reset',
                'otp' => $otp,
                'expires_at' => now()->addMinutes(15),
                'is_used' => false,
            ]);
        } catch (\Throwable $e) {
            // Fallback to cache if otp_verifications table is not migrated
        }

        \Illuminate\Support\Facades\Cache::put('password_reset_otp_' . $email, $otp, now()->addMinutes(15));

        return response()->json([
            'success' => true,
            'message' => 'A 6-digit verification code has been sent to your email.',
            'otp_code' => $otp,
            'data' => [
                'email' => $email,
                'otp_code' => $otp,
            ],
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?: 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->input('email')));
        $otp = trim($request->input('otp'));
        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.',
            ], 404);
        }

        $valid = false;
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('password_reset_otp_' . $email);
        if ($cachedOtp && (string) $cachedOtp === $otp) {
            $valid = true;
            \Illuminate\Support\Facades\Cache::forget('password_reset_otp_' . $email);
        }

        try {
            $record = \App\Models\OtpVerification::where('identifier', $email)
                ->where('otp', $otp)
                ->where('is_used', false)
                ->latest()
                ->first();
            if ($record && $record->isValid($otp)) {
                $valid = true;
                $record->markUsed();
            }
        } catch (\Throwable $e) {
            // Ignore table lookup errors if cache matched
        }

        if (! $valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successful! You can now sign in with your new password.',
        ]);
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'city' => $user->city,
            'country' => $user->country ?? null,
            'postal_code' => $user->postal_code ?? null,
            'avatar' => $user->avatar,
            'avatar_url' => $user->avatar_url,
            'role' => $user->role ?? User::ROLE_CUSTOMER,
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }
}
