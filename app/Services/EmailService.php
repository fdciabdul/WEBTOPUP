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

    public function sendOrderDelivered(array $data): bool
    {
        try {
            Mail::send('emails.order-delivered', $data, function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Pesanan Dikirim - ' . $data['order_id']);
            });

            Log::info('Email sent: Order Delivered', ['email' => $data['email']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Email Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendWelcome(array $data): bool
    {
        try {
            Mail::send('emails.welcome', $data, function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Selamat Datang di ' . config('app.name', 'Marspedia'));
            });

            Log::info('Email sent: Welcome', ['email' => $data['email']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Email Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendOtpVerification(array $data): bool
    {
        try {
            Mail::send('emails.otp-verification', $data, function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Kode Verifikasi - ' . config('app.name', 'Marspedia'));
            });

            Log::info('Email sent: OTP Verification', ['email' => $data['email']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Email Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendResetPassword(array $data): bool
    {
        try {
            Mail::send('emails.reset-password', $data, function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Reset Password - ' . config('app.name', 'Marspedia'));
            });

            Log::info('Email sent: Reset Password', ['email' => $data['email']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Email Error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
