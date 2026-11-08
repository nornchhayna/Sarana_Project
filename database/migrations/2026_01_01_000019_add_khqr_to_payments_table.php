<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // KHQR-specific columns
            $table->string('khqr_string')->nullable()->after('proof_image')
                ->comment('Raw QR payload string');
            $table->string('khqr_md5', 32)->nullable()->after('khqr_string')
                ->comment('MD5 hash used to poll NBC API for confirmation');
            $table->timestamp('khqr_expires_at')->nullable()->after('khqr_md5')
                ->comment('QR code expiry; null = no expiry enforced');
        });

        // Add 'khqr' to the method enum - only for MySQL
        // SQLite doesn't support MODIFY COLUMN with ENUM
        // The initial migration now includes 'khqr' in the enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE payments
                MODIFY COLUMN method
                ENUM('cash','bank_transfer','aba','wing','acleda','khqr','other')
                DEFAULT 'cash'
            ");
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['khqr_string', 'khqr_md5', 'khqr_expires_at']);
        });

        // Remove 'khqr' from the method enum - only for MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE payments
                MODIFY COLUMN method
                ENUM('cash','bank_transfer','aba','wing','acleda','other')
                DEFAULT 'cash'
            ");
        }
    }
};
