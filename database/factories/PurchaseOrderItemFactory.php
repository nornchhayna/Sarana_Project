<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'quantity_ordered' => fake()->numberBetween(1, 100),
            'quantity_received' => fake()->numberBetween(0, 100),
            'unit_cost' => fake()->randomFloat(2, 10, 1000),
        ];
    }
}