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
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'logo' => 'brands/jdMmSRcdqwVMRrSiFmnw6xfM9mP3k7xQ2VFoQkCS.png',
                'description' => 'Premium smartphones, tablets, wearables, and audio accessories.',
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'logo' => 'brands/tgXdbUYAObLIMbZvVf3uayIHj3V03lTMKnJ1Y62m.png',
                'description' => 'Innovative Galaxy smartphones, foldable displays, and smart electronics.',
            ],
            [
                'name' => 'Xiaomi',
                'slug' => 'xiaomi',
                'logo' => 'brands/iGcQWbT0yFbzfcIHUs1G9x4splEtEWwzXgMR0RZ1.png',
                'description' => 'High-performance flagship smartphones and smart lifestyle accessories.',
            ],
            [
                'name' => 'Oppo',
                'slug' => 'oppo',
                'logo' => 'brands/vp0t6UdFprycu5AySkNrOCPtfGQez4w9yBRiy2ys.png',
                'description' => 'Aesthetic design and photography-focused smartphones.',
            ],
            [
                'name' => 'Vivo',
                'slug' => 'vivo',
                'logo' => 'brands/mW9J6Gvgoty97WfN0nGM8nM10hM3X84TVLbODEQ0.png',
                'description' => 'Leading camera smartphones and mobile technology.',
            ],
            [
                'name' => 'Realme',
                'slug' => 'realme',
                'logo' => 'brands/WX0v2QGtHU3ZEgvOJFdBcKgaccDIgm0COdfOUrOz.png',
                'description' => 'Dare to leap with trendy styling and powerful mobile performance.',
            ],
            [
                'name' => 'Huawei',
                'slug' => 'huawei',
                'logo' => 'brands/zCEunk7sDMLzlkUc25iZViu1ctg79cbMaHhmWNsx.png',
                'description' => 'World-class optics, telecommunications, and high-end hardware.',
            ],
            [
                'name' => 'Google',
                'slug' => 'google',
                'logo' => 'brands/Sm7n8jl1CjKjN2dTKUoo6XRM1wy0Ehwqxmh9C1RK.png',
                'description' => 'Pixel smartphones with state-of-the-art Google AI and pure Android.',
            ],
            [
                'name' => 'OnePlus',
                'slug' => 'oneplus',
                'logo' => 'brands/RSQDzykV7s6afr5iC9qcxtB4TaUYH54evVFkxwW3.png',
                'description' => 'Never Settle - Fast and smooth flagship mobile experience.',
            ],
            [
                'name' => 'Nothing',
                'slug' => 'nothing',
                'logo' => 'brands/K9B6UUqGjGpNZucs9syB7hpu2JiR1B7iP8Kf0Tn0.png',
                'description' => 'Transparent glyph interface design and clean OS.',
            ],
            [
                'name' => 'Anker',
                'slug' => 'anker',
                'logo' => 'brands/zPtZNnaVMCDSg8lRDPa8hTkn3MyHPUaN8xV8cIvo.png',
                'description' => 'Global leader in GaN fast charging, power banks, and audio.',
            ],
            [
                'name' => 'Baseus',
                'slug' => 'baseus',
                'logo' => 'brands/t6cizHf9ktAZY2NLncORDtMqH7WSfuN7UlsnxN37.png',
                'description' => 'Base on user - Practical and minimalist mobile accessories.',
            ],
            [
                'name' => 'ESR',
                'slug' => 'esr',
                'logo' => 'brands/44Zt195ZKSTBg0H1OEHITUhwHJ33iXGq4ebZattR.png',
                'description' => 'Tech made easier - High quality protective cases and screen protectors.',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::withTrashed()->updateOrCreate(
                ['slug' => $brand['slug']],
                [
                    'name' => $brand['name'],
                    'slug' => $brand['slug'],
                    'logo' => $brand['logo'],
                    'description' => $brand['description'],
                    'is_active' => true,
                    'deleted_at' => null,
                ]
            );
        }
    }
}
