<?php

use App\Http\Controllers\Admin\AdminBackupController;
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
use App\Http\Controllers\Admin\AdminRamController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSliderController;
use App\Http\Controllers\Admin\AdminStockController;
use App\Http\Controllers\Admin\AdminStorageController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWebmasterController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\GiftController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\PurchaseOrderItemController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Admin\StockReceivingController;
use App\Http\Controllers\Admin\StockReceivingItemController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.alt');
Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('stats');



Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
    Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
    Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
    Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
    Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('show');
    Route::get('/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
    Route::match(['put', 'patch'], '/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('update');
    Route::delete('/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
    Route::patch('/{purchaseOrder}/submit', [PurchaseOrderController::class, 'submit'])->name('submit');
    Route::patch('/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('cancel');

    // Line items nested under their parent purchase order.
    Route::prefix('/{purchaseOrder}/items')->name('items.')->group(function () {
        Route::post('/', [PurchaseOrderItemController::class, 'store'])->name('store');
        Route::put('/{item}', [PurchaseOrderItemController::class, 'update'])->name('update');
        Route::delete('/{item}', [PurchaseOrderItemController::class, 'destroy'])->name('destroy');
    });

    // Receiving stock against a purchase order.
    Route::prefix('/{purchaseOrder}/receivings')->name('receivings.')->group(function () {
        Route::get('/create', [StockReceivingController::class, 'create'])->name('create');
        Route::post('/', [StockReceivingController::class, 'store'])->name('store');
    });
});
// Product variants for purchase orders (separate route for AJAX)
Route::get('/products/{product}/variants', [PurchaseOrderController::class, 'getProductVariants'])->name('products.variants');
 
Route::prefix('purchase-order-items')->name('purchase-order-items.')->group(function () {
    Route::get('/{purchaseOrderItem}/receiving-history', [StockReceivingItemController::class, 'forPurchaseOrderItem'])->name('receiving-history');
});
 
Route::prefix('stock-receivings')->name('stock-receivings.')->group(function () {
    Route::get('/', [StockReceivingController::class, 'index'])->name('index');
    Route::get('/{stockReceiving}', [StockReceivingController::class, 'show'])->name('show');
});
 
Route::prefix('stock-receiving-items')->name('stock-receiving-items.')->group(function () {
    Route::get('/{stockReceivingItem}', [StockReceivingItemController::class, 'show'])->name('show');
});
 
Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
    Route::get('/', [StockMovementController::class, 'index'])->name('index');
    Route::get('/create', [StockMovementController::class, 'create'])->name('create');
    Route::post('/', [StockMovementController::class, 'store'])->name('store');
    Route::get('/{stockMovement}', [StockMovementController::class, 'show'])->name('show');
});
 
Route::prefix('product-variants')->name('product-variants.')->group(function () {
    Route::get('/{variant}/stock-movements', [StockMovementController::class, 'forVariant'])->name('stock-movements');
});
 
Route::prefix('suppliers')->name('suppliers.')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    Route::get('/create', [SupplierController::class, 'create'])->name('create');
    Route::post('/', [SupplierController::class, 'store'])->name('store');
    Route::patch('/{supplier}/toggle-active', [SupplierController::class, 'toggleActive'])->name('toggleActive');
    Route::patch('/{supplier}/restore', [SupplierController::class, 'restore'])->name('restore');
    Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
    Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
    Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
    Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
});


// ── Users ─────────────────────────────────────
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [AdminUserController::class, 'index'])->name('index');
    Route::get('/create', [AdminUserController::class, 'create'])->name('create');
    Route::post('/', [AdminUserController::class, 'store'])->name('store');
    Route::patch('/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('toggleStatus');
    Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
    Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
    Route::patch('/{user}/ban', [AdminUserController::class, 'ban'])->name('ban');
    Route::patch('/{user}/unban', [AdminUserController::class, 'unban'])->name('unban');
    Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
});

