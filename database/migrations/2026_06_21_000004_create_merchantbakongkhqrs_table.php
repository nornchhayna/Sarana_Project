<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchantbakongkhqrs', function (Blueprint $table) {
            $table->id();
            $table->string('bakongAccountID');   // e.g. yourname@wing or yourname@acleda
            $table->string('merchantName');       // displayed on QR
            $table->string('merchantCity');       // e.g. Phnom Penh
            $table->string('currency')->default('USD');
            $table->string('countryCode')->default('KH');
            $table->timestamps();
        });

        // Seed with placeholder — update these values with your real Bakong account
        DB::table('merchantbakongkhqrs')->insert([
            'bakongAccountID' => 'nonchhayna@wing',    // ← change to your Bakong ID
            'merchantName' => 'Ashahi Shop',       // ← change to your shop name
            'merchantCity' => 'Phnom Penh',
            'currency' => 'USD',
            'countryCode' => 'KH',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('merchantbakongkhqrs');
    }
};
