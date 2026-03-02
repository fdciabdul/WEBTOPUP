<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $provider;
    protected array $config;

    public function __construct()
    {
        $this->provider = config('whatsapp.provider', 'fonnte');
        $this->config = config("whatsapp.{$this->provider}");
    }

    public function send(string $phone, string $message): bool
    {
        try {
            $phone = $this->formatPhone($phone);

            if ($this->provider === 'fonnte') {
                return $this->sendViaFonnte($phone, $message);
            } elseif ($this->provider === 'wablas') {
                return $this->sendViaWablas($phone, $message);
            }

            throw new \Exception('Invalid WhatsApp provider');
        } catch (\Exception $e) {
            Log::error('WhatsApp Send Error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function sendViaFonnte(string $phone, string $message): bool
    {
        $response = Http::withHeaders([
            'Authorization' => $this->config['api_key'],
        ])->post($this->config['url'], [
            'target' => $phone,
            'message' => $message,
        ]);

        Log::info('WhatsApp Sent (Fonnte)', [
            'phone' => $phone,
            'response' => $response->json(),
        ]);

        return $response->successful();
    }

    protected function sendViaWablas(string $phone, string $message): bool
    {
        $url = "https://{$this->config['domain']}/api/send-message";

        $response = Http::withHeaders([
            'Authorization' => $this->config['api_key'],
        ])->post($url, [
            'phone' => $phone,
            'message' => $message,
        ]);

        Log::info('WhatsApp Sent (Wablas)', [
            'phone' => $phone,
            'response' => $response->json(),
        ]);

        return $response->successful();
    }

    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    public function sendOrderConfirmation(array $data): bool
    {
        $message = "🎉 *PESANAN DITERIMA*\n\n";
        $message .= "Order ID: {$data['order_id']}\n";
        $message .= "Produk: {$data['product_name']}\n";
        $message .= "Tujuan: {$data['customer_no']}\n";
        $message .= "Total: Rp " . number_format($data['total_amount'], 0, ',', '.') . "\n\n";
        $message .= "Silakan lakukan pembayaran untuk melanjutkan pesanan.\n\n";
        $message .= "Terima kasih! 🙏";

        return $this->send($data['phone'], $message);
    }

    public function sendPaymentSuccess(array $data): bool
    {
        $message = "✅ *PEMBAYARAN BERHASIL*\n\n";
        $message .= "Order ID: {$data['order_id']}\n";
        $message .= "Produk: {$data['product_name']}\n";
        $message .= "Total: Rp " . number_format($data['total_amount'], 0, ',', '.') . "\n\n";
        $message .= "Pesanan Anda sedang diproses. Mohon tunggu beberapa saat.\n\n";
        $message .= "Terima kasih! 🙏";

        return $this->send($data['phone'], $message);
    }

    public function sendTopUpSuccess(array $data): bool
    {
        $message = "🎊 *TOP UP BERHASIL*\n\n";
        $message .= "Order ID: {$data['order_id']}\n";
        $message .= "Produk: {$data['product_name']}\n";
        $message .= "Tujuan: {$data['customer_no']}\n\n";

        if (!empty($data['serial_number'])) {
            $message .= "Serial Number: {$data['serial_number']}\n\n";
        }

        $message .= "Status: SUKSES ✅\n";
        $message .= "Pesan: {$data['message']}\n\n";
        $message .= "Terima kasih telah berbelanja! 🙏";

        return $this->send($data['phone'], $message);
    }

    public function sendTopUpFailed(array $data): bool
    {
        $message = "⚠️ *TOP UP GAGAL*\n\n";
        $message .= "Order ID: {$data['order_id']}\n";
        $message .= "Produk: {$data['product_name']}\n";
        $message .= "Tujuan: {$data['customer_no']}\n\n";
        $message .= "Status: GAGAL ❌\n";
        $message .= "Alasan: {$data['message']}\n\n";
        $message .= "Dana akan dikembalikan ke saldo Anda dalam 1x24 jam.\n\n";
        $message .= "Mohon maaf atas ketidaknyamanannya. 🙏";

        return $this->send($data['phone'], $message);
    }

    public function sendOrderDelivered(array $data): bool
    {
        $message = "🚀 *PESANAN DIKIRIM*\n\n";
        $message .= "Order ID: {$data['order_id']}\n";
        $message .= "Produk: {$data['product_name']}\n\n";

        if (!empty($data['delivery_data']) && is_array($data['delivery_data'])) {
            $message .= "*Detail Akun:*\n";
            foreach ($data['delivery_data'] as $key => $val) {
                $label = ucfirst(str_replace('_', ' ', $key));
                $message .= "- {$label}: {$val}\n";
            }
            $message .= "\n";
        }

        $message .= "⚠️ _Dilarang mengubah Email & Password akun. Garansi hangus jika melanggar._\n\n";
        $message .= "Terima kasih telah berbelanja! 🙏";

        return $this->send($data['phone'], $message);
    }
}