// ── Webmasters ─────────────────────────────────
Route::prefix('webmasters')->name('webmasters.')->group(function () {
    Route::get('/', [AdminWebmasterController::class, 'index'])->name('index');
    Route::get('/create', [AdminWebmasterController::class, 'create'])->name('create');
    Route::post('/', [AdminWebmasterController::class, 'store'])->name('store');
    Route::get('/{webmaster}', [AdminWebmasterController::class, 'show'])->name('show');
    Route::get('/{webmaster}/edit', [AdminWebmasterController::class, 'edit'])->name('edit');
    Route::put('/{webmaster}', [AdminWebmasterController::class, 'update'])->name('update');
    Route::patch('/{webmaster}/toggle-status', [AdminWebmasterController::class, 'toggleStatus'])->name('toggleStatus');
    Route::delete('/{webmaster}', [AdminWebmasterController::class, 'destroy'])->name('destroy');
});

// ── Gifts ──────────────────────────────────────
Route::prefix('gifts')->name('gifts.')->group(function () {
    Route::get('/', [GiftController::class, 'index'])->name('index');
    Route::get('/create', [GiftController::class, 'create'])->name('create');
    Route::post('/', [GiftController::class, 'store'])->name('store');
    Route::get('/{gift}/edit', [GiftController::class, 'edit'])->name('edit');
    Route::put('/{gift}', [GiftController::class, 'update'])->name('update');
    Route::delete('/{gift}', [GiftController::class, 'destroy'])->name('destroy');
});

// ── RAMs ───────────────────────────────────────
Route::prefix('rams')->name('rams.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminRamController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\AdminRamController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\AdminRamController::class, 'store'])->name('store');
    Route::get('/{ram}/edit', [\App\Http\Controllers\Admin\AdminRamController::class, 'edit'])->name('edit');
    Route::put('/{ram}', [\App\Http\Controllers\Admin\AdminRamController::class, 'update'])->name('update');
    Route::delete('/{ram}', [\App\Http\Controllers\Admin\AdminRamController::class, 'destroy'])->name('destroy');
});

// ── Products ───────────────────────────────────
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [AdminProductController::class, 'index'])->name('index');
    Route::get('/create', [AdminProductController::class, 'create'])->name('create');
    Route::post('/', [AdminProductController::class, 'store'])->name('store');
    Route::post('/products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('admin.products.toggle-featured');
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
// ── Blogs ──────────────────────────────────────
Route::prefix('blogs')->name('blogs.')->group(function () {
    Route::get('/', [AdminBlogController::class, 'index'])->name('index');
    Route::get('/create', [AdminBlogController::class, 'create'])->name('create');
    Route::post('/', [AdminBlogController::class, 'store'])->name('store');
    Route::get('/{blog}/edit', [AdminBlogController::class, 'edit'])->name('edit');
    Route::put('/{blog}', [AdminBlogController::class, 'update'])->name('update');
    Route::delete('/{blog}', [AdminBlogController::class, 'destroy'])->name('destroy');
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
    Route::put('/bulk-update', [AdminStockController::class, 'bulkUpdate'])->name('bulk-update');
    Route::get('/', [AdminStockController::class, 'index'])->name('index');
    Route::get('/{stock}/edit', [AdminStockController::class, 'edit'])->name('edit');
    Route::put('/{stock}', [AdminStockController::class, 'update'])->name('update');
    Route::post('/{stock}/adjust', [AdminStockController::class, 'adjust'])->name('adjust');
});
// ── Sliders ─────────────────────────────────────
Route::prefix('sliders')->name('sliders.')->group(function () {
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
    Route::patch('/{promotion}/toggle', [AdminPromotionController::class, 'toggleStatus'])->name('toggle');
    Route::post('/{promotion}/reset-usage', [AdminPromotionController::class, 'resetUsage'])->name('resetUsage');
});

// ── Orders ─────────────────────────────────────
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [AdminOrderController::class, 'index'])->name('index');
    Route::get('/export', [AdminOrderController::class, 'export'])->name('export'); // ADDED EXPORT ROUTE
    Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
    Route::put('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('cancel');
    Route::delete('/{order}', [AdminOrderController::class, 'destroy'])->name('destroy');
});

