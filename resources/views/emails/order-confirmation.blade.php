@extends('emails.layout')
@section('title', 'Menunggu Pembayaran')

@section('content')
<!-- Header Card - Pending -->
<div class="card">
    <div class="bg-pending">
        <img src="https://img.icons8.com/fluency/96/purchase-order.png" alt="Pending" class="icon-hero">
        <h2 class="header-title">Menunggu Pembayaran</h2>
        <p class="header-desc">Segera lunasi tagihan {{ $transaction->order_id }} sebelum expired.</p>
        @if($transaction->status === 'pending' && $transaction->payment_url)
            <a href="{{ $transaction->payment_url }}" class="btn btn-white" style="color: #FF8008;">Bayar Sekarang</a>
        @endif
    </div>
</div>

<!-- Order Details -->
<div class="card">
    <div class="card-padding">
        <span class="section-label">Rincian Tagihan</span>
        <table class="data-table">
            <tr class="data-row">
                <td class="label">Nomor Invoice</td>
                <td class="value">{{ $transaction->invoice_number ?? $transaction->order_id }}</td>
            </tr>
            <tr class="data-row">
                <td class="label">Produk</td>
                <td class="value">{{ $transaction->product_name }}</td>
            </tr>
            @if($transaction->order_data)
            <tr class="data-row">
                <td class="label">Tujuan</td>
                <td class="value">{{ $transaction->order_data['customer_no'] ?? ($transaction->order_data['user_id'] ?? '-') }}</td>
            </tr>
            @endif
            <tr class="data-row">
                <td class="label">Metode Pembayaran</td>
                <td class="value">{{ ucfirst($transaction->payment_method) }}</td>
            </tr>
            @if($transaction->discount > 0)
            <tr class="data-row">
                <td class="label">Diskon</td>
                <td class="value" style="color: #34C759;">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="data-row">
                <td class="label">Total Bayar</td>
                <td class="highlight-price">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</div>

<!-- Buyer Data -->
<div class="card">
    <div class="card-padding">
        <span class="section-label">Data Pembeli</span>
        <table class="data-table">
            <tr class="data-row">
                <td class="label">Nama</td>
                <td class="value">{{ $transaction->customer_name }}</td>
            </tr>
            @if($transaction->customer_phone)
            <tr class="data-row">
                <td class="label">WhatsApp</td>
                <td class="value">{{ $transaction->customer_phone }}</td>
            </tr>
            @endif
            <tr class="data-row">
                <td class="label">Email</td>
                <td class="value">{{ $transaction->customer_email }}</td>
            </tr>
        </table>
    </div>
</div>

<!-- Help Widget -->
<div class="card">
    <div class="card-center">
        <div class="icon-circle circle-blue">
            <img src="https://img.icons8.com/fluency/48/customer-support.png" class="widget-icon" alt="Support">
        </div>
        <div class="widget-title">Butuh Bantuan?</div>
        <p class="widget-desc">Tim kami siap membantu Anda 24/7.</p>
        <a href="https://wa.me/6282210109289" class="btn-download">Chat Admin</a>
    </div>
</div>
@endsection
