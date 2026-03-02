@extends('emails.layout')
@section('title', 'Pesanan Dibatalkan')

@section('content')
<!-- Header Card - Cancelled -->
<div class="card">
    <div class="bg-cancel">
        <img src="https://img.icons8.com/fluency/96/cancel.png" alt="Cancel" class="icon-hero">
        <h2 class="header-title">Pesanan Dibatalkan</h2>
        <p class="header-desc">Maaf, pesanan {{ $transaction->order_id }} telah dibatalkan.</p>
    </div>
</div>

<!-- Order Details -->
<div class="card">
    <div class="card-padding">
        <span class="section-label">Rincian Pesanan</span>
        <table class="data-table">
            <tr class="data-row">
                <td class="label">Nomor Invoice</td>
                <td class="value">{{ $transaction->invoice_number ?? $transaction->order_id }}</td>
            </tr>
            <tr class="data-row">
                <td class="label">Produk</td>
                <td class="value">{{ $transaction->product_name }}</td>
            </tr>
            <tr class="data-row">
                <td class="label">Total</td>
                <td class="highlight-price">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</div>

<!-- Refund Info -->
@if($transaction->is_refunded)
<div class="card">
    <div class="card-center">
        <div class="icon-circle circle-blue">
            <img src="https://img.icons8.com/fluency/48/receipt.png" class="widget-icon" alt="Refund">
        </div>
        <div class="widget-title">Dana Dikembalikan</div>
        <p class="widget-desc">Dana Anda telah dikembalikan ke saldo akun atau metode pembayaran Anda.</p>
    </div>
</div>
@endif

<!-- Help Widget -->
<div class="card">
    <div class="card-center">
        <div class="icon-circle circle-blue">
            <img src="https://img.icons8.com/fluency/48/customer-support.png" class="widget-icon" alt="Support">
        </div>
        <div class="widget-title">Butuh Bantuan?</div>
        <p class="widget-desc">Jika Anda memiliki pertanyaan, hubungi tim kami.</p>
        <a href="https://wa.me/6282210109289" class="btn-download">Chat Admin</a>
    </div>
</div>
@endsection
