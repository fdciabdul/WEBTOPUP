<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'action',
        'module',
        'description',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, string $module, string $description, array $oldData = null, array $newData = null, string $type = 'info'): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'type' => $type,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'success' => 'emerald',
            'warning' => 'amber',
            'error' => 'red',
            default => 'blue',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'login' => 'ri-login-box-line',
            'logout' => 'ri-logout-box-line',
            'create' => 'ri-add-circle-line',
            'update' => 'ri-edit-line',
            'delete' => 'ri-delete-bin-line',
            'sync' => 'ri-refresh-line',
            default => 'ri-information-line',
        };
    }
}
