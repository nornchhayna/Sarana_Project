<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true);
        return [
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'description' => fake()->paragraph(),
            'base_price' => fake()->randomFloat(2, 100, 5000),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'is_active' => true,
            'sku' => fake()->unique()->bothify('PROD-####'),
        ];
    }
}