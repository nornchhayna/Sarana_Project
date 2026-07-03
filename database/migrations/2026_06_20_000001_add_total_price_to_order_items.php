<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'total_price')) {
                $table->decimal('total_price', 12, 2)->default(0)->after('unit_price');
            }

            // Also fix any other columns in $fillable that may be missing
            if (! Schema::hasColumn('order_items', 'product_sku')) {
                $table->string('product_sku')->nullable()->after('product_name');
            }

            if (! Schema::hasColumn('order_items', 'storage_label')) {
                $table->string('storage_label')->nullable()->after('color_name');
            }
        });

        // Back-fill total_price for any existing rows
        DB::statement('UPDATE order_items SET total_price = unit_price * quantity WHERE total_price = 0');
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            foreach (['total_price', 'product_sku', 'storage_label'] as $col) {
                if (Schema::hasColumn('order_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
