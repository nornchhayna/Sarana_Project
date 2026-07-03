<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fixes: SQLSTATE[42S22]: Column not found: 1054 Unknown column
     * 'confirmed_at' in 'field list'.
     *
     * OrderFulfillmentService::markAsPaid() writes to orders.confirmed_at
     * when a payment is confirmed, but this column was never added to the
     * orders table. The resulting query exception was being silently
     * swallowed by CheckoutController::paymentStatus()'s catch block, so
     * real, successful Bakong payments were never reflected in the order's
     * status — the page just kept polling forever as if unpaid.
     *
     * Also adding cancelled_at for the same reason: OrderFulfillmentService
     * ::cancelPending() writes to it too, and if confirmed_at was missing
     * it's worth checking whether this one is too, rather than hitting the
     * same bug again later on the cancel path.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('confirmed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'confirmed_at')) {
                $table->dropColumn('confirmed_at');
            }
            if (Schema::hasColumn('orders', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
        });
    }
};
