<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'pin',
        'phone',
        'role',
        'level',
        'balance',
        'total_transactions',
        'total_spending',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'pin',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'total_transactions' => 'integer',
            'total_spending' => 'decimal:2',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function balanceHistories(): HasMany
    {
        return $this->hasMany(BalanceHistory::class);
    }

    public function logActivities(): HasMany
    {
        return $this->hasMany(LogActivity::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function addBalance(float $amount, ?string $referenceType = null, ?int $referenceId = null, ?string $description = null): void
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);
        $this->refresh();

        BalanceHistory::create([
            'user_id' => $this->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'description' => $description,
            'created_by' => auth()->id(),
        ]);
    }

    public function deductBalance(float $amount, ?string $referenceType = null, ?int $referenceId = null, ?string $description = null): void
    {
        $balanceBefore = $this->balance;
        $this->decrement('balance', $amount);
        $this->refresh();

        BalanceHistory::create([
            'user_id' => $this->id,
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'description' => $description,
            'created_by' => auth()->id(),
        ]);
    }

    public function hasBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }
}
