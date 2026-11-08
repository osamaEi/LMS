<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function allKeyed(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Supported social networks: key => [Bootstrap icon, label, brand color]
     */
    public const SOCIALS = [
        'social_twitter'   => ['icon' => 'bi-twitter-x', 'label' => 'X',         'color' => '#000000'],
        'social_instagram' => ['icon' => 'bi-instagram', 'label' => 'Instagram', 'color' => '#e1306c'],
        'social_tiktok'    => ['icon' => 'bi-tiktok',    'label' => 'TikTok',    'color' => '#000000'],
        'social_snapchat'  => ['icon' => 'bi-snapchat',  'label' => 'Snapchat',  'color' => '#fffc00'],
        'social_linkedin'  => ['icon' => 'bi-linkedin',  'label' => 'LinkedIn',  'color' => '#0a66c2'],
        'social_youtube'   => ['icon' => 'bi-youtube',   'label' => 'YouTube',   'color' => '#ff0000'],
        'social_facebook'  => ['icon' => 'bi-facebook',  'label' => 'Facebook',  'color' => '#1877f2'],
        'social_whatsapp'  => ['icon' => 'bi-whatsapp',  'label' => 'WhatsApp',  'color' => '#25d366'],
    ];

    /**
     * Social links merged from admin/settings (Setting) and footer settings (SiteSetting).
     * Each item: ['key','icon','label','color','url'] — url may be empty.
     */
    public static function socialLinks(?array $siteSettings = null): array
    {
        $site = $siteSettings ?? static::allKeyed();
        $main = Setting::where('group', 'social')->pluck('value', 'key')->toArray();

        $links = [];
        foreach (self::SOCIALS as $key => $meta) {
            $url = trim((string) (($main[$key] ?? '') ?: ($site[$key] ?? '')));
            if ($url !== '' && !preg_match('#^(https?:)?//|^(mailto|tel):#i', $url)) {
                $url = 'https://' . ltrim($url, '/');
            }
            $links[] = $meta + ['key' => $key, 'url' => $url];
        }

        return $links;
    }
}
