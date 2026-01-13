<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public function sendOrderConfirmation(array $data): bool
    {
        try {
            Mail::send('emails.order-confirmation', $data, function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Pesanan Diterima - ' . $data['order_id']);
            });

            Log::info('Email sent: Order Confirmation', ['email' => $data['email']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Email Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendPaymentSuccess(array $data): bool
    {
        try {
            Mail::send('emails.payment-success', $data, function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Pembayaran Berhasil - ' . $data['order_id']);
            });

            Log::info('Email sent: Payment Success', ['email' => $data['email']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Email Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendTopUpSuccess(array $data): bool
    {
        try {
            Mail::send('emails.topup-success', $data, function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Top Up Berhasil - ' . $data['order_id']);
            });

            Log::info('Email sent: TopUp Success', ['email' => $data['email']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Email Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendTopUpFailed(array $data): bool
    {
        try {
            Mail::send('emails.topup-failed', $data, function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Top Up Gagal - ' . $data['order_id']);
            });

            Log::info('Email sent: TopUp Failed', ['email' => $data['email']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Email Error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
