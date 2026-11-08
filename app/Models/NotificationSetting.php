<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'email_enabled',
        'sms_enabled',
        'push_enabled',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'push_enabled' => 'boolean',
    ];

    /**
     * Check whether a given channel is enabled for this notification key.
     */
    public static function channelEnabled(string $key, string $channel): bool
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return false;
        }

        return match ($channel) {
            'email' => $setting->email_enabled,
            'sms' => $setting->sms_enabled,
            'push' => $setting->push_enabled,
            default => false,
        };
    }
}
