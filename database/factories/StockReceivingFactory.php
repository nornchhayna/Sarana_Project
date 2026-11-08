<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockReceivingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'supplier_id' => Supplier::factory(),
            'reference_number' => fake()->optional()->bothify('REF-####'),
            'received_by' => User::factory(),
            'received_at' => fake()->dateTime(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}