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
            [
                'name' => 'Phone Cases',
                'slug' => 'phone-cases',
                'image' => 'categories/phone-cases.png',
                'description' => 'Protective cases, silicone covers, and heavy duty cases.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Screen Protectors',
                'slug' => 'screen-protectors',
                'image' => 'categories/Screenp-Protectors.png',
                'description' => '9H tempered glass, privacy screens, and matte protectors.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Chargers',
                'slug' => 'chargers',
                'image' => 'categories/x5C8J1Wp1UOIy5Qqj7vEOsJPFNpoV9nytgRvbwgf.png',
                'description' => 'Fast wall chargers, GaN power adapters, and multi-port docks.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Charging Cables',
                'slug' => 'charging-cables',
                'image' => 'categories/swWaMla7o6MYPKshZuA4uGtGLUFDhaLUTypfu3Yn.webp',
                'description' => 'Braided USB-C, Lightning, and fast charging data cables.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Power Banks',
                'slug' => 'power-banks',
                'image' => 'categories/5sV8KdsU8l6pvIEfueeaNXgmsszTV2iB4dCQ5rVo.webp',
                'description' => 'High capacity portable power banks and magnetic battery packs.',
                'sort_order' => 5,
            ],
            [
                'name' => 'Wireless Chargers',
                'slug' => 'wireless-chargers',
                'image' => 'categories/8vquSnZQzQUhdLaaY6phpfC5yUQdAxSAi8ZR9ELb.webp',
                'description' => 'Qi wireless charging pads, stands, and 3-in-1 charging stations.',
                'sort_order' => 6,
            ],
            [
                'name' => 'Camera Protectors',
                'slug' => 'camera-protectors',
                'image' => 'categories/O5ypicv3eyD32s71ctmVGFOxdFKSn8Xh0Ey2RLgJ.webp',
                'description' => 'Scratch resistant camera lens protectors and ring guards.',
                'sort_order' => 7,
            ],
            [
                'name' => 'Phone Holders',
                'slug' => 'phone-holders',
                'image' => 'categories/eHizskTNhbeIBKzdhopIjhaiyXYyIo37MEnevsTm.png',
                'description' => 'Desk stands, magnetic rings, kickstands, and grips.',
                'sort_order' => 8,
            ],
            [
                'name' => 'Car Accessories',
                'slug' => 'car-accessories',
                'image' => 'categories/onmb6k5ehF0yyMQbt9H6WdOd1vsz1oh0KEOl1IrD.png',
                'description' => 'Car mounts, MagSafe air-vent holders, and car fast chargers.',
                'sort_order' => 9,
            ],
            [
                'name' => 'Earphones',
                'slug' => 'earphones',
                'image' => 'categories/wyS4nA9g7jrxk5etxCzyrrl9aKHV12HoYZmE3ybM.png',
                'description' => 'Wireless earbuds, noise-cancelling headphones, and in-ear monitors.',
                'sort_order' => 10,
            ],
            [
                'name' => 'Smart Watches',
                'slug' => 'smart-watches',
                'image' => 'categories/B32h5jx0bzywvmxc1XZ0mToXvZMKFngPoETtLg5o.png',
                'description' => 'Smartwatches, fitness bands, and wearable tech accessories.',
                'sort_order' => 11,
            ],
            [
                'name' => 'Bluetooth Speakers',
                'slug' => 'bluetooth-speakers',
                'image' => 'categories/wyS4nA9g7jrxk5etxCzyrrl9aKHV12HoYZmE3ybM.png',
                'description' => 'Portable Bluetooth speakers with rich sound and water resistance.',
                'sort_order' => 12,
            ],
        ];

        foreach ($categories as $cat) {
            Category::withTrashed()->updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'image' => $cat['image'],
                    'description' => $cat['description'],
                    'sort_order' => $cat['sort_order'],
                    'is_active' => true,
                    'deleted_at' => null,
                ]
            );
        }
    }
}
