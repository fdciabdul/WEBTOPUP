<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('track-order');
    }

    public function check(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'phone' => 'required|string',
        ]);

        $transaction = Transaction::where('order_id', $request->order_id)
            ->where('customer_phone', $request->phone)
            ->with('product')
            ->first();

        if (!$transaction) {
            return back()->with('error', 'Order not found. Please check your Order ID and phone number.');
        }

        return view('order-status', compact('transaction'));
    }
}
