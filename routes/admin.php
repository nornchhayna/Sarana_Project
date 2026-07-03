<?php

use App\Http\Controllers\Admin\AdminBrandController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminColorController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminModelController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminPromotionController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSliderController;
use App\Http\Controllers\Admin\AdminStockController;
use App\Http\Controllers\Admin\AdminStorageController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('stats');

// ── Users ─────────────────────────────────────
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [AdminUserController::class, 'index'])->name('index');
    Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');

    // ADD THESE MISSING REPOSITORIES PLUGINS NATIVELY:
    Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');

    Route::patch('/{user}/ban', [AdminUserController::class, 'ban'])->name('ban');
    Route::patch('/{user}/unban', [AdminUserController::class, 'unban'])->name('unban');
    Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
});

// ── Products ───────────────────────────────────
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [AdminProductController::class, 'index'])->name('index');
    Route::get('/create', [AdminProductController::class, 'create'])->name('create');
    Route::post('/', [AdminProductController::class, 'store'])->name('store');
    Route::post('/products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])
        ->name('admin.products.toggle-featured');
    Route::get('/{product}', [AdminProductController::class, 'show'])->name('show');
    Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('edit');
    Route::put('/{product}', [AdminProductController::class, 'update'])->name('update');
    Route::patch('/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('toggle-status');
    Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('destroy');
    Route::delete('/{product}/thumbnail', [AdminProductController::class, 'deleteThumbnail'])->name('delete-thumbnail');
    Route::post('/{product}/images', [AdminProductController::class, 'uploadImages'])->name('images.upload');
    Route::delete('/{product}/images/{image}', [AdminProductController::class, 'deleteImage'])->name('images.delete');
    Route::post('/{product}/remove-image', [AdminProductController::class, 'removeImage'])->name('removeImage');
});
// ── Categories ─────────────────────────────────
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
    Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
    Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
    Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
    Route::put('/{category}', [AdminCategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])->name('destroy');
});

// ── Brands ─────────────────────────────────────
Route::prefix('brands')->name('brands.')->group(function () {
    Route::get('/', [AdminBrandController::class, 'index'])->name('index');
    Route::get('/create', [AdminBrandController::class, 'create'])->name('create');
    Route::post('/', [AdminBrandController::class, 'store'])->name('store');
    Route::get('/{brand}/edit', [AdminBrandController::class, 'edit'])->name('edit');
    Route::put('/{brand}', [AdminBrandController::class, 'update'])->name('update');
    Route::delete('/{brand}', [AdminBrandController::class, 'destroy'])->name('destroy');
    Route::patch('/{brand}/toggle-status', [AdminBrandController::class, 'toggleStatus'])->name('toggle-status');
});

// ── Colors ─────────────────────────────────────
Route::prefix('colors')->name('colors.')->group(function () {
    Route::get('/', [AdminColorController::class, 'index'])->name('index');
    Route::get('/create', [AdminColorController::class, 'create'])->name('create');
    Route::post('/', [AdminColorController::class, 'store'])->name('store');
    Route::get('/{color}/edit', [AdminColorController::class, 'edit'])->name('edit');
    Route::put('/{color}', [AdminColorController::class, 'update'])->name('update');
    Route::delete('/{color}', [AdminColorController::class, 'destroy'])->name('destroy');
});

// ── Phone Models ───────────────────────────────
Route::prefix('models')->name('models.')->group(function () {
    Route::get('/', [AdminModelController::class, 'index'])->name('index');
    Route::get('/create', [AdminModelController::class, 'create'])->name('create');
    Route::post('/', [AdminModelController::class, 'store'])->name('store');
    Route::get('/{phoneModel}/edit', [AdminModelController::class, 'edit'])->name('edit');
    Route::put('/{phoneModel}', [AdminModelController::class, 'update'])->name('update');
    Route::delete('/{phoneModel}', [AdminModelController::class, 'destroy'])->name('destroy');
});

// ── Phone Storage options ──────────────────────
Route::prefix('storages')->name('storages.')->group(function () {
    Route::get('/', [AdminStorageController::class, 'index'])->name('index');
    Route::get('/create', [AdminStorageController::class, 'create'])->name('create');
    Route::post('/', [AdminStorageController::class, 'store'])->name('store');
    Route::get('/{phoneStorage}/edit', [AdminStorageController::class, 'edit'])->name('edit');
    Route::put('/{phoneStorage}', [AdminStorageController::class, 'update'])->name('update');
    Route::delete('/{phoneStorage}', [AdminStorageController::class, 'destroy'])->name('destroy');
});

