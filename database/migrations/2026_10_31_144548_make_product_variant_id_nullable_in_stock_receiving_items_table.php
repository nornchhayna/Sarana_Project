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
        Schema::table('stock_receiving_items', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['product_variant_id']);

            // Make the column nullable
            $table->foreignId('product_variant_id')
                ->nullable()
                ->change();

            // Re-add the foreign key constraint with nullOnDelete
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_receiving_items', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['product_variant_id']);

            // Make the column not nullable
            $table->foreignId('product_variant_id')
                ->nullable(false)
                ->change();

            // Re-add the foreign key constraint with cascadeOnDelete
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->cascadeOnDelete();
        });
    }
};
