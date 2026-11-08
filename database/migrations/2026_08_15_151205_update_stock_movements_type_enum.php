<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support ENUM changes, so we only do this for MySQL
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('stock_movements')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->enum('type', [
                    'purchase_receipt',
                    'sale',
                    'adjustment_in',
                    'adjustment_out',
                    'sale_return',
                    'damage'
                ])->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SQLite doesn't support ENUM changes, so we only do this for MySQL
        if (DB::getDriverName() === 'mysql') {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->enum('type', ['purchase_in', 'sale_out', 'adjustment', 'return_in', 'return_out'])->change();
            });
        }
    }
};
