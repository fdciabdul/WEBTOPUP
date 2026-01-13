<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'product_id',
        'product_name',
        'category_name',
        'order_data',
        'quantity',
        'product_price',
        'admin_fee',
        'discount',
        'total_amount',
        'payment_method',
        'payment_channel',
        'payment_reference',
        'payment_expired_at',
        'paid_at',
        'status',
        'provider_order_id',
        'provider_status',
        'provider_response',
        'result_data',
        'delivery_data',
        'completed_at',
        'admin_note',
        'customer_note',
        'is_refunded',
        'refund_amount',
        'refund_id',
        'refunded_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'order_data' => 'array',
        'quantity' => 'integer',
        'product_price' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'provider_response' => 'array',
        'result_data' => 'array',
        'delivery_data' => 'array',
        'completed_at' => 'datetime',
        'is_refunded' => 'boolean',
        'refund_amount' => 'decimal:2',
        'refunded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pending'])
            ->where('payment_expired_at', '<', now());
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'processing', 'completed']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeRefunded(): bool
    {
        return !$this->is_refunded &&
               in_array($this->status, ['paid', 'failed', 'cancelled']);
    }
}
