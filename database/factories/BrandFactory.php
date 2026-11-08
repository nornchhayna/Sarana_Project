<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();
        return [
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}