// ── Stock ──────────────────────────────────────
Route::prefix('stock')->name('stock.')->group(function () {
    // 1. Specific routes go FIRST
    Route::put('/bulk-update', [AdminStockController::class, 'bulkUpdate'])->name('bulk-update');
    // 2. Wildcard routes go LAST
    Route::get('/', [AdminStockController::class, 'index'])->name('index');
    Route::get('/{stock}/edit', [AdminStockController::class, 'edit'])->name('edit');
    Route::put('/{stock}', [AdminStockController::class, 'update'])->name('update');
});

Route::prefix('sliders')
    ->name('sliders.')
    ->group(function () {

        Route::get('/', [AdminSliderController::class, 'index'])->name('index');
        Route::get('/create', [AdminSliderController::class, 'create'])->name('create');
        Route::post('/', [AdminSliderController::class, 'store'])->name('store');
        Route::get('/{slider}/edit', [AdminSliderController::class, 'edit'])->name('edit');
        Route::put('/{slider}', [AdminSliderController::class, 'update'])->name('update');
        Route::delete('/{slider}', [AdminSliderController::class, 'destroy'])->name('destroy');
        Route::patch('/{slider}/toggle-status', [AdminSliderController::class, 'toggleStatus'])->name('toggleStatus');
        Route::post('/reorder', [AdminSliderController::class, 'reorder'])->name('reorder');
    });
// ── Promotions ─────────────────────────────────
Route::prefix('promotions')->name('promotions.')->group(function () {
    Route::get('/', [AdminPromotionController::class, 'index'])->name('index');
    Route::get('/create', [AdminPromotionController::class, 'create'])->name('create');
    Route::post('/', [AdminPromotionController::class, 'store'])->name('store');
    Route::get('/{promotion}/edit', [AdminPromotionController::class, 'edit'])->name('edit');
    Route::put('/{promotion}', [AdminPromotionController::class, 'update'])->name('update');
    Route::delete('/{promotion}', [AdminPromotionController::class, 'destroy'])->name('destroy');
    Route::patch('/{promotion}/toggle', [AdminPromotionController::class, 'toggle'])->name('toggle');
});

// ── Orders ─────────────────────────────────────
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [AdminOrderController::class, 'index'])->name('index');
    Route::get('/export', [AdminOrderController::class, 'export'])->name('export'); // ADDED EXPORT ROUTE
    Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');

    // Update these to match the method names and verbs your controller expects:
    Route::put('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('cancel');

    // Keep your destroy if you use it elsewhere, otherwise your controller uses cancel()
    Route::delete('/{order}', [AdminOrderController::class, 'destroy'])->name('destroy');
});

// ── Payments ───────────────────────────────────
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
    Route::get('/{payment}', [AdminPaymentController::class, 'show'])->name('show');
    Route::patch('/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('verify');
    Route::patch('/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('refund');
});

// ── Invoices ───────────────────────────────────
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [AdminInvoiceController::class, 'index'])->name('index');
    Route::get('/{invoice}', [AdminInvoiceController::class, 'show'])->name('show');
    Route::get('/{invoice}/download', [AdminInvoiceController::class, 'download'])->name('download');
    Route::post('/regenerate/{order}', [AdminInvoiceController::class, 'regenerate'])->name('regenerate');
});

// ── Reviews ────────────────────────────────────
Route::prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/', [AdminReviewController::class, 'index'])->name('index');
    Route::get('/{review}', [AdminReviewController::class, 'show'])->name('show');

    // 1. ADD THIS BULK ROUTE NATIVELY:
    Route::post('/bulk-approve', [AdminReviewController::class, 'bulkApprove'])->name('bulkApprove');

    // 2. SWITCH THESE FROM PATCH TO PUT:
    Route::put('/{review}/approve', [AdminReviewController::class, 'approve'])->name('approve');
    Route::put('/{review}/reject', [AdminReviewController::class, 'reject'])->name('reject');

    Route::delete('/{review}', [AdminReviewController::class, 'destroy'])->name('destroy');
});

// ── Contacts / Messages ────────────────────────
Route::prefix('contacts')->name('contacts.')->group(function () {
    Route::get('/', [AdminContactController::class, 'index'])->name('index');
    Route::get('/{contact}', [AdminContactController::class, 'show'])->name('show');
    Route::patch('/{contact}/resolve', [AdminContactController::class, 'resolve'])->name('resolve');
    Route::delete('/{contact}', [AdminContactController::class, 'destroy'])->name('destroy');
});
