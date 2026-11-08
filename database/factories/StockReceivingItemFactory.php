<?php

namespace Database\Factories;

use App\Models\PurchaseOrderItem;
use App\Models\ProductVariant;
use App\Models\StockReceiving;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockReceivingItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'stock_receiving_id' => StockReceiving::factory(),
            'purchase_order_item_id' => PurchaseOrderItem::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'quantity_received' => fake()->numberBetween(1, 100),
            'unit_cost' => fake()->randomFloat(2, 10, 1000),
        ];
    }
}