<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Get a setting value by key, cast to its proper type.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cached = Cache::rememberForever("setting:{$key}", function () use ($key) {
            $setting = static::where('key', $key)->first();
            if (! $setting) {
                return null;
            }

            return [
                'type' => (string) $setting->type,
                'value' => $setting->value,
            ];
        });

        // Handle legacy or corrupted incomplete serialized objects
        if ($cached instanceof \__PHP_Incomplete_Class || is_object($cached)) {
            Cache::forget("setting:{$key}");
            $setting = static::where('key', $key)->first();
            $cached = $setting ? [
                'type' => (string) $setting->type,
                'value' => $setting->value,
            ] : null;
        }

        if (! is_array($cached)) {
            return $default;
        }

        return match ($cached['type'] ?? 'string') {
            'boolean' => filter_var($cached['value'], FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $cached['value'],
            'float' => (float) $cached['value'],
            'json' => is_string($cached['value']) ? json_decode($cached['value'], true) : $cached['value'],
            default => $cached['value'],
        };
    }

    /**
     * Set (or create) a setting value by key.
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): self
    {
        $stored = is_array($value) ? json_encode($value) : $value;

        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'group' => $group, 'type' => $type]
        );

        Cache::forget("setting:{$key}");

        return $setting;
    }

    /**
     * Get all settings for a given group as a key => value array.
     */
    public static function group(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(fn ($setting) => [$setting->key => static::get($setting->key)])
            ->toArray();
    }

    protected static function booted(): void
    {
        static::saved(fn (Setting $setting) => Cache::forget("setting:{$setting->key}"));
        static::deleted(fn (Setting $setting) => Cache::forget("setting:{$setting->key}"));
    }
}
