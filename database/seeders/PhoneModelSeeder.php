<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\PhoneModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PhoneModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            'Apple' => [
                'iPhone 15',
                'iPhone 15 Plus',
                'iPhone 15 Pro',
                'iPhone 15 Pro Max',
                'iPhone 16',
                'iPhone 16 Plus',
                'iPhone 16 Pro',
                'iPhone 16 Pro Max',
            ],

            'Samsung' => [
                'Galaxy S24',
                'Galaxy S24 Plus',
                'Galaxy S24 Ultra',
                'Galaxy S25',
                'Galaxy S25 Plus',
                'Galaxy S25 Ultra',
                'Galaxy A55',
                'Galaxy A56',
                'Galaxy Z Flip6',
                'Galaxy Z Fold6',
            ],

            'Xiaomi' => [
                'Xiaomi 14',
                'Xiaomi 14 Ultra',
                'Xiaomi 15',
                'Xiaomi 15 Ultra',
                'Redmi Note 13',
                'Redmi Note 14',
                'POCO X7',
                'POCO F7',
            ],

            'Oppo' => [
                'Find X8',
                'Find X8 Pro',
                'Reno 12',
                'Reno 13',
                'A5 Pro',
            ],

            'Vivo' => [
                'X200',
                'X200 Pro',
                'V40',
                'V50',
                'Y39',
            ],

            'Realme' => [
                'GT 7',
                'GT 7 Pro',
                'Realme 14 Pro',
                'Realme 14 Pro Plus',
                'C75',
            ],

            'Huawei' => [
                'Pura 70',
                'Pura 70 Pro',
                'Pura 80',
                'Mate 70 Pro',
                'Nova 13',
            ],

            'Google' => [
                'Pixel 9',
                'Pixel 9 Pro',
                'Pixel 9 Pro XL',
                'Pixel 10',
                'Pixel 10 Pro',
            ],

            'OnePlus' => [
                'OnePlus 12',
                'OnePlus 13',
                'OnePlus 13R',
                'Nord 4',
                'Nord 5',
            ],

            'Nothing' => [
                'Phone (2)',
                'Phone (2a)',
                'Phone (3)',
                'Phone (3a)',
                'Phone (3a) Pro',
            ],
        ];

        foreach ($models as $brandName => $phones) {

            $brand = Brand::where('name', $brandName)->first();

            if (! $brand) {
                continue;
            }

            foreach ($phones as $phone) {
                PhoneModel::updateOrCreate(
                    [
                        'brand_id' => $brand->id,
                        'slug' => Str::slug($phone),
                    ],
                    [
                        'name' => $phone,
                        'brand_id' => $brand->id,
                        'slug' => Str::slug($phone),
                    ]
                );
            }
        }
    }
}
