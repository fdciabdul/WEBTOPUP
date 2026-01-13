<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonusFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'download_count',
        'required_level',
        'is_active',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForLevel($query, string $level)
    {
        $levels = ['visitor', 'reseller', 'reseller_vip', 'reseller_vvip'];
        $index = array_search($level, $levels);
        
        return $query->whereIn('required_level', array_slice($levels, 0, $index + 1));
    }

    public function incrementDownload(): void
    {
        $this->increment('download_count');
    }
}
