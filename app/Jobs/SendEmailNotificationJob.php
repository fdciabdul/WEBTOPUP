<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    protected Transaction $transaction;
    protected string $type;

    public function __construct(Transaction $transaction, string $type)
    {
        $this->transaction = $transaction;
        $this->type = $type;
    }

    public function handle(EmailService $emailService): void
    {
        if (!$this->transaction->customer_email) {
            return;
        }

        try {
            $data = [
                'order_id' => $this->transaction->order_id,
                'invoice_number' => $this->transaction->invoice_number,
                'product_name' => $this->transaction->product_name,
                'customer_name' => $this->transaction->customer_name,
                'customer_no' => $this->transaction->order_data['customer_no'] ?? '',
                'total_amount' => $this->transaction->total_amount,
                'email' => $this->transaction->customer_email,
                'created_at' => $this->transaction->created_at,
                'transaction' => $this->transaction,
            ];

            $sent = match($this->type) {
                'order_confirmation' => $emailService->sendOrderConfirmation($data),
                'payment_success' => $emailService->sendPaymentSuccess($data),
                'topup_success' => $emailService->sendTopUpSuccess(array_merge($data, [
                    'serial_number' => $this->transaction->result_data['serial_number'] ?? '',
                    'message' => $this->transaction->result_data['message'] ?? 'Success',
                    'completed_at' => $this->transaction->completed_at,
                ])),
                'topup_failed' => $emailService->sendTopUpFailed(array_merge($data, [
                    'message' => $this->transaction->result_data['message'] ?? 'Failed',
                ])),
                'order_delivered' => $emailService->sendOrderDelivered(array_merge($data, [
                    'delivery_data' => $this->transaction->delivery_data ?? [],
                ])),
                default => false,
            };

            if ($sent) {
                Log::info('Email notification sent', [
                    'order_id' => $this->transaction->order_id,
                    'type' => $this->type,
                ]);
            } else {
                Log::warning('Email notification failed', [
                    'order_id' => $this->transaction->order_id,
                    'type' => $this->type,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Email notification error', [
                'order_id' => $this->transaction->order_id,
                'type' => $this->type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
