<?php

namespace App\Providers;

use App\Models\Cart;
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
            $count = Cart::where('user_id', auth()->id())
                ->orWhere('session_id', session()->get('cart_session_id'))
                ->with('items')
                ->first()?->items->sum('quantity') ?? 0;

            $view->with('cartCount', $count);
        });
    }
}
