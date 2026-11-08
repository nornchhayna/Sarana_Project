<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ColorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->colorName(),
            'hex_code' => fake()->hexColor(),
            'is_active' => true,
        ];
    }
}