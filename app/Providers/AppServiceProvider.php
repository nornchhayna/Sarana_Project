<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Wishlist;
use App\Services\OtpService;
use App\Services\SpeedSmsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SpeedSmsService::class);
        $this->app->singleton(OtpService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share with ALL views
        view()->composer('*', function ($view) {
            // Try to get user ID from multiple guards
            $userId = null;
            foreach (['customer', 'web', 'admin', 'webmaster'] as $guard) {
                if (auth()->guard($guard)->check()) {
                    $userId = auth()->guard($guard)->id();
                    break;
                }
            }

            // Cart count - get total quantity from all cart items
            $cartCount = 0;
            if ($userId) {
                $cartCount = Cart::where('user_id', $userId)
                    ->with('items')
                    ->first()?->items->sum('quantity') ?? 0;
            }

            // Also check session-based cart for guests
            if (! $userId && session()->has('cart_session_id')) {
                $cartCount = Cart::where('session_id', session()->get('cart_session_id'))
                    ->with('items')
                    ->first()?->items->sum('quantity') ?? 0;
            }

            // Wishlist count
            $wishlistCount = 0;
            if ($userId) {
                $wishlistCount = Wishlist::where('user_id', $userId)->count() ?? 0;
            }

            $view->with('cartCount', $cartCount)
                ->with('wishlistCount', $wishlistCount);
        });
    }
}
