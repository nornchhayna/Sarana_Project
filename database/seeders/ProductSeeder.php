<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PhoneModel;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {

        $category = Category::first();
        $brand = Brand::first();
        $phoneModel = PhoneModel::first();

        Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'phone_model_id' => $phoneModel->id,
            'name' => 'Premium Silicone Case',
            'slug' => Str::slug('Premium Silicone Case'),
            'sku' => 'CASE-001',
            'description' => 'High-quality silicone protective case.',
            'specifications' => json_encode([
                'material' => 'Silicone',
                'color' => 'Black',
                'weight' => '35g',
            ]),
            'base_price' => 19.99,
            'thumbnail' => 'products/case-001-thumb.jpg',
            'images' => [
                'products/case-001-1.jpg',
                'products/case-001-2.jpg',
            ],
            'is_active' => true,
            'is_featured' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'phone_model_id' => $phoneModel->id,
            'name' => 'Tempered Glass Protector',
            'slug' => Str::slug('Tempered Glass Protector'),
            'sku' => 'GLASS-001',
            'description' => '9H hardness screen protector.',
            'specifications' => json_encode([
                'hardness' => '9H',
                'thickness' => '0.3mm',
            ]),
            'base_price' => 9.99,
            'thumbnail' => 'products/glass-001-thumb.jpg',
            'images' => [
                'products/glass-001-1.jpg',
                'products/glass-001-2.jpg',
            ],
            'is_active' => true,
            'is_featured' => false,
        ]);
    }
}
