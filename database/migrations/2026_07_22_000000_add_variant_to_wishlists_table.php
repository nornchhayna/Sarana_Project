<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->foreignId('color_id')->nullable()->after('product_id')
                ->constrained('colors')->nullOnDelete();
            $table->foreignId('phone_storage_id')->nullable()->after('color_id')
                ->constrained('phone_storages')->nullOnDelete();
        });

        // If you currently have a unique(user_id, product_id) constraint,
        // drop it and replace it with one that includes the variant columns
        // — otherwise a user can't wishlist two different colors/storages
        // of the same product. Adjust the constraint name below to match
        // whatever your original migration named it (check with
        // `SHOW INDEX FROM wishlists;` if unsure).
        Schema::table('wishlists', function (Blueprint $table) {
            // $table->dropUnique(['user_id', 'product_id']); // uncomment + fix name if needed
            $table->unique(
                ['user_id', 'product_id', 'color_id', 'phone_storage_id'],
                'wishlists_user_product_variant_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropUnique('wishlists_user_product_variant_unique');
            $table->dropConstrainedForeignId('phone_storage_id');
            $table->dropConstrainedForeignId('color_id');
        });
    }
};
