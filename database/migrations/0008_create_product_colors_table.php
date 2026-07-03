<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('color_id')->constrained()->cascadeOnDelete();
            $table->decimal('price_adjustment', 10, 2)->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'color_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_colors');
    }
};
