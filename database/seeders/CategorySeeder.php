<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Phone Cases',
            'Screen Protectors',
            'Chargers',
            'Charging Cables',
            'Power Banks',
            'Wireless Chargers',
            'Camera Protectors',
            'Phone Holders',
            'Car Accessories',
            'Earphones',
            'Bluetooth Speakers',
            'Smart Watches',
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category)],
                [
                    'name' => $category,
                    'slug' => Str::slug($category),
                ]
            );
        }
    }
}
