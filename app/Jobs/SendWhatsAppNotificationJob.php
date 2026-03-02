<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotificationJob implements ShouldQueue
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

    public function handle(WhatsAppService $whatsAppService): void
    {
        try {
            $data = [
                'order_id' => $this->transaction->order_id,
                'product_name' => $this->transaction->product_name,
                'customer_no' => $this->transaction->order_data['customer_no'] ?? '',
                'total_amount' => $this->transaction->total_amount,
                'phone' => $this->transaction->customer_phone,
            ];

            $sent = match($this->type) {
                'order_confirmation' => $whatsAppService->sendOrderConfirmation($data),
                'payment_success' => $whatsAppService->sendPaymentSuccess($data),
                'topup_success' => $whatsAppService->sendTopUpSuccess(array_merge($data, [
                    'serial_number' => $this->transaction->result_data['serial_number'] ?? '',
                    'message' => $this->transaction->result_data['message'] ?? 'Success',
                ])),
                'topup_failed' => $whatsAppService->sendTopUpFailed(array_merge($data, [
                    'message' => $this->transaction->result_data['message'] ?? 'Failed',
                ])),
                'order_delivered' => $whatsAppService->sendOrderDelivered(array_merge($data, [
                    'delivery_data' => $this->transaction->delivery_data ?? [],
                ])),
                default => false,
            };

            if ($sent) {
                Log::info('WhatsApp notification sent', [
                    'order_id' => $this->transaction->order_id,
                    'type' => $this->type,
                ]);
            } else {
                Log::warning('WhatsApp notification failed', [
                    'order_id' => $this->transaction->order_id,
                    'type' => $this->type,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp notification error', [
                'order_id' => $this->transaction->order_id,
                'type' => $this->type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