// ── Payments ───────────────────────────────────
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
    Route::get('/{payment}', [AdminPaymentController::class, 'show'])->name('show');
    Route::match(['POST', 'PUT', 'PATCH'], '/{payment}/approve', [AdminPaymentController::class, 'approve'])->name('approve');
    Route::match(['POST', 'PUT', 'PATCH'], '/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('reject');
    Route::match(['POST', 'PUT', 'PATCH'], '/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('verify');
    Route::match(['POST', 'PUT', 'PATCH'], '/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('refund');
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
    Route::post('/bulk-approve', [AdminReviewController::class, 'bulkApprove'])->name('bulkApprove');
    Route::put('/{review}/approve', [AdminReviewController::class, 'approve'])->name('approve');
    Route::put('/{review}/reject', [AdminReviewController::class, 'reject'])->name('reject');
    Route::delete('/{review}', [AdminReviewController::class, 'destroy'])->name('destroy');
});

// ── Reports ────────────────────────────────────
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/sales-dashboard', [ReportController::class, 'salesDashboard'])->name('sales-dashboard');
    Route::get('/inventory-valuation', [ReportController::class, 'inventoryValuation'])->name('inventory-valuation');
    Route::get('/product-performance', [ReportController::class, 'productPerformance'])->name('product-performance');
    Route::get('/purchase-order-summary', [ReportController::class, 'purchaseOrderSummary'])->name('purchase-order-summary');
    Route::get('/financial-summary', [ReportController::class, 'financialSummary'])->name('financial-summary');
});


Route::prefix('contacts')->name('contacts.')->group(function () {
    Route::get('/', [AdminContactController::class, 'index'])->name('index');
    Route::get('/{contact}', [AdminContactController::class, 'show'])->name('show');
    Route::post('/{contact}/reply', [AdminContactController::class, 'reply'])->name('reply');
    Route::post('/{contact}/mark-read', [AdminContactController::class, 'markRead'])->name('mark-read');
    Route::post('/bulk-mark-read', [AdminContactController::class, 'bulkMarkRead'])->name('bulk-mark-read');
    Route::post('/bulk-destroy', [AdminContactController::class, 'bulkDestroy'])->name('bulk-destroy');
    Route::delete('/{contact}', [AdminContactController::class, 'destroy'])->name('destroy');
});

    // Settings (general / store / payment / shipping)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
 
        Route::get('general', [SettingsController::class, 'general'])->name('general');
        Route::put('general', [SettingsController::class, 'updateGeneral'])->name('general.update');
 
        Route::get('store', [SettingsController::class, 'store'])->name('store');
        Route::put('store', [SettingsController::class, 'updateStore'])->name('store.update');
 
        Route::get('payment', [SettingsController::class, 'payment'])->name('payment');
        Route::put('payment', [SettingsController::class, 'updatePayment'])->name('payment.update');
 
        Route::get('shipping', [SettingsController::class, 'shipping'])->name('shipping');
        Route::put('shipping', [SettingsController::class, 'updateShipping'])->name('shipping.update');
 
        // Notifications tab
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::put('notifications', [NotificationController::class, 'update'])->name('notifications.update');
 
        // Security tab
        Route::get('security', [SecurityController::class, 'index'])->name('security');
        Route::put('security', [SecurityController::class, 'update'])->name('security.update');
        Route::delete('security/sessions', [SecurityController::class, 'logoutOtherSessions'])->name('security.sessions.destroy');
    });
 
 
    // Roles (no 'show' view exists, so it's excluded)
    Route::resource('roles', RoleController::class)->except(['show'])->names('roles');
 
    // Permissions (single-page list; create/update/delete happen inline)
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::put('{permission}', [PermissionController::class, 'update'])->name('update');
        Route::delete('{permission}', [PermissionController::class, 'destroy'])->name('destroy');
    });

    // Database & System Backups
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [AdminBackupController::class, 'index'])->name('index');
        Route::post('/', [AdminBackupController::class, 'store'])->name('store');
        Route::post('/upload', [AdminBackupController::class, 'upload'])->name('upload');
        Route::get('/{filename}/download', [AdminBackupController::class, 'download'])->name('download');
        Route::post('/{filename}/restore', [AdminBackupController::class, 'restore'])->name('restore');
        Route::delete('/{filename}', [AdminBackupController::class, 'destroy'])->name('destroy');
    });
