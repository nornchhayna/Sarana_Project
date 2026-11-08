<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_variant_id' => ProductVariant::factory(),
            'type' => fake()->randomElement(['purchase_receipt', 'sale', 'adjustment_in', 'adjustment_out', 'sale_return', 'damage']),
            'quantity' => fake()->numberBetween(1, 100),
            'reference_type' => fake()->randomElement(['stock_receiving', 'manual_adjustment', 'order']),
            'reference_id' => fake()->optional()->numberBetween(1, 1000),
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}