<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Smart Watch GT Series',
                'subtitle' => 'Next Generation AMOLED Health & Fitness Tracker',
                'image' => 'sliders/m6q0VYCCLxkoHRmVFkqeaHKtxj7NboyVe9cWZ5lq.png',
                'button_text' => 'Order Now',
                'button_url' => '/products',
                'badge_text' => 'NEW RELEASE',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'New Phones Arrived',
                'subtitle' => 'Flagship Smartphones with AI-Powered Cameras',
                'image' => 'sliders/MLljuAj8C3k5nu9i2lnEikP8lWkINZXCKWuXG36x.png',
                'button_text' => 'Explore Models',
                'button_url' => '/products',
                'badge_text' => 'HOT DEALS',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Summer Tech Sale',
                'subtitle' => 'Enjoy Up To 20% OFF on Top Brand Accessories',
                'image' => 'sliders/0SuHmHFSkuGPKeiS9ngR6Na5wAnoC9wV0NfFkEbn.webp',
                'button_text' => 'Shop Discount',
                'button_url' => '/products',
                'badge_text' => 'LIMITED TIME',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slide) {
            Slider::updateOrCreate(
                ['title' => $slide['title']],
                $slide
            );
        }
    }
}
