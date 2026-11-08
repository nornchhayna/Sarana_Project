<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CustomerNotificationController;
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
use App\Http\Controllers\Webmaster\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationController;


Route::get('/forgot-password/verify-otp', [
    PasswordResetController::class,
    'showVerifyOtpForm',
])->name('auth.password.verify-otp');

Route::post('/forgot-password/verify-otp', [PasswordResetController::class, 'verifyOtp'])
    ->name('auth.password.verify-otp.submit');
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'km'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Blog routes
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

// Admin login routes (no authentication required)
Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login.post');

// Webmaster login routes (no authentication required)
Route::get('/webmaster/login', [LoginController::class, 'showWebmasterLoginForm'])->name('webmaster.login');
Route::post('/webmaster/login', [LoginController::class, 'webmasterLogin'])->name('webmaster.login.post');

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders');
});
Route::get('/dashboard', function () {

    $user = auth()->user();

    return match ($user?->role) {

        'admin' => redirect()->route('admin.dashboard'),
        'webmaster' => redirect()->route('webmaster.dashboard'),
        default => redirect()->route('home'),

    };

})->middleware('auth')->name('dashboard');

Route::middleware('guest')->group(function () {

    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register/email', [AuthController::class, 'registerWithEmail'])->name('auth.register.email');
    Route::post('/register/phone', [AuthController::class, 'registerWithPhone'])->name('auth.register.phone');

    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('auth.verify-email.show');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('auth.verify-email');
    Route::post('/verify-email/resend', [EmailVerificationController::class, 'resend'])->name('auth.verify-email.resend');
    Route::get('/verify-phone', [AuthController::class, 'showVerifyPhone'])->name('auth.verify-phone.show');
    Route::post('/verify-phone', [AuthController::class, 'verifyPhone'])->name('auth.verify-phone');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');

    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('auth.password.forgot');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetOtp'])->name('auth.password.send-otp');
});
 Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Public product browsing
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});

// Public category browsing
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
    Route::get('/{parentSlug}/{childSlug}', [CategoryController::class, 'showSubcategory'])->name('showSubcategory');
});

// Public static & policy pages
Route::get('/about', function () {
    return view('web.about');
})->name('about');
Route::view('/shipping-policy', 'web.shipping-policy')->name('shipping-policy');
Route::view('/warranty', 'web.warranty-policy')->name('warranty-policy');
Route::view('/privacy', 'web.privacy-policy')->name('privacy-policy');
Route::view('/terms', 'web.terms-and-conditions')->name('terms-and-conditions');
Route::view('/faq', 'web.faq')->name('faq');

Route::middleware('auth')->group(function () {

   
    Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow');
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow');
        Route::put('/items/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/items/{cartItem}', [CartController::class, 'remove'])->name('remove');
       Route::post('/promotion', [CartController::class, 'applyPromotion'])
        ->name('apply-promotion');

    Route::delete('/promotion', [CartController::class, 'removePromotion'])
        ->name('remove-promotion');
    });

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::get('/checkout/{order}/confirmation', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
    Route::get('/checkout/{order}/payment/status', [CheckoutController::class, 'paymentStatus'])->name('checkout.payment.status');
    Route::post('/checkout/{order}/payment/cancel', [CheckoutController::class, 'cancelPayment'])->name('checkout.payment.cancel');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}/pay/khqr', [OrderController::class, 'showKHQR'])->name('khqr.pay');
        Route::get('/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('invoice.download');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::patch('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });

    Route::post('/webhooks/bakong', [CheckoutController::class, 'bakongWebhook'])->name('checkout.bakong.webhook');
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])->name('download');
    });

    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('/store', [WishlistController::class, 'store'])->name('store');
        Route::post('/toggle/{product}', [WishlistController::class, 'toggle'])->name('toggle');
        Route::delete('/{wishlist}', [WishlistController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/create/{product}', [ReviewController::class, 'create'])->name('create');
        Route::post('/{product}', [ReviewController::class, 'store'])->name('store');
        Route::get('/{review}/edit', [ReviewController::class, 'edit'])->name('edit');
        Route::put('/{review}', [ReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('password');
        Route::delete('/avatar', [ProfileController::class, 'removeAvatar'])->name('avatar.remove');
    });
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/index', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::delete('/', [NotificationController::class, 'destroyAll'])->name('destroy-all');
        // Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    Route::get('/notifications/unseen', [CustomerNotificationController::class, 'index'])
        ->name('web.notifications.unseen');

    Route::get('/notifications/index', [CustomerNotificationController::class, 'all'])
        ->name('web.notifications.index');

    Route::get('/my-messages/{contact}', [CustomerNotificationController::class, 'show'])
        ->name('web.contact.show');

    Route::get('/whoami', function () {
        return response()->json([
            'id' => auth()->id(),
            'email' => auth()->user()?->email,
        ]);
    });

    Route::get('/contact', [ContactController::class, 'index'])
        ->name('contact.index');

    Route::post('/contact', [ContactController::class, 'store'])
        ->name('contact.store');

});
Route::get('/debug-auth', function () {
    return [
        'admin' => \Illuminate\Support\Facades\Auth::guard('admin')->check(),
        'webmaster_check' => \Illuminate\Support\Facades\Auth::guard('webmaster')->check(),
        'webmaster_role' => \Illuminate\Support\Facades\Auth::guard('webmaster')->user()?->role,
        'customer' => \Illuminate\Support\Facades\Auth::guard('customer')->check(),
        'web_check' => \Illuminate\Support\Facades\Auth::guard('web')->check(),
        'web_role' => \Illuminate\Support\Facades\Auth::guard('web')->user()?->role,
    ];
});
