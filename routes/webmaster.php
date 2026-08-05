<?php

use App\Http\Controllers\Webmaster\DashboardController;
use App\Http\Controllers\Webmaster\WebmasterBrandController;
use App\Http\Controllers\Webmaster\WebmasterCategoryController;
use App\Http\Controllers\Webmaster\WebmasterColorController;
use App\Http\Controllers\Webmaster\WebmasterContactController;
use App\Http\Controllers\Webmaster\WebmasterInvoiceController;
use App\Http\Controllers\Webmaster\WebmasterModelController;
use App\Http\Controllers\Webmaster\WebmasterOrderController;
use App\Http\Controllers\Webmaster\WebmasterPaymentController;
use App\Http\Controllers\Webmaster\WebmasterPromotionController;
use App\Http\Controllers\Webmaster\WebmasterProductController;
use App\Http\Controllers\Webmaster\WebmasterReviewController;
use App\Http\Controllers\Webmaster\WebmasterSliderController;
use App\Http\Controllers\Webmaster\WebmasterStockController;
use App\Http\Controllers\Webmaster\WebmasterStorageController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ── Products ───────────────────────────────────
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [WebmasterProductController::class, 'index'])->name('index');
    Route::get('/create', [WebmasterProductController::class, 'create'])->name('create');
    Route::post('/', [WebmasterProductController::class, 'store'])->name('store');
    Route::get('/{product}', [WebmasterProductController::class, 'show'])->name('show');
    Route::get('/{product}/edit', [WebmasterProductController::class, 'edit'])->name('edit');
    Route::put('/{product}', [WebmasterProductController::class, 'update'])->name('update');
    Route::patch('/{product}/toggle-status', [WebmasterProductController::class, 'toggleStatus'])->name('toggle-status');
    Route::patch('/{product}/toggle-featured', [WebmasterProductController::class, 'toggleFeatured'])->name('toggle-featured');
    Route::delete('/{product}', [WebmasterProductController::class, 'destroy'])->name('destroy');
    Route::delete('/{product}/thumbnail', [WebmasterProductController::class, 'deleteThumbnail'])->name('delete-thumbnail');
    Route::post('/{product}/images', [WebmasterProductController::class, 'uploadImages'])->name('images.upload');
    Route::delete('/{product}/images/{image}', [WebmasterProductController::class, 'deleteImage'])->name('images.delete');
});

// ── Categories ─────────────────────────────────
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [WebmasterCategoryController::class, 'index'])->name('index');
    Route::get('/create', [WebmasterCategoryController::class, 'create'])->name('create');
    Route::post('/', [WebmasterCategoryController::class, 'store'])->name('store');
    Route::get('/{category}/edit', [WebmasterCategoryController::class, 'edit'])->name('edit');
    Route::put('/{category}', [WebmasterCategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [WebmasterCategoryController::class, 'destroy'])->name('destroy');
});

// ── Brands ─────────────────────────────────────
Route::prefix('brands')->name('brands.')->group(function () {
    Route::get('/', [WebmasterBrandController::class, 'index'])->name('index');
    Route::get('/create', [WebmasterBrandController::class, 'create'])->name('create');
    Route::post('/', [WebmasterBrandController::class, 'store'])->name('store');
    Route::get('/{brand}/edit', [WebmasterBrandController::class, 'edit'])->name('edit');
    Route::put('/{brand}', [WebmasterBrandController::class, 'update'])->name('update');
    Route::delete('/{brand}', [WebmasterBrandController::class, 'destroy'])->name('destroy');
    Route::patch('/{brand}/toggle-status', [WebmasterBrandController::class, 'toggleStatus'])->name('toggle-status');
});

// ── Colors ─────────────────────────────────────
Route::prefix('colors')->name('colors.')->group(function () {
    Route::get('/', [WebmasterColorController::class, 'index'])->name('index');
    Route::get('/create', [WebmasterColorController::class, 'create'])->name('create');
    Route::post('/', [WebmasterColorController::class, 'store'])->name('store');
    Route::get('/{color}/edit', [WebmasterColorController::class, 'edit'])->name('edit');
    Route::put('/{color}', [WebmasterColorController::class, 'update'])->name('update');
    Route::delete('/{color}', [WebmasterColorController::class, 'destroy'])->name('destroy');
});

// ── Phone Models ───────────────────────────────
Route::prefix('models')->name('models.')->group(function () {
    Route::get('/', [WebmasterModelController::class, 'index'])->name('index');
    Route::get('/create', [WebmasterModelController::class, 'create'])->name('create');
    Route::post('/', [WebmasterModelController::class, 'store'])->name('store');
    Route::get('/{phoneModel}/edit', [WebmasterModelController::class, 'edit'])->name('edit');
    Route::put('/{phoneModel}', [WebmasterModelController::class, 'update'])->name('update');
    Route::delete('/{phoneModel}', [WebmasterModelController::class, 'destroy'])->name('destroy');
});

