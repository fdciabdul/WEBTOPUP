<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'group_name',
        'name',
        'provider_code',
        'price_visitor',
        'price_reseller',
        'price_vip',
        'price_vvip',
        'provider_price',
        'is_unlimited_stock',
        'stock',
        'download_link',
        'sort_order',
        'is_active',
        'total_sales',
    ];

    protected $casts = [
        'price_visitor' => 'decimal:2',
        'price_reseller' => 'decimal:2',
        'price_vip' => 'decimal:2',
        'price_vvip' => 'decimal:2',
        'provider_price' => 'decimal:2',
        'is_unlimited_stock' => 'boolean',
        'stock' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'total_sales' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getPriceByLevel(string $level): float
    {
        return match($level) {
            'reseller' => $this->price_reseller,
            'vip', 'reseller_vip' => $this->price_vip,
            'vvip', 'reseller_vvip' => $this->price_vvip,
            default => $this->price_visitor,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->where('is_unlimited_stock', true)
              ->orWhere('stock', '>', 0);
        });
    }

    public function scopeByGroup($query, ?string $groupName)
    {
        if ($groupName) {
            return $query->where('group_name', $groupName);
        }
        return $query->whereNull('group_name');
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('group_name')->orderBy('sort_order');
    }

    public function isInStock(): bool
    {
        return $this->is_unlimited_stock || $this->stock > 0;
    }

    public function decrementStock(int $quantity = 1): void
    {
        if (!$this->is_unlimited_stock) {
            $this->decrement('stock', $quantity);
        }
        $this->increment('total_sales', $quantity);
    }

    public function getFormattedPricesAttribute(): array
    {
        return [
            'visitor' => 'Rp ' . number_format($this->price_visitor, 0, ',', '.'),
            'reseller' => 'Rp ' . number_format($this->price_reseller, 0, ',', '.'),
            'vip' => 'Rp ' . number_format($this->price_vip, 0, ',', '.'),
            'vvip' => 'Rp ' . number_format($this->price_vvip, 0, ',', '.'),
        ];
    }
}
