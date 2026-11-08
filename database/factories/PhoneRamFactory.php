<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneRamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'capacity' => fake()->randomElement(['4GB', '8GB', '12GB', '16GB', '32GB']),
            'label' => fake()->randomElement(['4GB', '8GB', '12GB', '16GB', '32GB']),
            'is_active' => true,
        ];
    }
}