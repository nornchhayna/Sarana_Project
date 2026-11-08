<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();
            // Use string instead of enum for SQLite compatibility
            // The enum values will be validated at application level
            $table->string('type')->default('adjustment');
            $table->integer('quantity')->comment('positive = stock increase, negative = decrease');
            $table->string('reference_type')->nullable()->comment('e.g. stock_receiving, order, manual');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index(['reference_type', 'reference_id'], 'sm_reference_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
