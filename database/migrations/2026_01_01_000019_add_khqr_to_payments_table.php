<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add 'khqr' to the method enum
            // NOTE: MySQL requires re-declaring the full enum when adding values
            // Run: ALTER TABLE payments MODIFY COLUMN method ENUM(...)
            // OR handle this via a raw statement:
            DB::statement("
                ALTER TABLE payments
                MODIFY COLUMN method
                ENUM('cash','bank_transfer','aba','wing','acleda','khqr','other')
                DEFAULT 'cash'
            ");

            // KHQR-specific columns
            $table->string('khqr_string')->nullable()->after('proof_image')
                ->comment('Raw QR payload string');
            $table->string('khqr_md5', 32)->nullable()->after('khqr_string')
                ->comment('MD5 hash used to poll NBC API for confirmation');
            $table->timestamp('khqr_expires_at')->nullable()->after('khqr_md5')
                ->comment('QR code expiry; null = no expiry enforced');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['khqr_string', 'khqr_md5', 'khqr_expires_at']);

            DB::statement("
                ALTER TABLE payments
                MODIFY COLUMN method
                ENUM('cash','bank_transfer','aba','wing','acleda','other')
                DEFAULT 'cash'
            ");
        });
    }
};
