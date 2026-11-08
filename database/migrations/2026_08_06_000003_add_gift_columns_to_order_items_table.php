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
Schema::table('order_items', function (Blueprint $table) {
    $table->boolean('is_gift')->default(false)->after('quantity');
    $table->foreignId('parent_order_item_id')->nullable()
        ->after('is_gift')
        ->constrained('order_items')->nullOnDelete();
    // parent_order_item_id links a gift line back to the paid product
    // that earned it, so you can display them grouped on the invoice.
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['parent_order_item_id']);
            $table->dropColumn(['is_gift', 'parent_order_item_id']);
        });
    }
};
