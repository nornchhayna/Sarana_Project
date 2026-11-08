<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneStorageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'capacity' => fake()->randomElement(['64GB', '128GB', '256GB', '512GB', '1TB']),
            'is_active' => true,
        ];
    }
}