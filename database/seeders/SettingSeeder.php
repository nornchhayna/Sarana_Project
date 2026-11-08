<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Ashahi Store',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Website Name',
                'description' => 'The brand name displayed on headers and invoices.',
            ],
            [
                'key' => 'site_logo',
                'value' => 'settings/gl6oxUumDeRjz7zxu2CIEb1yfObMICOQ0WXUckRj.png',
                'type' => 'file',
                'group' => 'general',
                'label' => 'Site Logo',
                'description' => 'Header and checkout logo.',
            ],
            [
                'key' => 'contact_email',
                'value' => 'contact@ashahi.com',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Contact Email',
                'description' => 'Customer support email address.',
            ],
            [
                'key' => 'contact_phone',
                'value' => '+855 96 717 7746',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Contact Phone Number',
                'description' => 'Customer helpline phone number.',
            ],
            [
                'key' => 'store_address',
                'value' => 'Phnom Penh, Cambodia',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Store Address',
                'description' => 'Physical store location.',
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Bangkok',
                'type' => 'string',
                'group' => 'general',
                'label' => 'System Timezone',
                'description' => 'Default system timezone.',
            ],
            [
                'key' => 'currency',
                'value' => 'USD',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Default Currency',
                'description' => 'Store pricing currency code.',
            ],
            [
                'key' => 'two_factor_required',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'security',
                'label' => 'Require 2FA',
                'description' => 'Enforce two-factor authentication for admin accounts.',
            ],
            [
                'key' => 'session_lifetime',
                'value' => '120',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Session Lifetime (Minutes)',
                'description' => 'Idle admin session expiration timeout.',
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Minimum Password Length',
                'description' => 'Minimum character count required for user passwords.',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
