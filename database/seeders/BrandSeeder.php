<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Apple',
            'Samsung',
            'Xiaomi',
            'Oppo',
            'Vivo',
            'Realme',
            'Huawei',
            'Google',
            'OnePlus',
            'Nothing',
            'Anker',
            'Ugreen',
            'Baseus',
            'Spigen',
            'ESR',
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($brand)],
                [
                    'name' => $brand,
                    'slug' => Str::slug($brand),
                ]
            );
        }
    }
}
