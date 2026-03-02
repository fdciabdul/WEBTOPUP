<?php

namespace App\Http\Controllers;

use App\Services\MVStoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected MVStoreService $mvStoreService;

    public function __construct(MVStoreService $mvStoreService)
    {
        $this->mvStoreService = $mvStoreService;
    }

    /**
     * Pre-validate user account (AJAX)
     */
    public function validateAccount(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
            'server_id' => 'nullable|string',
            'game_code' => 'required|string',
        ]);

        try {
            $userId = $request->input('user_id');
            $serverId = $request->input('server_id', '');
            $gameCode = $request->input('game_code');

            $response = $this->mvStoreService->checkAccount($userId, $serverId, $gameCode);

            if ($response['success']) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'nickname' => $response['nickname'],
                        'region' => $response['region'] ?? '',
                        'userId' => $response['userId'] ?? $userId,
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $response['error'] ?? 'Akun tidak ditemukan',
            ]);
        } catch (\Exception $e) {
            Log::error('Account validation error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Gagal memvalidasi akun. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Create order (guest checkout)
     */
    public function create(Request $request)
    {
        $request->validate([
            'product_code' => 'required|string',
            'item_sku' => 'required|string',
            'item_price' => 'required|numeric',
            'item_name' => 'required|string',
            'payment_code' => 'required|string',
            'user_id' => 'required|string',
            'server_id' => 'nullable|string',
            'nickname' => 'required|string',
        ]);

        try {
            // Combine user_id and server_id for game_id
            $userId = $request->input('user_id');
            $serverId = $request->input('server_id', '');
            $gameId = $serverId ? "{$userId}({$serverId})" : $userId;

            $orderData = [
                'game_id' => $gameId,
                'nickname' => $request->input('nickname'),
                'item_sku' => $request->input('item_sku'),
                'item_price' => $request->input('item_price'),
                'payment_code' => $request->input('payment_code'),
                'product_code' => $request->input('product_code'),
                'email' => $request->input('email', ''),
            ];

            Log::info('Creating MVStore order', $orderData);

            // Create order via MVStore API
            $orderResponse = $this->mvStoreService->createOrder($orderData);

            Log::info('MVStore order response', ['response' => $orderResponse]);

            if (!$orderResponse['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $orderResponse['error'] ?? 'Gagal membuat pesanan',
                ]);
            }

            $invoiceData = $orderResponse['data'] ?? [];
            $invoiceNumber = $orderResponse['invoice'] ?? $invoiceData['invoice'] ?? null;

            // Store order data in session
            $orderSession = [
                'invoice_data' => $invoiceData,
                'order_data' => [
                    'product_code' => $request->input('product_code'),
                    'item_name' => $request->input('item_name'),
                    'item_price' => $request->input('item_price'),
                    'game_id' => $gameId,
                    'nickname' => $request->input('nickname'),
                    'payment_code' => $request->input('payment_code'),
                ],
                'invoice_number' => $invoiceNumber,
                'created_at' => now()->toDateTimeString(),
            ];

            session(['pending_order' => $orderSession]);

            return response()->json([
                'success' => true,
                'redirect' => route('order.payment'),
            ]);

        } catch (\Exception $e) {
            Log::error('Order creation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan server. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Show payment page
     */
    public function payment()
    {
        $orderSession = session('pending_order');

        if (!$orderSession) {
            return redirect()->route('home')->with('error', 'Tidak ada pesanan yang menunggu pembayaran');
        }

        $invoiceData = $orderSession['invoice_data'] ?? [];
        $orderData = $orderSession['order_data'] ?? [];
        $invoiceNumber = $orderSession['invoice_number'] ?? null;

        // If we have invoice number, fetch latest invoice data
        if ($invoiceNumber) {
            $freshInvoiceData = $this->mvStoreService->checkInvoice($invoiceNumber);
            if ($freshInvoiceData) {
                $invoiceData = $freshInvoiceData;
            }
        }

        // Generate QR code URL from base64QrCode (QRIS string)
        $qrCodeUrl = null;
        $qrisData = $invoiceData['base64QrCode'] ?? null;
        if ($qrisData) {
            $qrCodeUrl = $this->mvStoreService->generateQRCodeUrl($qrisData);
        }

        return view('mv-payment', compact(
            'invoiceData',
            'orderData',
            'invoiceNumber',
            'qrCodeUrl'
        ));
    }

    /**
     * Check order status
     */
    public function status(Request $request)
    {
        $invoiceNumber = $request->query('invoice');

        if (!$invoiceNumber) {
            return redirect()->route('track.order');
        }

        try {
            $invoiceData = $this->mvStoreService->checkInvoice($invoiceNumber);

            if (empty($invoiceData)) {
                return redirect()->route('track.order')->with('error', 'Invoice tidak ditemukan');
            }

            // Generate QR code URL if payment is pending
            $qrCodeUrl = null;
            $qrisData = $invoiceData['base64QrCode'] ?? null;
            $paymentStatus = $invoiceData['statusPayment'] ?? $invoiceData['status_payment'] ?? '';

            if ($qrisData && strtolower($paymentStatus) === 'pending') {
                $qrCodeUrl = $this->mvStoreService->generateQRCodeUrl($qrisData);
            }

            // Clear pending order session since we're viewing status
            session()->forget('pending_order');

            return view('mv-order-status', compact('invoiceData', 'invoiceNumber', 'qrCodeUrl'));

        } catch (\Exception $e) {
            Log::error('Invoice check error', [
                'invoice' => $invoiceNumber,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('track.order')->with('error', 'Gagal memeriksa status pesanan');
        }
    }

    /**
     * Track order by invoice number (AJAX)
     */
    public function track(Request $request)
    {
        $request->validate([
            'invoice' => 'required|string',
        ]);

        $invoiceNumber = $request->input('invoice');

        try {
            $invoiceData = $this->mvStoreService->checkInvoice($invoiceNumber);

            if (empty($invoiceData)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invoice tidak ditemukan',
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $invoiceData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal memeriksa status pesanan',
            ]);
        }
    }

    /**
     * Refresh payment status (AJAX)
     */
    public function refreshStatus(Request $request)
    {
        $request->validate([
            'invoice' => 'required|string',
        ]);

        $invoiceNumber = $request->input('invoice');

        try {
            $invoiceData = $this->mvStoreService->checkInvoice($invoiceNumber);

            if (empty($invoiceData)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invoice tidak ditemukan',
                ]);
            }

            $status = $invoiceData['statusPayment'] ?? $invoiceData['status_payment'] ?? 'pending';
            $orderStatus = $invoiceData['statusOrder'] ?? $invoiceData['status_order'] ?? 'pending';

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_status' => $status,
                    'order_status' => $orderStatus,
                    'invoice_data' => $invoiceData,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal memeriksa status',
            ]);
        }
    }
}
