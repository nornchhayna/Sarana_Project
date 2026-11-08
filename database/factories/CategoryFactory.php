<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->word();
        return [
            'name' => $name,
            'slug' => strtolower($name),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}