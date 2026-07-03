<?php

namespace Database\Seeders;

use App\Models\PhoneStorage;
use Illuminate\Database\Seeder;

class StorageSeeder extends Seeder
{
    public function run(): void
    {
        $storages = [
            '32GB',
            '64GB',
            '128GB',
            '256GB',
            '512GB',
            '1TB',
        ];

        foreach ($storages as $storage) {
            PhoneStorage::updateOrCreate(
                ['capacity' => $storage],
                [
                    'capacity' => $storage,
                    'is_active' => true,
                ]
            );
        }
    }
}
