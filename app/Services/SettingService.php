<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingService
{
    protected static array $runtimeCache = [];

    /**
     * Get setting value by key with fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, static::$runtimeCache)) {
            return static::$runtimeCache[$key];
        }

        try {
            if (!Schema::hasTable('settings')) {
                return $default;
            }

            $val = Cache::remember("setting_{$key}", 3600, function () use ($key) {
                $setting = Setting::where('key', $key)->first();
                return $setting ? $setting->value : null;
            });

            $result = $val !== null ? $val : $default;
            static::$runtimeCache[$key] = $result;

            return $result;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set or update setting value by key.
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string', ?string $description = null): Setting
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'group' => $group,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget("setting_{$key}");
        static::$runtimeCache[$key] = (string) $value;

        return $setting;
    }

    /**
     * Clear runtime and system cache for settings.
     */
    public static function clearCache(?string $key = null): void
    {
        if ($key) {
            Cache::forget("setting_{$key}");
            unset(static::$runtimeCache[$key]);
        } else {
            static::$runtimeCache = [];
        }
    }
}
