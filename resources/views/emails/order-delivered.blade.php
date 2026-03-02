@extends('emails.layout')
@section('title', 'Pesanan Dikirim')

@section('content')
<!-- Header Card - Delivered -->
<div class="card">
    <div class="bg-delivered">
        <img src="https://img.icons8.com/fluency/96/open-box.png" alt="Delivered" class="icon-hero">
        <h2 class="header-title">Produk Siap!</h2>
        <p class="header-desc">Pesanan {{ $transaction->order_id }} selesai. Produk Anda telah dikirim dan siap digunakan.</p>
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
                <td class="label">Total Bayar</td>
                <td class="highlight-price">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</div>

<!-- Delivery Data -->
@if($transaction->delivery_data)
<div class="card">
    <div class="card-padding">
        <span class="section-label">Detail Pesanan</span>
        <table class="data-table">
            @foreach($transaction->delivery_data as $key => $val)
            <tr class="data-row">
                <td class="label">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                <td class="value">
                    @if(in_array(strtolower($key), ['pin', 'profile', 'otp']))
                        <span class="pin-value">{{ $val }}</span>
                    @else
                        {{ $val }}
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
        <p style="font-size: 12px; color: #6B7280; margin-top: 15px; text-align: center; font-style: italic;">
            *Harap segera login. Jangan ubah email/password.
        </p>
    </div>
</div>
@endif

<!-- Important Note -->
<div class="card">
    <div class="card-center">
        <div class="icon-circle circle-yellow">
            <img src="https://img.icons8.com/fluency/48/high-priority.png" class="widget-icon" alt="Warning">
        </div>
        <div class="widget-title" style="color: #F57F17;">CATATAN PENTING</div>
        <p class="widget-desc" style="color: #F57F17;">
            Dilarang mengubah Email & Password akun. Garansi otomatis hangus jika melanggar.
        </p>
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
