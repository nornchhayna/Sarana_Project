<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('role', User::ROLE_ADMIN)->first() ?? User::first();
        $authorId = $author?->id ?? 1;

        $blogs = [
            [
                'title' => 'Top 10 Smartphones of 2026: The Ultimate Guide',
                'slug' => 'top-10-smartphones-of-2026',
                'content' => "The smartphone market in 2026 has witnessed unprecedented advancements in artificial intelligence, battery efficiency, and camera hardware. From Apple's A18 Pro chip to Qualcomm's Snapdragon 8 Elite, mobile performance has reached desktop levels. In this comprehensive review, we test and compare the top flagships including iPhone 16 Pro Max, Samsung Galaxy S25 Ultra, and Xiaomi 15 Ultra.",
                'excerpt' => 'Our comprehensive guide to the top performing smartphones this year with detailed camera and battery tests.',
                'image' => 'blogs/ah2hj8Fqd8kKNYrAztx2rTmei678hRrCB3yyl3Fq.jpg',
                'is_published' => true,
                'published_at' => now(),
                'author_id' => $authorId,
            ],
            [
                'title' => 'How to Choose the Right Phone Case for Drop Protection',
                'slug' => 'how-to-choose-the-right-phone-case',
                'content' => "Protecting your investment is essential when modern smartphones cost upwards of a thousand dollars. Learn how different materials like TPU, polycarbonate, and silicone absorb shock differently. We also discuss raised camera bezels, reinforced corners, and MagSafe compatibility.",
                'excerpt' => 'Tips and expert advice for selecting the perfect protective case for your daily smartphone usage.',
                'image' => 'blogs/bSygMIGZO25JB49if5XdfJMd6U6Q1c74ayyvguiF.jpg',
                'is_published' => true,
                'published_at' => now()->subDays(2),
                'author_id' => $authorId,
            ],
            [
                'title' => 'Wireless Charging & GaN: Everything You Need to Know',
                'slug' => 'wireless-charging-and-gan-guide',
                'content' => "Gallium Nitride (GaN) has revolutionized charging technology by delivering higher wattage in significantly smaller form factors with reduced heat generation. Combined with Qi2 magnetic wireless charging, keeping your devices charged at work or on the road has never been easier.",
                'excerpt' => 'A complete guide to fast GaN chargers, Qi2 wireless charging, and maintaining your smartphone battery health.',
                'image' => 'blogs/4m7Nwl5hcAP1szzzjVczO5JsnsJycQftSY5fh6wY.webp',
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'author_id' => $authorId,
            ],
            [
                'title' => 'Best Everyday Accessories for Your New Tech Setup',
                'slug' => 'best-everyday-accessories-for-tech-setup',
                'content' => "Enhance your digital workspace with essential accessories: from braided high-durability cables to multi-device GaN adapters, Bluetooth audio earphones, and tempered glass screen guards. Here are our top editor-picked accessories for 2026.",
                'excerpt' => 'Essential daily accessories every smartphone and tech owner should have in their gear bag.',
                'image' => 'blogs/KupAEQj3hVjpjWcKjd5z6z3JmE7gf8ang7BSffeb.jpg',
                'is_published' => true,
                'published_at' => now()->subWeek(),
                'author_id' => $authorId,
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::updateOrCreate(
                ['slug' => $blog['slug']],
                $blog
            );
        }
    }
}
