<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'key',
        'channel',
        'label',
        'description',
        'is_enabled',
        'config',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'config' => 'array',
    ];

    public static function isEnabled(string $key): bool
    {
        return self::where('key', $key)->value('is_enabled') ?? false;
    }

    public static function getConfig(string $key): ?array
    {
        return self::where('key', $key)->value('config');
    }

    public function getChannelIconAttribute(): string
    {
        return match ($this->channel) {
            'whatsapp' => 'ri-whatsapp-fill',
            'telegram' => 'ri-telegram-fill',
            'email' => 'ri-mail-fill',
            default => 'ri-notification-3-fill',
        };
    }

    public function getChannelColorAttribute(): string
    {
        return match ($this->channel) {
            'whatsapp' => 'green',
            'telegram' => 'blue',
            'email' => 'red',
            default => 'slate',
        };
    }
}
