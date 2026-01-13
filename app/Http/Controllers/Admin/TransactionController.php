<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\OrderService;
use App\Jobs\ProcessTopUpJob;
use App\Jobs\SendWhatsAppNotificationJob;
use App\Jobs\SendEmailNotificationJob;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'product']);

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_id', 'like', '%' . $request->search . '%')
                  ->orWhere('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(20);

        $stats = [
            'total' => Transaction::count(),
            'pending' => Transaction::where('status', 'pending')->count(),
            'paid' => Transaction::where('status', 'paid')->count(),
            'processing' => Transaction::where('status', 'processing')->count(),
            'completed' => Transaction::where('status', 'completed')->count(),
            'failed' => Transaction::where('status', 'failed')->count(),
            'cancelled' => Transaction::where('status', 'cancelled')->count(),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'product']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,processing,completed,failed,cancelled',
            'admin_note' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('admin.transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully');
    }

    public function process(Transaction $transaction)
    {
        if (!$transaction->isPaid()) {
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('error', 'Transaction is not paid yet');
        }

        ProcessTopUpJob::dispatch($transaction);

        return redirect()->route('admin.transactions.show', $transaction)
            ->with('success', 'Top-up processing started');
    }

    public function refund(Transaction $transaction)
    {
        try {
            $this->orderService->refundOrder($transaction);

            return redirect()->route('admin.transactions.show', $transaction)
                ->with('success', 'Transaction refunded successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }

    public function cancel(Transaction $transaction)
    {
        try {
            $this->orderService->cancelOrder($transaction);

            return redirect()->route('admin.transactions.show', $transaction)
                ->with('success', 'Transaction cancelled successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('error', 'Cancel failed: ' . $e->getMessage());
        }
    }

    public function resendNotification(Request $request, Transaction $transaction)
    {
        $type = $request->input('type', 'topup_success');

        SendWhatsAppNotificationJob::dispatch($transaction, $type);
        SendEmailNotificationJob::dispatch($transaction, $type);

        return redirect()->route('admin.transactions.show', $transaction)
            ->with('success', 'Notification sent');
    }
}
