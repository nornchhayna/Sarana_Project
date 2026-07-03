<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            ['code' => 'WELCOME10', 'type' => 'percent', 'value' => 10, 'min_order' => 0,   'max_uses' => null],
            ['code' => 'SAVE20',    'type' => 'percent', 'value' => 20, 'min_order' => 50,  'max_uses' => 100],
            ['code' => 'FLAT5',     'type' => 'fixed',   'value' => 5,  'min_order' => 30,  'max_uses' => 200],
        ];

        foreach ($codes as $code) {
            PromoCode::firstOrCreate(['code' => $code['code']], array_merge($code, ['is_active' => true]));
        }
    }
}
