<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'required|string',
            'customer_no' => 'required|string',
            'quantity' => 'nullable|integer|min:1',
            'payment_method' => 'required|in:balance,midtrans',
        ]);

        try {
            $product = Product::findOrFail($validated['product_id']);

            $transaction = $this->orderService->createOrder([
                'product_id' => $validated['product_id'],
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'order_data' => [
                    'customer_no' => $validated['customer_no'],
                ],
                'quantity' => $validated['quantity'] ?? 1,
                'payment_method' => $validated['payment_method'],
            ]);

            $paymentResult = $this->orderService->processPayment($transaction);

            if ($validated['payment_method'] === 'balance') {
                return redirect()->route('dashboard.transactions')
                    ->with('success', 'Order placed successfully! Your top-up is being processed.');
            } else {
                return view('payment', [
                    'transaction' => $transaction,
                    'snap_token' => $paymentResult['snap_token'] ?? null,
                    'redirect_url' => $paymentResult['redirect_url'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function payment($orderId)
    {
        $transaction = \App\Models\Transaction::where('order_id', $orderId)->firstOrFail();

        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$transaction->isPending()) {
            return redirect()->route('dashboard.transactions')
                ->with('info', 'This transaction is no longer pending.');
        }

        return view('payment', compact('transaction'));
    }
}
