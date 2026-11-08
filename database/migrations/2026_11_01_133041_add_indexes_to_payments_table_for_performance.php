<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add index on status for faster filtering
            $table->index('status');

            // Add index on khqr_md5 for faster payment lookups
            $table->index('khqr_md5');

            // Add composite index on order_id and status for order-related queries
            $table->index(['order_id', 'status']);

            // Add index on created_at for date range queries
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['khqr_md5']);
            $table->dropIndex(['order_id', 'status']);
            $table->dropIndex(['created_at']);
        });
    }
};
