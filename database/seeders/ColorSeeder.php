<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Black', 'hex_code' => '#000000'],
            ['name' => 'White', 'hex_code' => '#FFFFFF'],
            ['name' => 'Natural Titanium', 'hex_code' => '#8C867A'],
            ['name' => 'Desert Titanium', 'hex_code' => '#C5B49F'],
            ['name' => 'Black Titanium', 'hex_code' => '#2B2B2B'],
            ['name' => 'White Titanium', 'hex_code' => '#F2F1ED'],
            ['name' => 'Silver', 'hex_code' => '#E1E2E6'],
            ['name' => 'Gold', 'hex_code' => '#F5E7D0'],
            ['name' => 'Space Gray', 'hex_code' => '#4A4B4D'],
            ['name' => 'Deep Blue', 'hex_code' => '#1D3557'],
            ['name' => 'Midnight', 'hex_code' => '#171E27'],
            ['name' => 'Starlight', 'hex_code' => '#E3E4E5'],
            ['name' => 'Alpine Green', 'hex_code' => '#505A4E'],
            ['name' => 'Purple', 'hex_code' => '#6A4C93'],
            ['name' => 'Titanium Yellow', 'hex_code' => '#F4DC7D'],
        ];

        foreach ($colors as $color) {
            Color::updateOrCreate(
                ['name' => $color['name']],
                [
                    'name' => $color['name'],
                    'hex_code' => $color['hex_code'],
                    'is_active' => true,
                ]
            );
        }
    }
}
