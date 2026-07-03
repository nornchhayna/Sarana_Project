<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to provider (google | facebook).
     */
    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the OAuth callback.
     */
    public function callback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('auth.login')
                ->withErrors(['email' => 'Unable to authenticate with '.ucfirst($provider).'. Please try again.']);
        }

        // Find existing user by provider ID or email
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (! $user) {
            // Check by email (user may already have a local account)
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Link the social provider to the existing account
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                    'avatar' => $socialUser->getAvatar(),
                ]);
            } else {
                // Create a brand-new account (defaults to customer role)
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(40)),
                    'role' => User::ROLE_CUSTOMER,
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);
            }
        } else {
            // Refresh the token
            $user->update([
                'provider_token' => $socialUser->token,
                'avatar' => $socialUser->getAvatar(),
            ]);
        }

        Auth::login($user, true);

        return match ($user->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_WEBMASTER => redirect()->route('webmaster.dashboard'),
            default => redirect()->route('home'),
        };
    }

    private function validateProvider(string $provider): void
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404, 'Unsupported provider.');
    }
}
