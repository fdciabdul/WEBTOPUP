@extends('emails.layout')
@section('title', 'Selamat Datang')

@section('content')
<!-- Header Card - Welcome -->
<div class="card">
    <div class="bg-welcome">
        <img src="https://img.icons8.com/fluency/96/handshake.png" alt="Welcome" class="icon-hero">
        <h2 class="header-title">Selamat Datang!</h2>
        <p class="header-desc">Akun Anda berhasil dibuat. Verifikasi email untuk mulai bertransaksi.</p>
        @if(isset($verification_url))
            <a href="{{ $verification_url }}" class="btn btn-white" style="color: #11998e;">Verifikasi Email</a>
        @endif
    </div>
</div>

<!-- Account Info -->
<div class="card">
    <div class="card-padding">
        <span class="section-label">Informasi Akun</span>
        <table class="data-table">
            <tr class="data-row">
                <td class="label">Nama</td>
                <td class="value">{{ $name ?? 'User' }}</td>
            </tr>
            <tr class="data-row">
                <td class="label">Email</td>
                <td class="value">{{ $email ?? '-' }}</td>
            </tr>
            @if(isset($phone))
            <tr class="data-row">
                <td class="label">WhatsApp</td>
                <td class="value">{{ $phone }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>

<!-- Info Widget -->
<div class="card">
    <div class="card-center">
        <div class="icon-circle circle-blue">
            <img src="https://img.icons8.com/fluency/48/receipt.png" class="widget-icon" alt="Info">
        </div>
        <div class="widget-title">Mulai Top Up Sekarang</div>
        <p class="widget-desc">Nikmati top up game, pulsa, dan produk digital lainnya dengan harga terbaik.</p>
        <a href="{{ config('app.url') }}" class="btn-download">Kunjungi Website</a>
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
