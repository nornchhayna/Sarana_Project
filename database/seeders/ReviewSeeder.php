<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('role', User::ROLE_CUSTOMER)->first() ?? User::first();
        $order = Order::first();
        $product1 = Product::where('slug', 'iphone-16-pro-max')->first() ?? Product::first();
        $product2 = Product::where('slug', 'samsung-galaxy-s25-ultra')->first() ?? Product::skip(1)->first();

        if ($product1 && $user) {
            Review::updateOrCreate(
                ['product_id' => $product1->id, 'user_id' => $user->id],
                [
                    'product_id' => $product1->id,
                    'user_id' => $user->id,
                    'order_id' => $order?->id,
                    'rating' => 5,
                    'title' => 'Stunning performance and battery life!',
                    'body' => 'Absolutely genuine item, sealed in original box with all paperwork. The display is ultra smooth, and camera zoom captures incredible detail even at night.',
                    'images' => [
                        'review-images/PwCttv4SmyOfdA13QtfoWBJIhMGoZniMGNAokpIY.png'
                    ],
                    'is_approved' => true,
                ]
            );
        }

        if ($product2 && $user) {
            Review::updateOrCreate(
                ['product_id' => $product2->id, 'user_id' => $user->id],
                [
                    'product_id' => $product2->id,
                    'user_id' => $user->id,
                    'order_id' => $order?->id,
                    'rating' => 5,
                    'title' => 'Top notch build quality and fast delivery!',
                    'body' => 'Delivered in Phnom Penh within 2 hours of ordering. Very satisfied with customer support and warranty assurance.',
                    'images' => [
                        'review-images/xp1ambYnaUteBydzgrt7hFZeJv1vQ9DRreDehFsM.webp'
                    ],
                    'is_approved' => true,
                ]
            );
        }
    }
}
