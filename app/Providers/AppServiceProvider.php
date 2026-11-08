<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Setting;
use App\Models\Wishlist;
use App\Services\OtpService;
use App\Services\SpeedSmsService;
use Illuminate\Pagination\Paginator;
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
        // Use Bootstrap 4 pagination for AdminLTE in admin and webmaster panels
        if (request()->is('admin*') || request()->is('webmaster*')) {
            Paginator::useBootstrapFour();
        }

        // Apply real settings across configs and views
        try {
            $allSettings = Setting::all()->pluck('value', 'key')->toArray();

            $siteName = $allSettings['site_name'] ?? config('app.name', 'Ashahi Store');
            $siteLogo = $allSettings['site_logo'] ?? null;
            $storeEmail = $allSettings['store_email'] ?? $allSettings['contact_email'] ?? 'contact@ashahi.com';
            $storePhone = $allSettings['store_phone'] ?? $allSettings['contact_phone'] ?? '+855 96 717 7746';
            $storeAddress = $allSettings['store_address'] ?? 'Phnom Penh, Cambodia';
            $currency = $allSettings['currency'] ?? 'USD';
            $flatRate = (float) ($allSettings['flat_shipping_rate'] ?? 0);
            $freeThreshold = (float) ($allSettings['free_shipping_threshold'] ?? 0);

            $siteSettings = array_merge($allSettings, [
                'site_name' => $siteName,
                'site_logo' => $siteLogo,
                'store_email' => $storeEmail,
                'store_phone' => $storePhone,
                'store_address' => $storeAddress,
                'currency' => $currency,
                'flat_shipping_rate' => $flatRate,
                'free_shipping_threshold' => $freeThreshold,
            ]);

            config([
                'app.name' => $siteName,
                'mail.from.name' => $siteName,
                'mail.from.address' => $storeEmail,
            ]);

            if (! empty($allSettings['timezone'])) {
                date_default_timezone_set($allSettings['timezone']);
            }

            if (! empty($allSettings['session_lifetime'])) {
                config(['session.lifetime' => (int) $allSettings['session_lifetime']]);
            }

            if (! empty($allSettings['bakong_account_id'])) {
                config(['bakong.account_id' => $allSettings['bakong_account_id']]);
            }
            if (! empty($allSettings['bakong_merchant_name'])) {
                config(['bakong.merchant_name' => $allSettings['bakong_merchant_name']]);
            }
            if (! empty($allSettings['bakong_merchant_city'])) {
                config(['bakong.merchant_city' => $allSettings['bakong_merchant_city']]);
            }
            if (! empty($allSettings['bakong_api_token'])) {
                config(['bakong.api_token' => $allSettings['bakong_api_token']]);
            }

            view()->share('siteSettings', $siteSettings);
        } catch (\Throwable $e) {
            view()->share('siteSettings', [
                'site_name' => 'Ashahi Store',
                'store_email' => 'contact@ashahi.com',
                'store_phone' => '+855 96 717 7746',
                'store_address' => 'Phnom Penh, Cambodia',
                'currency' => 'USD',
                'flat_shipping_rate' => 0,
                'free_shipping_threshold' => 0,
            ]);
        }

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

        // Share counters with Webmaster layout & views
        view()->composer(['layouts.webmaster', 'webmaster.*'], function ($view) {
            try {
                $unreadContacts = \App\Models\Contact::where('status', 'unread')->count();
                $pendingReviewsCount = \App\Models\Review::where('is_approved', false)->count();
                $variantLowStock = \App\Models\ProductVariant::where('stock', '<=', 10)->where('stock', '>', 0)->count();
                $singleLowStock = \App\Models\Stock::whereNotIn('product_id', \App\Models\ProductVariant::select('product_id'))
                    ->where('quantity', '<=', 10)->where('quantity', '>', 0)->count();
                $lowStockBadge = $variantLowStock + $singleLowStock;
                $pendingOrdersBadge = \App\Models\Order::where('status', 'pending')->count();

                $view->with('unreadContacts', $unreadContacts)
                    ->with('pendingReviewsCount', $pendingReviewsCount)
                    ->with('lowStockBadge', $lowStockBadge)
                    ->with('pendingOrdersBadge', $pendingOrdersBadge);
            } catch (\Throwable $e) {
                // Ignore during initial setups or migrations
            }
        });
    }
}
