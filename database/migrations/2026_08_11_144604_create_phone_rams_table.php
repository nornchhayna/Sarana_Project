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
        Schema::create('phone_rams', function (Blueprint $table) {
            $table->id();
            $table->string('capacity'); // e.g., '6GB', '8GB', '12GB'
            $table->string('label')->nullable(); // e.g., '6GB RAM', '12GB RAM'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_rams');
    }
};
