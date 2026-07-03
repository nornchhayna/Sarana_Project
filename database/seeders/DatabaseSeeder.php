<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            BrandSeeder::class,
            PhoneModelSeeder::class,   // ⚠️ missing data
            ColorSeeder::class,
            StorageSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
