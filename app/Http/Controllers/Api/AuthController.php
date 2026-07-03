<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_CUSTOMER,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $token = $user->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Register successful',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', User::ROLE_CUSTOMER)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (! $user->is_active) {

            return response()->json([
                'success' => false,
                'message' => 'Account inactive',
            ], 403);
        }

        // Delete old tokens
        $user->tokens()->delete();

        $token = $user->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Current User
    |--------------------------------------------------------------------------
    */

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Forgot Password
    |--------------------------------------------------------------------------
    */

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json([
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => __($status),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user, $password) {

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return response()->json([
            'success' => $status === Password::PASSWORD_RESET,
            'message' => __($status),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Social Login
    |--------------------------------------------------------------------------
    */

    public function socialLogin(Request $request, $provider)
    {
        $request->validate([
            'access_token' => 'required',
        ]);

        try {

            $socialUser = Socialite::driver($provider)
                ->stateless()
                ->userFromToken($request->access_token);

            $user = User::where('email', $socialUser->getEmail())->first();

            if (! $user) {

                $user = User::create([
                    'name' => $socialUser->getName() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => User::ROLE_CUSTOMER,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
            }

            // Delete old tokens
            $user->tokens()->delete();

            $token = $user->createToken('flutter-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => ucfirst($provider).' login successful',
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Social login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