// ── Phone Storage options ──────────────────────
Route::prefix('storages')->name('storages.')->group(function () {
    Route::get('/', [WebmasterStorageController::class, 'index'])->name('index');
    Route::get('/create', [WebmasterStorageController::class, 'create'])->name('create');
    Route::post('/', [WebmasterStorageController::class, 'store'])->name('store');
    Route::get('/{phoneStorage}/edit', [WebmasterStorageController::class, 'edit'])->name('edit');
    Route::put('/{phoneStorage}', [WebmasterStorageController::class, 'update'])->name('update');
    Route::delete('/{phoneStorage}', [WebmasterStorageController::class, 'destroy'])->name('destroy');
});

// ── Stock ──────────────────────────────────────
Route::prefix('stock')->name('stock.')->group(function () {
    Route::put('/bulk-update', [WebmasterStockController::class, 'bulkUpdate'])->name('bulk-update');
    Route::get('/', [WebmasterStockController::class, 'index'])->name('index');
    Route::get('/{stock}/edit', [WebmasterStockController::class, 'edit'])->name('edit');
    Route::put('/{stock}', [WebmasterStockController::class, 'update'])->name('update');
    Route::post('/{stock}/adjust', [WebmasterStockController::class, 'adjust'])->name('adjust');
});

// ── Sliders ─────────────────────────────────────
Route::prefix('sliders')->name('sliders.')->group(function () {
    Route::get('/', [WebmasterSliderController::class, 'index'])->name('index');
    Route::get('/create', [WebmasterSliderController::class, 'create'])->name('create');
    Route::post('/', [WebmasterSliderController::class, 'store'])->name('store');
    Route::get('/{slider}/edit', [WebmasterSliderController::class, 'edit'])->name('edit');
    Route::put('/{slider}', [WebmasterSliderController::class, 'update'])->name('update');
    Route::delete('/{slider}', [WebmasterSliderController::class, 'destroy'])->name('destroy');
    Route::patch('/{slider}/toggle-status', [WebmasterSliderController::class, 'toggleStatus'])->name('toggleStatus');
    Route::post('/reorder', [WebmasterSliderController::class, 'reorder'])->name('reorder');
});

// ── Promotions ─────────────────────────────────
Route::prefix('promotions')->name('promotions.')->group(function () {
    Route::get('/', [WebmasterPromotionController::class, 'index'])->name('index');
    Route::get('/create', [WebmasterPromotionController::class, 'create'])->name('create');
    Route::post('/', [WebmasterPromotionController::class, 'store'])->name('store');
    Route::get('/{promotion}/edit', [WebmasterPromotionController::class, 'edit'])->name('edit');
    Route::put('/{promotion}', [WebmasterPromotionController::class, 'update'])->name('update');
    Route::delete('/{promotion}', [WebmasterPromotionController::class, 'destroy'])->name('destroy');
    Route::patch('/{promotion}/toggle', [WebmasterPromotionController::class, 'toggle'])->name('toggle');
});

// ── Orders ─────────────────────────────────────
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [WebmasterOrderController::class, 'index'])->name('index');
    Route::get('/export', [WebmasterOrderController::class, 'export'])->name('export');
    Route::get('/{order}', [WebmasterOrderController::class, 'show'])->name('show');
    Route::put('/{order}/status', [WebmasterOrderController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/{order}/cancel', [WebmasterOrderController::class, 'cancel'])->name('cancel');
});

// ── Payments ───────────────────────────────────
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [WebmasterPaymentController::class, 'index'])->name('index');
    Route::get('/{payment}', [WebmasterPaymentController::class, 'show'])->name('show');
    Route::patch('/{payment}/verify', [WebmasterPaymentController::class, 'verify'])->name('verify');
});

// ── Invoices ───────────────────────────────────
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [WebmasterInvoiceController::class, 'index'])->name('index');
    Route::get('/{invoice}', [WebmasterInvoiceController::class, 'show'])->name('show');
    Route::get('/{invoice}/download', [WebmasterInvoiceController::class, 'download'])->name('download');
});

// ── Reviews ────────────────────────────────────
Route::prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/', [WebmasterReviewController::class, 'index'])->name('index');
    Route::get('/{review}', [WebmasterReviewController::class, 'show'])->name('show');
    Route::post('/bulk-approve', [WebmasterReviewController::class, 'bulkApprove'])->name('bulkApprove');
    Route::put('/{review}/approve', [WebmasterReviewController::class, 'approve'])->name('approve');
    Route::put('/{review}/reject', [WebmasterReviewController::class, 'reject'])->name('reject');
    Route::delete('/{review}', [WebmasterReviewController::class, 'destroy'])->name('destroy');
});

// ── Contacts ────────────────────────────────────
Route::prefix('contacts')->name('contacts.')->group(function () {
    Route::get('/', [WebmasterContactController::class, 'index'])->name('index');
    Route::get('/{contact}', [WebmasterContactController::class, 'show'])->name('show');
    Route::post('/{contact}/reply', [WebmasterContactController::class, 'reply'])->name('reply');
    Route::post('/{contact}/mark-read', [WebmasterContactController::class, 'markRead'])->name('mark-read');
});