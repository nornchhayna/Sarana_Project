<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\InvoiceController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ReviewController;
use App\Http\Controllers\Web\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Products (public browsing — no auth needed)
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');

    // CHANGE THIS LINE: Remove "product:"
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});
// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

/*
|--------------------------------------------------------------------------
| Admin & Webmaster Login — outside guest middleware so always accessible
| even if a customer session is active
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login.post');

Route::get('/webmaster/login', [LoginController::class, 'showWebmasterLoginForm'])->name('webmaster.login');
Route::post('/webmaster/login', [LoginController::class, 'webmasterLogin'])->name('webmaster.login.post');

/*
|--------------------------------------------------------------------------
| Smart /dashboard redirect
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user?->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'webmaster' => redirect()->route('admin.dashboard'),
        default => redirect()->route('home'),
    };
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Guest-only Routes (customer)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // Registration
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register/email', [AuthController::class, 'registerWithEmail'])->name('auth.register.email');
    Route::post('/register/phone', [AuthController::class, 'registerWithPhone'])->name('auth.register.phone');

    // Email / phone verification
    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('auth.verify-email.show');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('auth.verify-email');
    Route::get('/verify-phone', [AuthController::class, 'showVerifyPhone'])->name('auth.verify-phone.show');
    Route::post('/verify-phone', [AuthController::class, 'verifyPhone'])->name('auth.verify-phone');

    // Customer login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');

    // Social Auth
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

    // Password Reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('auth.password.forgot');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetOtp'])->name('auth.password.send-otp');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (all roles)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow');
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        // routes/web.php
        Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow');
        // Find the update line and change it to put

        Route::put('/items/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/items/{cartItem}', [CartController::class, 'remove'])->name('remove');
        Route::post('/promotion', [CartController::class, 'applyPromotion'])
            ->name('apply-promotion');
        Route::delete('/promotion', [CartController::class, 'removePromotion'])->name('promotion.remove');
    });
    // Route::get('/cart', [CartController::class, 'index']);
    // Route::get('/checkout', [CheckoutController::class, 'index']);
    // Route::get('/orders', [OrderController::class, 'index']);
    // Route::get('/orders/{orderId}', [OrderController::class, 'show'])->name('orders.show');
    // Route::get('/orders/{orderId}/payment-status', [OrderController::class, 'paymentStatus'])->name('orders.payment-status');
    // Route::post('/orders/{orderId}/payment-proof', [OrderController::class, 'uploadPaymentProof'])->name('orders.payment-proof');
    // Route::get('/invoice/{orderId}', [OrderController::class, 'viewInvoice']);
    // Route::get('/invoice/{orderId}/generate', [OrderController::class, 'generateInvoice']);
    // ── Checkout ──────────────────────────────────────────────────────────

    Route::middleware('auth')->group(function () {

        // Checkout
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

        // Payment QR page
        Route::get('/checkout/{order}/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
        Route::get('/checkout/{order}/payment/status', [CheckoutController::class, 'paymentStatus'])->name('checkout.payment.status');
        Route::post('/checkout/{order}/payment/cancel', [CheckoutController::class, 'cancelPayment'])->name('checkout.payment.cancel');

    });

    // Bakong webhook — no auth, but outside the group
    Route::post('/webhooks/bakong', [CheckoutController::class, 'bakongWebhook'])->name('checkout.bakong.webhook');
    // ── Orders ────────────────────────────────────────────────────────────
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');

        // KHQR payment page
        Route::get('/{order}/pay/khqr', [OrderController::class, 'showKHQR'])->name('khqr.pay');

        // FIX THIS LINE: Clean up the path and name
        Route::get('/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('invoice.download');

        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::patch('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });
    Route::post('/webhooks/bakong', [CheckoutController::class, 'bakongWebhook'])
        ->name('checkout.bakong.webhook');

    // ── Invoices ──────────────────────────────────────────────────────────
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])->name('download');
    });

    // ── Wishlist ──────────────────────────────────────────────────────────
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::get('/store', [WishlistController::class, 'store'])->name('store');
        Route::post('/toggle/{product}', [WishlistController::class, 'toggle'])->name('toggle');
        Route::delete('/{product}', [WishlistController::class, 'destroy'])->name('destroy');
    });

    // ── Reviews ───────────────────────────────────────────────────────────
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/{product}', [ReviewController::class, 'store'])->name('store');
        Route::get('/{review}/edit', [ReviewController::class, 'edit'])->name('edit');
        Route::put('/{review}', [ReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });

    // ── Profile ───────────────────────────────────────────────────────────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');       // profile.show
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');   // profile.edit
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('password');
        Route::delete('/avatar', [ProfileController::class, 'removeAvatar'])->name('avatar.remove');
    });

    // ── Categories ─────────────────────────────────────────────────────
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{parentSlug}/{childSlug}', [CategoryController::class, 'showSubcategory'])->name('showSubcategory');
    });
    // ── Notifications ─────────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');  // before {id}
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::delete('/', [NotificationController::class, 'destroyAll'])->name('destroy-all');         // before {id}
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });
});
