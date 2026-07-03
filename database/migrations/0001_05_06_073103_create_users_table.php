<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('password');

            // Role
            $table->enum('role', ['admin', 'webmaster', 'customer'])->default('customer');

            // Profile photo — stores relative path e.g. "avatars/abc123.jpg"
            $table->string('avatar')->nullable();

            // Profile
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();

            // Delivery address
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
