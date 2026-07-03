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
        Schema::table('products', function (Blueprint $table) {
            // Pricing fields
            $table->decimal('sale_price', 12, 2)->nullable()->after('base_price');

            // Product classification
            $table->boolean('is_new')->default(false)->after('is_featured');
            $table->integer('sort_order')->default(0)->after('is_new');

            // Status and workflow
            $table->string('status', 20)->default('active')->after('sort_order');
            $table->boolean('is_pre_order')->default(false)->after('status');

            // Marketing
            $table->decimal('discount', 5, 2)->default(0)->after('is_pre_order');
            $table->string('token', 64)->nullable()->unique()->after('discount');
            $table->date('data_from')->nullable()->after('token');

            // Analytics
            $table->unsignedInteger('total_sold')->default(0)->after('data_from');
            $table->decimal('avg_rating', 3, 2)->default(0)->after('total_sold');

            // Additional details
            $table->json('details')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sale_price',
                'is_new',
                'sort_order',
                'status',
                'is_pre_order',
                'discount',
                'token',
                'data_from',
                'total_sold',
                'avg_rating',
                'details',
            ]);
        });
    }
};
