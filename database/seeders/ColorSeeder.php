<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            'Black',
            'White',
            'Blue',
            'Red',
            'Green',
            'Purple',
            'Pink',
            'Yellow',
            'Silver',
            'Gold',
            'Gray',
            'Titanium',
            'Natural Titanium',
            'Desert Titanium',
        ];

        foreach ($colors as $color) {
            Color::updateOrCreate(
                ['name' => $color],
                ['name' => $color]
            );
        }
    }
}
