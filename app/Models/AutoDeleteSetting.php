<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoDeleteSetting extends Model
{
    protected $fillable = [
        'key',
        'label',
        'description',
        'is_enabled',
        'days',
        'last_run_at',
        'last_deleted_count',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'days' => 'integer',
        'last_run_at' => 'datetime',
        'last_deleted_count' => 'integer',
    ];

    public static function isEnabled(string $key): bool
    {
        return self::where('key', $key)->value('is_enabled') ?? false;
    }

    public static function getDays(string $key): int
    {
        return self::where('key', $key)->value('days') ?? 30;
    }
}
