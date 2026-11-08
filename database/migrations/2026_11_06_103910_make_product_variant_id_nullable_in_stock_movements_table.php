<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('product_variant_id')->nullable()->change();
            if (!Schema::hasColumn('stock_movements', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('id')->constrained('products')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            if (Schema::hasColumn('stock_movements', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
            $table->unsignedBigInteger('product_variant_id')->nullable(false)->change();
        });
    }
};
