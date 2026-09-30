<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public const DEFAULTS = [
        'site_name' => 'Social Network',
        'site_description' => 'منصة اجتماعية للتواصل ومشاركة المحتوى.',
        'support_email' => '',
        'registration_enabled' => true,
        'maintenance_mode' => false,
        'max_upload_mb' => 200,
        'posts_per_page' => 10,
        'primary_color' => '#0866ff',
        'accent_color' => '#42b72a',
        'page_background' => '#f0f2f5',
        'logo_path' => '',
        'favicon_path' => '',
        'login_background_path' => '',
        'navbar_color' => '#ffffff',
        'font_family' => 'Arial',
        'card_radius' => 8,
        'login_title' => 'تواصل وشارك مع مجتمعك.',
        'login_subtitle' => 'ابقَ على تواصل مع الأصدقاء وشارك اهتماماتك وأفكارك.',
        'footer_text' => '© Social Network',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('site_settings.all', 3600, function () {
            return static::query()->get()->mapWithKeys(function (self $setting) {
                return [$setting->key => static::castValue($setting->value, $setting->type)];
            })->all();
        });

        return $settings[$key] ?? static::DEFAULTS[$key] ?? $default;
    }

    public static function putValue(string $key, mixed $value, string $type = 'string'): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value, 'type' => $type]
        );
        Cache::forget('site_settings.all');
    }

    public static function allWithDefaults(): array
    {
        return collect(static::DEFAULTS)->mapWithKeys(fn ($default, $key) => [
            $key => static::getValue($key, $default),
        ])->all();
    }

    private static function castValue(?string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            default => $value,
        };
    }
}
