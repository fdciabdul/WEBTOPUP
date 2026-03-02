<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\OrderService;
use App\Jobs\ProcessTopUpJob;
use App\Jobs\SendWhatsAppNotificationJob;
use App\Jobs\SendEmailNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        // Trash filter uses withTrashed
        $showTrash = $request->get('status') === 'trash';
        $query = $showTrash
            ? Transaction::onlyTrashed()->with(['user', 'product'])
            : Transaction::with(['user', 'product']);

        // Search: invoice, order_id, customer name, phone, product, IP, admin_note
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('admin_note', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status') && !in_array($request->status, ['all', 'trash'])) {
            $query->where('status', $request->status);
        }

        // Date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Time filter shortcut
        if ($request->filled('time_filter')) {
            switch ($request->time_filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->startOfMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', now()->startOfYear());
                    break;
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['created_at', 'total_amount', 'product_name', 'invoice_number'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->latest();
        }

        $perPage = $request->get('per_page', 10);
        $transactions = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Transaction::count(),
            'pending' => Transaction::where('status', 'pending')->count(),
            'paid' => Transaction::where('status', 'paid')->count(),
            'processing' => Transaction::where('status', 'processing')->count(),
            'completed' => Transaction::where('status', 'completed')->count(),
            'failed' => Transaction::where('status', 'failed')->count(),
            'cancelled' => Transaction::where('status', 'cancelled')->count(),
            'complain' => Transaction::where('status', 'complain')->count(),
            'trash' => Transaction::onlyTrashed()->count(),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'product']);
        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Full update from detail page (AJAX)
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:pending,paid,processing,completed,failed,cancelled,complain',
            'admin_note' => 'nullable|string',
            'cost_price' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
        ]);

        // Only update provided fields
        $data = array_filter($validated, fn($v) => $v !== null);
        $transaction->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diupdate',
                'transaction' => $transaction->fresh()
            ]);
        }

        return redirect()->route('admin.transactions.show', $transaction)
            ->with('success', 'Transaksi berhasil diupdate');
    }

    /**
     * Inline status update (AJAX)
     */
    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,completed,failed,cancelled,complain',
        ]);

        $oldStatus = $transaction->status;
        $transaction->update(['status' => $request->status]);

        // If marking as completed, set completed_at
        if ($request->status === 'completed' && !$transaction->completed_at) {
            $transaction->update(['completed_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => "Status diubah dari {$oldStatus} ke {$request->status}",
            'old_status' => $oldStatus,
            'new_status' => $request->status
        ]);
    }

    /**
     * Update admin note (AJAX)
     */
    public function updateNote(Request $request, Transaction $transaction)
    {
        $request->validate(['admin_note' => 'nullable|string']);
        $transaction->update(['admin_note' => $request->admin_note]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil disimpan'
        ]);
    }

    /**
     * Send order manually (Kirim Pesanan)
     */
    public function sendOrder(Request $request, Transaction $transaction)
    {
        $request->validate([
            'send_type' => 'required|in:akun,link,custom',
            'delivery_data' => 'required|array',
            'notify_wa' => 'boolean',
            'notify_email' => 'boolean',
            'note' => 'nullable|string',
        ]);

        // Save delivery data
        $transaction->update([
            'delivery_data' => $request->delivery_data,
            'status' => 'completed',
            'completed_at' => now(),
            'admin_note' => $request->note ?: $transaction->admin_note,
        ]);

        // Send notifications
        if ($request->input('notify_wa', true)) {
            try {
                SendWhatsAppNotificationJob::dispatch($transaction, 'order_delivered');
            } catch (\Exception $e) {
                Log::warning('WA notification failed: ' . $e->getMessage());
            }
        }

        if ($request->input('notify_email', true)) {
            try {
                SendEmailNotificationJob::dispatch($transaction, 'order_delivered');
            } catch (\Exception $e) {
                Log::warning('Email notification failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dikirim ke ' . $transaction->customer_name
        ]);
    }

    public function process(Transaction $transaction)
    {
        if (!in_array($transaction->status, ['paid', 'failed'])) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Transaksi tidak bisa diproses'], 422);
            }
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('error', 'Transaksi tidak bisa diproses');
        }

        ProcessTopUpJob::dispatch($transaction);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Proses top-up dimulai']);
        }

        return redirect()->route('admin.transactions.show', $transaction)
            ->with('success', 'Proses top-up dimulai');
    }

    public function refund(Transaction $transaction)
    {
        try {
            $this->orderService->refundOrder($transaction);

            return redirect()->route('admin.transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil direfund');
        } catch (\Exception $e) {
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('error', 'Refund gagal: ' . $e->getMessage());
        }
    }

    public function cancel(Transaction $transaction)
    {
        try {
            $this->orderService->cancelOrder($transaction);

            return redirect()->route('admin.transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil dibatalkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('error', 'Batal gagal: ' . $e->getMessage());
        }
    }

    public function resendNotification(Request $request, Transaction $transaction)
    {
        $type = $request->input('type', 'topup_success');

        SendWhatsAppNotificationJob::dispatch($transaction, $type);
        SendEmailNotificationJob::dispatch($transaction, $type);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notifikasi berhasil dikirim']);
        }

        return redirect()->route('admin.transactions.show', $transaction)
            ->with('success', 'Notifikasi berhasil dikirim');
    }

    /**
     * Process all paid transactions (batch)
     */
    public function processAll()
    {
        $paidTransactions = Transaction::where('status', 'paid')->get();

        if ($paidTransactions->isEmpty()) {
            return redirect()->route('admin.transactions.index')
                ->with('info', 'Tidak ada transaksi yang perlu diproses');
        }

        $count = 0;
        foreach ($paidTransactions as $transaction) {
            ProcessTopUpJob::dispatch($transaction);
            $count++;
        }

        return redirect()->route('admin.transactions.index')
            ->with('success', "{$count} transaksi sedang diproses");
    }

    /**
     * Export transactions to JSON
     */
    public function export(Request $request)
    {
        $query = Transaction::with(['user', 'product']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->get();

        $exportData = $transactions->map(function ($trx) {
            return [
                'invoice_number' => $trx->invoice_number,
                'order_id' => $trx->order_id,
                'customer_name' => $trx->customer_name,
                'customer_email' => $trx->customer_email,
                'customer_phone' => $trx->customer_phone,
                'product_name' => $trx->product_name,
                'category_name' => $trx->category_name,
                'product_price' => $trx->product_price,
                'admin_fee' => $trx->admin_fee,
                'discount' => $trx->discount,
                'total_amount' => $trx->total_amount,
                'cost_price' => $trx->cost_price,
                'payment_method' => $trx->payment_method,
                'status' => $trx->status,
                'admin_note' => $trx->admin_note,
                'ip_address' => $trx->ip_address,
                'paid_at' => $trx->paid_at?->toDateTimeString(),
                'completed_at' => $trx->completed_at?->toDateTimeString(),
                'created_at' => $trx->created_at->toDateTimeString(),
            ];
        });

        $filename = 'transactions_' . now()->format('Y-m-d_His') . '.json';

        return response()->json($exportData)
            ->header('Content-Disposition', "attachment; filename={$filename}")
            ->header('Content-Type', 'application/json');
    }

    /**
     * Import transactions from JSON
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json,txt|max:10240',
        ]);

        try {
            $content = file_get_contents($request->file('file')->getRealPath());
            $data = json_decode($content, true);

            if (!is_array($data)) {
                return back()->with('error', 'Format file tidak valid');
            }

            $imported = 0;
            foreach ($data as $row) {
                if (isset($row['invoice_number']) && !Transaction::where('invoice_number', $row['invoice_number'])->exists()) {
                    Transaction::create($row);
                    $imported++;
                }
            }

            return back()->with('success', "{$imported} transaksi berhasil diimport");
        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete (move to trash)
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transaksi dipindahkan ke trash']);
        }

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi dipindahkan ke trash');
    }

    /**
     * Restore from trash
     */
    public function restore($id)
    {
        $transaction = Transaction::onlyTrashed()->findOrFail($id);
        $transaction->restore();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dipulihkan']);
        }

        return redirect()->route('admin.transactions.index', ['status' => 'trash'])
            ->with('success', 'Transaksi berhasil dipulihkan');
    }

    /**
     * Permanently delete
     */
    public function forceDelete($id)
    {
        $transaction = Transaction::onlyTrashed()->findOrFail($id);
        $transaction->forceDelete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transaksi dihapus permanen']);
        }

        return redirect()->route('admin.transactions.index', ['status' => 'trash'])
            ->with('success', 'Transaksi dihapus permanen');
    }
}
