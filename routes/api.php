<?php

use App\Http\Controllers\Api\Auth\ApiLoginController;
use App\Http\Controllers\Api\Auth\ApiProfileController;
use App\Http\Controllers\Api\Auth\ApiRegisterController;
use App\Http\Controllers\Api\BrandApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ContactApiController;
use App\Http\Controllers\Api\HomeApiController;
use App\Http\Controllers\Api\InvoiceApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\PromotionApiController;
use App\Http\Controllers\Api\ReviewApiController;
use App\Http\Controllers\Api\SliderApiController;
use App\Http\Controllers\Api\WishlistApiController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────
//  Public API routes  (v1)
// ─────────────────────────────────────────────

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Home aggregated payload
    Route::get('/home', [HomeApiController::class, 'index'])->name('home');

    // Auth
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [ApiRegisterController::class, 'register'])->name('register');
        Route::post('/login', [ApiLoginController::class, 'login'])->name('login');
        Route::post('/forgot-password', [ApiLoginController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('/reset-password', [ApiLoginController::class, 'resetPassword'])->name('reset-password');
        Route::post('/google', [ApiLoginController::class, 'googleLogin'])->name('google');
    });


    // Catalogue (publicly browsable)
    Route::get('/sliders', [SliderApiController::class, 'index'])->name('sliders.index');
    Route::get('/promotions', [PromotionApiController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/{promotion}', [PromotionApiController::class, 'show'])->name('promotions.show');

    Route::get('/categories', [CategoryApiController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryApiController::class, 'show'])->name('categories.show');

    Route::get('/brands', [BrandApiController::class, 'index'])->name('brands.index');
    Route::get('/brands/{brand}', [BrandApiController::class, 'show'])->name('brands.show');

    Route::get('/products', [ProductApiController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductApiController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/reviews', [ReviewApiController::class, 'index'])->name('products.reviews.index');

    // Product Variants
    Route::get('/product-variant', [ProductVariantController::class, 'show'])->name('product-variant.show');
    Route::get('/product-variants', [ProductVariantController::class, 'index'])->name('product-variants.index');

    // Blogs & Newsletter
    Route::get('/blogs', [HomeApiController::class, 'blogs'])->name('blogs.index');
    Route::get('/blogs/{slug}', [HomeApiController::class, 'blogDetail'])->name('blogs.show');
    Route::post('/newsletter/subscribe', [HomeApiController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');

    // ─────────────────────────────────────────────
    //  Authenticated API routes
    // ─────────────────────────────────────────────

    Route::middleware('auth:sanctum')->group(function () {

        // Auth management
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/logout', [ApiLoginController::class, 'logout'])->name('logout');
            Route::post('/logout-all', [ApiLoginController::class, 'logoutAll'])->name('logout-all');
            Route::post('/refresh', [ApiLoginController::class, 'refresh'])->name('refresh');
        });

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ApiProfileController::class, 'show'])->name('show');
            Route::put('/', [ApiProfileController::class, 'update'])->name('update');
            Route::put('/password', [ApiProfileController::class, 'updatePassword'])->name('password');
            Route::post('/avatar', [ApiProfileController::class, 'uploadAvatar'])->name('avatar');
            Route::delete('/', [ApiProfileController::class, 'destroy'])->name('destroy');
        });

        // Cart
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartApiController::class, 'index'])->name('index');
            Route::post('/add', [CartApiController::class, 'add'])->name('add');
            Route::post('/buy-now', [CartApiController::class, 'buyNow'])->name('buy-now');
            Route::patch('/items/{cartItem}', [CartApiController::class, 'update'])->name('update');
            Route::delete('/items/{cartItem}', [CartApiController::class, 'remove'])->name('remove');
            Route::delete('/clear', [CartApiController::class, 'clear'])->name('clear');
            Route::post('/sync', [CartApiController::class, 'sync'])->name('sync');  // merge guest cart on login
            Route::post('/promotion', [CartApiController::class, 'applyPromotion'])->name('promotion.apply');
            Route::delete('/promotion', [CartApiController::class, 'removePromotion'])->name('promotion.remove');
        });

        // Wishlist
        Route::prefix('wishlist')->name('wishlist.')->group(function () {
            Route::get('/', [WishlistApiController::class, 'index'])->name('index');
            Route::post('/toggle', [WishlistApiController::class, 'toggleBody'])->name('toggle.body');
            Route::post('/{product}', [WishlistApiController::class, 'toggle'])->name('toggle');
            Route::delete('/{product}', [WishlistApiController::class, 'remove'])->name('remove');
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderApiController::class, 'index'])->name('index');
            Route::post('/', [OrderApiController::class, 'store'])->name('store');
            Route::get('/{order}', [OrderApiController::class, 'show'])->name('show');
            Route::patch('/{order}/cancel', [OrderApiController::class, 'cancel'])->name('cancel');
            Route::put('/{order}/status', [OrderApiController::class, 'updateStatus'])->name('status');
            Route::get('/{order}/khqr', [OrderApiController::class, 'showKHQR'])->name('khqr');
            Route::post('/{order}/khqr/verify', [OrderApiController::class, 'verifyKHQR'])->name('khqr.verify');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::post('/initiate', [PaymentApiController::class, 'initiate'])->name('initiate');
            Route::post('/callback', [PaymentApiController::class, 'callback'])->name('callback');
            Route::get('/verify/{payment}', [PaymentApiController::class, 'verify'])->name('verify');
            Route::post('/cambodia', [PaymentApiController::class, 'initiateCambodia'])->name('cambodia.initiate');
            Route::get('/cambodia/status/{reference}', [PaymentApiController::class, 'checkCambodiaStatus'])->name('cambodia.status');
        });

        // Invoices
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceApiController::class, 'index'])->name('index');
            Route::get('/{invoice}', [InvoiceApiController::class, 'show'])->name('show');
            Route::get('/{invoice}/download', [InvoiceApiController::class, 'download'])->name('download');
        });

        // Reviews
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::post('/{product}', [ReviewApiController::class, 'store'])->name('store');
            Route::put('/{review}', [ReviewApiController::class, 'update'])->name('update');
            Route::delete('/{review}', [ReviewApiController::class, 'destroy'])->name('destroy');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationApiController::class, 'index'])->name('index');
            Route::get('/unread-count', [NotificationApiController::class, 'unreadCount'])->name('unread-count');
            Route::patch('/{notification}/read', [NotificationApiController::class, 'markAsRead'])->name('read');
            Route::patch('/read-all', [NotificationApiController::class, 'markAllAsRead'])->name('read-all');
            Route::delete('/{notification}', [NotificationApiController::class, 'destroy'])->name('destroy');
        });

        // Contact / Customer Support Enquiries
        Route::get('/contact', [ContactApiController::class, 'index'])->name('contact.index');
        Route::get('/contact/{contact}', [ContactApiController::class, 'show'])->name('contact.show');
        Route::post('/contact', [ContactApiController::class, 'store'])->name('contact.store');
    });
});
