<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Color;
use App\Models\PhoneStorage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'color_id' => Color::factory(),
            'phone_storage_id' => PhoneStorage::factory(),
            'sku' => fake()->unique()->bothify('VAR-####'),
            'price' => fake()->randomFloat(2, 100, 5000),
            'stock' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}