<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promotions = [
            [
                'name' => 'New Customer Welcome Coupon',
                'code' => 'WELCOME10',
                'description' => 'Get 10% discount on your first tech or accessory order.',
                'type' => 'percentage',
                'value' => 10.00,
                'min_order_amount' => 20.00,
                'max_discount_amount' => 50.00,
                'usage_limit' => 500,
                'usage_count' => 12,
                'is_active' => true,
                'starts_at' => now()->subMonth(),
                'expires_at' => now()->addYear(),
            ],
            [
                'name' => 'Save $5 on Accessories',
                'code' => 'SAVE5',
                'description' => 'Flat $5 off for any accessories order over $30.',
                'type' => 'fixed',
                'value' => 5.00,
                'min_order_amount' => 30.00,
                'max_discount_amount' => 5.00,
                'usage_limit' => 200,
                'usage_count' => 28,
                'is_active' => true,
                'starts_at' => now()->subWeeks(2),
                'expires_at' => now()->addMonths(6),
            ],
            [
                'name' => 'Mega Flagship Promo 15% OFF',
                'code' => 'TECH15',
                'description' => 'Special 15% discount for premium phones and smart watches.',
                'type' => 'percentage',
                'value' => 15.00,
                'min_order_amount' => 100.00,
                'max_discount_amount' => 150.00,
                'usage_limit' => 100,
                'usage_count' => 5,
                'is_active' => true,
                'starts_at' => now()->subDays(5),
                'expires_at' => now()->addMonths(3),
            ],
        ];

        foreach ($promotions as $promo) {
            Promotion::updateOrCreate(
                ['code' => $promo['code']],
                $promo
            );
        }
    }
}
