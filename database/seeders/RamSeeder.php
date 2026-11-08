<?php

namespace Database\Seeders;

use App\Models\PhoneRam;
use Illuminate\Database\Seeder;

class RamSeeder extends Seeder
{
    public function run(): void
    {
        $rams = [
            '4GB',
            '6GB',
            '8GB',
            '12GB',
            '16GB',
            '24GB',
        ];

        foreach ($rams as $ram) {
            PhoneRam::updateOrCreate(
                ['capacity' => $ram],
                [
                    'capacity' => $ram,
                    'label' => $ram,
                    'is_active' => true,
                ]
            );
        }
    }
}
