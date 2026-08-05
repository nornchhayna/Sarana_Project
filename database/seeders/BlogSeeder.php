<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'title' => 'Top 10 Smartphones of 2024',
                'slug' => 'top-10-smartphones-2024',
                'content' => 'Discover the best smartphones available in 2024 with our comprehensive review...',
                'excerpt' => 'Our comprehensive guide to the best smartphones this year.',
                'image' => null,
                'is_published' => true,
                'published_at' => now(),
                'author_id' => 1,
            ],
            [
                'title' => 'How to Choose the Right Phone Case',
                'slug' => 'how-to-choose-right-phone-case',
                'content' => 'Protect your investment with the perfect phone case. Learn about materials, styles, and protection levels...',
                'excerpt' => 'Tips for selecting the perfect phone case for your device.',
                'image' => null,
                'is_published' => true,
                'published_at' => now()->subDays(2),
                'author_id' => 1,
            ],
            [
                'title' => 'Wireless Charging: Everything You Need to Know',
                'slug' => 'wireless-charging-guide',
                'content' => 'Wireless charging has become standard. Learn about different standards, speeds, and compatibility...',
                'excerpt' => 'A complete guide to wireless charging technology.',
                'image' => null,
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'author_id' => 1,
            ],
            [
                'title' => 'Best Accessories for Your New Phone',
                'slug' => 'best-phone-accessories',
                'content' => 'Enhance your phone experience with these must-have accessories from cases to chargers...',
                'excerpt' => 'Essential accessories every phone owner should have.',
                'image' => null,
                'is_published' => true,
                'published_at' => now()->subWeek(),
                'author_id' => 1,
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::create($blog);
        }
    }
}
