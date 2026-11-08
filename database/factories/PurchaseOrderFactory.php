<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'po_number' => 'PO-' . now()->year . '-' . str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'status' => 'draft',
            'order_date' => fake()->date(),
            'expected_date' => fake()->date(),
            'subtotal' => fake()->randomFloat(2, 100, 10000),
            'shipping_cost' => fake()->randomFloat(2, 0, 500),
            'total_amount' => fake()->randomFloat(2, 100, 10500),
            'currency' => 'USD',
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}