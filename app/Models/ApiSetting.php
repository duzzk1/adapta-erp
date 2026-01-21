<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiSetting extends Model
{
    protected $fillable = ['api', 'settings'];

    protected $table = 'api_settings';

    public $timestamps = true;

    protected $casts = [
        'settings' => 'array',
    ];

    public static function get(string $api, $default = null): ?array
    {
        $row = static::query()->where('api', $api)->first();
        return $row ? ($row->settings ?? $default) : $default;
    }

    public static function set(string $api, array $settings): void
    {
        $existing = static::query()->where('api', $api)->first();
        if ($existing) {
            $merged = array_merge($existing->settings ?? [], $settings);
            $existing->settings = $merged;
            $existing->save();
        } else {
            static::query()->create(['api' => $api, 'settings' => $settings]);
        }
    }
}
