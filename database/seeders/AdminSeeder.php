<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@12345'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Sample webmaster
        User::updateOrCreate(
            ['email' => 'webmaster@gmail.com'],
            [
                'name' => 'Web Master',
                'password' => Hash::make('Webmaster@12345'),
                'role' => User::ROLE_WEBMASTER,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin:     admin@gmail.com     / Admin@12345');
        $this->command->info('Webmaster: webmaster@gmail.com / Webmaster@12345');
    }
}
