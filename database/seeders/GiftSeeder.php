<?php

namespace Database\Seeders;

use App\Models\Gift;
use App\Models\Product;
use Illuminate\Database\Seeder;

class GiftSeeder extends Seeder
{
    public function run(): void
    {
        $gift = Gift::updateOrCreate(
            ['name' => 'Premium Brand Gift Package'],
            [
                'name' => 'Premium Brand Gift Package',
                'description' => 'Exclusive complimentary accessories pack including branded sports items and phone lanyard.',
                'image' => 'gifts/KVy7zrN4FsjspvlmnVdp3VcAGlMcfUJmOlbq7iO5.png',
                'is_active' => true,
            ]
        );

        // Attach gift to flagship products
        $flagships = Product::whereIn('slug', ['iphone-16-pro-max', 'samsung-galaxy-s25-ultra'])->get();
        foreach ($flagships as $product) {
            $product->gifts()->syncWithoutDetaching([
                $gift->id => ['quantity' => 1]
            ]);
        }
    }
}
