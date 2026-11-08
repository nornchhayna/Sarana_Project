<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_receiving_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_receiving_id')
                ->constrained('stock_receivings')
                ->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')
                ->nullable()
                ->constrained('purchase_order_items')
                ->nullOnDelete();
            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();
            $table->unsignedInteger('quantity_received');
            $table->decimal('unit_cost', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_receiving_items');
    }
};
