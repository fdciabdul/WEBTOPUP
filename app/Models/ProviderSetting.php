<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ProviderSetting extends Model
{
    protected $fillable = [
        'provider_type',
        'provider_name',
        'is_active',
        'is_default',
        'credentials',
        'config',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'credentials' => 'array',
        'config' => 'array',
        'priority' => 'integer',
    ];

    // Encrypt credentials when setting
    public function setCredentialsAttribute($value)
    {
        if (is_array($value)) {
            $encrypted = [];
            foreach ($value as $key => $val) {
                $encrypted[$key] = Crypt::encryptString($val);
            }
            $this->attributes['credentials'] = json_encode($encrypted);
        }
    }

    // Decrypt credentials when getting
    public function getCredentialsAttribute($value)
    {
        if (!$value) return [];
        
        $data = json_decode($value, true);
        $decrypted = [];
        
        foreach ($data as $key => $val) {
            try {
                $decrypted[$key] = Crypt::decryptString($val);
            } catch (\Exception $e) {
                $decrypted[$key] = null;
            }
        }
        
        return $decrypted;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTopup($query)
    {
        return $query->where('provider_type', 'topup');
    }

    public function scopePayment($query)
    {
        return $query->where('provider_type', 'payment');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Get default provider for type
    public static function getDefaultProvider(string $type)
    {
        return self::where('provider_type', $type)
            ->active()
            ->default()
            ->orderBy('priority', 'desc')
            ->first();
    }

    // Get active providers by type
    public static function getActiveProviders(string $type)
    {
        return self::where('provider_type', $type)
            ->active()
            ->orderBy('priority', 'desc')
            ->get();
    }
}
