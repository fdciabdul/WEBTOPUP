<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogActivity extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'event',
        'ip_address',
        'country_code',
        'country_name',
        'user_agent',
        'browser',
        'device',
        'platform',
        'url',
        'description',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $event, ?string $description = null, ?array $data = null): void
    {
        $request = request();
        
        self::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'description' => $description,
            'data' => $data,
        ]);
    }
}
