@extends('emails.layout')
@section('title', 'Pesanan Gagal')

@section('content')
<!-- Header Card - Cancel -->
<div class="card">
    <div class="bg-cancel">
        <img src="https://img.icons8.com/fluency/96/cancel.png" alt="Failed" class="icon-hero">
        <h2 class="header-title">Pesanan Gagal</h2>
        <p class="header-desc">Maaf, pesanan {{ $transaction->order_id }} tidak dapat diproses.</p>
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
            @if($transaction->order_data)
            <tr class="data-row">
                <td class="label">Tujuan</td>
                <td class="value">{{ $transaction->order_data['customer_no'] ?? ($transaction->order_data['user_id'] ?? '-') }}</td>
            </tr>
            @endif
            <tr class="data-row">
                <td class="label">Total Bayar</td>
                <td class="highlight-price">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</div>

<!-- Error Info -->
<div class="card">
    <div class="card-center">
        <div class="icon-circle circle-yellow">
            <img src="https://img.icons8.com/fluency/48/high-priority.png" class="widget-icon" alt="Warning">
        </div>
        <div class="widget-title" style="color: #F57F17;">Alasan Gagal</div>
        <p class="widget-desc" style="color: #6B7280;">
            @if(isset($transaction->result_data['message']))
                {{ $transaction->result_data['message'] }}
            @else
                Terjadi kesalahan sistem. Mohon coba lagi nanti atau hubungi customer service kami.
            @endif
        </p>
    </div>
</div>

@if($transaction->is_refunded)
<!-- Refund Info -->
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
        <p class="widget-desc">Tim kami siap membantu Anda 24/7.</p>
        <a href="https://wa.me/6282210109289" class="btn-download">Chat Admin</a>
    </div>
</div>
@endsection
