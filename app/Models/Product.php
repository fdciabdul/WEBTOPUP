<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'icon',
        'variant_mode', // 'simple' or 'nested'
        'provider',
        'provider_code',
        'price_visitor',
        'price_reseller',
        'price_reseller_vip',
        'price_reseller_vvip',
        'provider_price',
        'is_unlimited_stock',
        'stock',
        'min_order',
        'max_order',
        'status',
        'total_sales',
        'is_featured',
        'is_best_seller',
        'meta_data',
        'input_fields',
        'notes',
    ];

    protected $casts = [
        'price_visitor' => 'decimal:2',
        'price_reseller' => 'decimal:2',
        'price_reseller_vip' => 'decimal:2',
        'price_reseller_vvip' => 'decimal:2',
        'provider_price' => 'decimal:2',
        'is_unlimited_stock' => 'boolean',
        'stock' => 'integer',
        'min_order' => 'integer',
        'max_order' => 'integer',
        'total_sales' => 'integer',
        'is_featured' => 'boolean',
        'is_best_seller' => 'boolean',
        'meta_data' => 'array',
        'input_fields' => 'array',
        'notes' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('group_name')->orderBy('sort_order');
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)
            ->where('is_active', true)
            ->orderBy('group_name')
            ->orderBy('sort_order');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getPriceByLevel(string $level): float
    {
        return match($level) {
            'reseller' => $this->price_reseller,
            'reseller_vip' => $this->price_reseller_vip,
            'reseller_vvip' => $this->price_reseller_vvip,
            default => $this->price_visitor,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->where('is_unlimited_stock', true)
              ->orWhere('stock', '>', 0);
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBestSeller($query)
    {
        return $query->where('is_best_seller', true);
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

    public function hasVariants(): bool
    {
        return $this->variants()->exists();
    }

    public function getVariantGroups(): array
    {
        if ($this->variant_mode !== 'nested') {
            return [];
        }

        return $this->variants()
            ->whereNotNull('group_name')
            ->distinct()
            ->pluck('group_name')
            ->toArray();
    }

    public function getVariantsByGroup(): array
    {
        $variants = $this->activeVariants;

        if ($this->variant_mode === 'nested') {
            return $variants->groupBy('group_name')->toArray();
        }

        return ['default' => $variants->toArray()];
    }

    public function getLowestPrice(): float
    {
        if ($this->hasVariants()) {
            return $this->variants()->min('price_visitor') ?? 0;
        }
        return $this->price_visitor;
    }

    public function getPriceRange(): array
    {
        if ($this->hasVariants()) {
            $min = $this->variants()->min('price_visitor') ?? 0;
            $max = $this->variants()->max('price_visitor') ?? 0;
            return ['min' => $min, 'max' => $max];
        }
        return ['min' => $this->price_visitor, 'max' => $this->price_visitor];
    }

    public function getIconUrlAttribute(): ?string
    {
        if ($this->icon) {
            if (str_starts_with($this->icon, 'http')) {
                return $this->icon;
            }
            return asset('storage/' . $this->icon);
        }
        return null;
    }
}
