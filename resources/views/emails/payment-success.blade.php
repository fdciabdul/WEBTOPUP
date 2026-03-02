@extends('emails.layout')
@section('title', 'Pembayaran Berhasil')

@section('content')
<!-- Header Card - Payment Success -->
<div class="card">
    <div class="bg-success">
        <img src="https://img.icons8.com/fluency/96/ok.png" alt="Success" class="icon-hero">
        <h2 class="header-title">Pembayaran Berhasil</h2>
        <p class="header-desc">Dana diterima. Pesanan {{ $transaction->order_id }} sedang diproses.</p>
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
            <tr class="data-row">
                <td class="label">Metode Pembayaran</td>
                <td class="value">{{ ucfirst($transaction->payment_method) }}</td>
            </tr>
            <tr class="data-row">
                <td class="label">Waktu Pembayaran</td>
                <td class="value">{{ $transaction->paid_at ? $transaction->paid_at->format('d M Y H:i') : now()->format('d M Y H:i') }}</td>
            </tr>
            <tr class="data-row">
                <td class="label">Total Bayar</td>
                <td class="highlight-price">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</div>

<!-- Info Widget -->
<div class="card">
    <div class="card-center">
        <div class="icon-circle circle-blue">
            <img src="https://img.icons8.com/fluency/48/receipt.png" class="widget-icon" alt="Receipt">
        </div>
        <div class="widget-title">Pesanan Sedang Diproses</div>
        <p class="widget-desc">Pesanan Anda sedang antri untuk diproses. Anda akan menerima notifikasi lagi ketika pesanan selesai.</p>
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
