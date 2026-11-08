<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            PhoneModelSeeder::class,
            ColorSeeder::class,
            StorageSeeder::class,
            RamSeeder::class,
            ProductSeeder::class,
            SliderSeeder::class,
            BlogSeeder::class,
            GiftSeeder::class,
            SupplierSeeder::class,
            PromotionSeeder::class,
            ReviewSeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
            
        ]);
    }
}
