<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@12345'),
                'role' => User::ROLE_ADMIN,
                'avatar' => 'avatars/cm8ybmug7b60IXLg6g1LYCY8KD8ZeiSPJfKTvpQx.jpg',
                'phone' => '012345678',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Fallback admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'avatar' => 'avatars/cm8ybmug7b60IXLg6g1LYCY8KD8ZeiSPJfKTvpQx.jpg',
                'phone' => '012345679',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Webmaster
        User::updateOrCreate(
            ['email' => 'webmaster@gmail.com'],
            [
                'name' => 'Web Master',
                'password' => Hash::make('Webmaster@12345'),
                'role' => User::ROLE_WEBMASTER,
                'avatar' => 'avatars/vCR9YnEfEzChVYSbF6qkKEanBSEdu9yM5mAYFomx.png',
                'phone' => '098765432',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Staff
        User::updateOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Store Staff',
                'password' => Hash::make('Staff@12345'),
                'role' => User::ROLE_WEBMASTER,
                'avatar' => 'avatars/W6crumMRCeZllXV2fdmy6bnKnqAh8iKs1xZusFs3.jpg',
                'phone' => '087654321',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Customer 1
        User::updateOrCreate(
            ['email' => 'chhaynatah@gmail.com'],
            [
                'name' => 'Chhayna Norn',
                'password' => Hash::make('Password@123'),
                'role' => User::ROLE_CUSTOMER,
                'avatar' => 'avatars/gOPYiqNruKFh6AnYjc3yq3G5DdWpUUUuYdbRJtn1.png',
                'phone' => '0967177746',
                'address' => 'Phnom Penh, Cambodia',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Customer 2
        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'John Customer',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
                'avatar' => 'avatars/RFUH8HaE3nn1WFaMnZz9oREiwXps9E7saQDaZWQ0.jpg',
                'phone' => '011223344',
                'address' => 'Toul Kork, Phnom Penh',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
