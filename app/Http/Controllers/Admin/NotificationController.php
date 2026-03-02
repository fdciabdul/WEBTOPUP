<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use App\Models\Transaction;
use App\Models\ActivityLog;
use App\Services\EmailService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = NotificationSetting::orderBy('channel')->get();
        $grouped = $notifications->groupBy('channel');

        return view('admin.notifications.index', compact('notifications', 'grouped'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'notifications' => 'required|array',
            'notifications.*.id' => 'required|exists:notification_settings,id',
            'notifications.*.is_enabled' => 'nullable|boolean',
            'notifications.*.message_template' => 'nullable|string|max:2000',
        ]);

        foreach ($validated['notifications'] as $data) {
            $updateData = [
                'is_enabled' => isset($data['is_enabled']) && $data['is_enabled'],
            ];

            if (isset($data['message_template'])) {
                $notification = NotificationSetting::find($data['id']);
                $config = $notification->config ?? [];
                $config['message_template'] = $data['message_template'];
                $updateData['config'] = json_encode($config);
            }

            NotificationSetting::where('id', $data['id'])->update($updateData);
        }

        ActivityLog::log('update', 'notifications', 'Mengubah pengaturan notifikasi', null, null, 'success');

        return back()->with('success', 'Pengaturan notifikasi berhasil disimpan');
    }

    public function test(Request $request, NotificationSetting $notification)
    {
        $request->validate([
            'test_email' => 'nullable|email',
            'test_phone' => 'nullable|string',
            'test_type' => 'nullable|string|in:order_confirmation,payment_success,topup_success,topup_failed,order_delivered',
        ]);

        $testEmail = $request->input('test_email', 'ceo@marspedia.id');
        $testPhone = $request->input('test_phone', '6282210109289');
        $testType = $request->input('test_type', 'topup_success');

        // Build fake transaction for testing
        $fakeTransaction = new Transaction([
            'order_id' => 'TEST-' . date('Ymd') . '-' . strtoupper(substr(md5(time()), 0, 6)),
            'invoice_number' => 'INV/TEST/' . date('Y/m') . '/001',
            'customer_name' => 'Test Customer',
            'customer_email' => $testEmail,
            'customer_phone' => $testPhone,
            'product_name' => 'Netflix Premium 4K (1 Bulan)',
            'category_name' => 'Streaming',
            'product_price' => 55000,
            'admin_fee' => 0,
            'discount' => 5000,
            'total_amount' => 50000,
            'quantity' => 1,
            'payment_method' => 'balance',
            'status' => 'completed',
            'order_data' => ['customer_no' => 'test@example.com'],
            'result_data' => [
                'serial_number' => 'SN-TEST-' . strtoupper(substr(md5(time()), 0, 8)),
                'message' => 'Top up berhasil diproses',
            ],
            'delivery_data' => [
                'Email' => 'testuser@gmail.com',
                'Password' => 'Mars123!',
                'PIN' => '2291',
                'Profile' => 'Profile 3',
            ],
            'paid_at' => now(),
            'completed_at' => now(),
            'is_refunded' => false,
        ]);

        $results = [];

        try {
            if ($notification->channel === 'email' || $request->has('test_email')) {
                $emailService = app(EmailService::class);
                $data = [
                    'order_id' => $fakeTransaction->order_id,
                    'invoice_number' => $fakeTransaction->invoice_number,
                    'product_name' => $fakeTransaction->product_name,
                    'customer_name' => $fakeTransaction->customer_name,
                    'customer_no' => $fakeTransaction->order_data['customer_no'] ?? '',
                    'total_amount' => $fakeTransaction->total_amount,
                    'email' => $testEmail,
                    'transaction' => $fakeTransaction,
                ];

                $sent = match($testType) {
                    'order_confirmation' => $emailService->sendOrderConfirmation($data),
                    'payment_success' => $emailService->sendPaymentSuccess($data),
                    'topup_success' => $emailService->sendTopUpSuccess(array_merge($data, [
                        'serial_number' => $fakeTransaction->result_data['serial_number'] ?? '',
                        'message' => $fakeTransaction->result_data['message'] ?? 'Success',
                    ])),
                    'topup_failed' => $emailService->sendTopUpFailed(array_merge($data, [
                        'message' => 'Produk sedang tidak tersedia (test)',
                    ])),
                    'order_delivered' => $emailService->sendOrderDelivered(array_merge($data, [
                        'delivery_data' => $fakeTransaction->delivery_data ?? [],
                    ])),
                    default => false,
                };

                $results['email'] = $sent ? 'sent' : 'failed';
            }

            if ($notification->channel === 'whatsapp' || $request->has('test_phone')) {
                $waService = app(WhatsAppService::class);
                $data = [
                    'order_id' => $fakeTransaction->order_id,
                    'product_name' => $fakeTransaction->product_name,
                    'customer_no' => $fakeTransaction->order_data['customer_no'] ?? '',
                    'total_amount' => $fakeTransaction->total_amount,
                    'phone' => $testPhone,
                ];

                $sent = match($testType) {
                    'order_confirmation' => $waService->sendOrderConfirmation($data),
                    'payment_success' => $waService->sendPaymentSuccess($data),
                    'topup_success' => $waService->sendTopUpSuccess(array_merge($data, [
                        'serial_number' => $fakeTransaction->result_data['serial_number'] ?? '',
                        'message' => $fakeTransaction->result_data['message'] ?? 'Success',
                    ])),
                    'topup_failed' => $waService->sendTopUpFailed(array_merge($data, [
                        'message' => 'Produk sedang tidak tersedia (test)',
                    ])),
                    'order_delivered' => $waService->sendOrderDelivered(array_merge($data, [
                        'delivery_data' => $fakeTransaction->delivery_data ?? [],
                    ])),
                    default => false,
                };

                $results['whatsapp'] = $sent ? 'sent' : 'failed';
            }
        } catch (\Exception $e) {
            Log::error('Test notification error', ['error' => $e->getMessage()]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Test gagal: ' . $e->getMessage());
        }

        ActivityLog::log('test', 'notifications', "Test notifikasi ({$testType}): " . json_encode($results), null, null, 'info');

        $message = 'Test notifikasi dikirim';
        if (!empty($results['email'])) {
            $message .= " | Email: {$results['email']} ({$testEmail})";
        }
        if (!empty($results['whatsapp'])) {
            $message .= " | WA: {$results['whatsapp']} ({$testPhone})";
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'results' => $results,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Quick test: send all notification types to a specific email
     */
    public function testAll(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'nullable|string',
        ]);

        $types = ['order_confirmation', 'payment_success', 'topup_success', 'topup_failed', 'order_delivered'];
        $results = [];

        // Create a dummy NotificationSetting for testing
        $dummyNotification = new NotificationSetting([
            'channel' => $request->has('phone') ? 'whatsapp' : 'email',
        ]);

        foreach ($types as $type) {
            $testRequest = new Request([
                'test_email' => $request->email,
                'test_phone' => $request->phone,
                'test_type' => $type,
            ]);

            try {
                $this->test($testRequest, $dummyNotification);
                $results[$type] = 'sent';
            } catch (\Exception $e) {
                $results[$type] = 'failed: ' . $e->getMessage();
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Test semua notifikasi selesai',
                'results' => $results,
            ]);
        }

        return back()->with('success', 'Test semua notifikasi selesai: ' . json_encode($results));
    }
}
