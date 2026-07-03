<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run migrations
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | OTP VERIFICATIONS TABLE
        |--------------------------------------------------------------------------
        */
        Schema::create('otp_verifications', function (Blueprint $table) {

            $table->id();

            // Email or phone number
            $table->string('identifier');

            // email | phone
            $table->enum('type', [
                'email',
                'phone',
            ]);

            // 6 digit OTP
            $table->string('otp', 6);

            // Why OTP is used
            $table->enum('purpose', [
                'register',
                'reset_password',
                'phone_verify',
            ]);

            // OTP already used?
            $table->boolean('is_used')
                ->default(false);

            // OTP expiration
            $table->timestamp('expires_at');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index([
                'identifier',
                'type',
                'purpose',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | USERS TABLE UPDATE
        |--------------------------------------------------------------------------
        */
        Schema::table('users', function (Blueprint $table) {

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')
                    ->nullable()
                    ->unique()
                    ->after('email');
            }

            if (! Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')
                    ->nullable()
                    ->after('email_verified_at');
            }

            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')
                    ->default(false)
                    ->after('phone_verified_at');
            }
        });
    }

    /**
     * Reverse migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');

        Schema::table('users', function (Blueprint $table) {

            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('users', 'phone_verified_at')) {
                $table->dropColumn('phone_verified_at');
            }

            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
