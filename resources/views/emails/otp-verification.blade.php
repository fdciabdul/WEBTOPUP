@extends('emails.layout')
@section('title', 'Verifikasi Login')

@section('content')
<!-- Header Card - OTP -->
<div class="card">
    <div class="bg-otp">
        <img src="https://img.icons8.com/fluency/96/lock.png" alt="Lock" class="icon-hero">
        <h2 class="header-title">Verifikasi Login</h2>
        <div style="background: rgba(255,255,255,0.25); border-radius: 12px; padding: 10px 25px; font-size: 32px; font-weight: 800; letter-spacing: 8px; display: inline-block; margin-bottom: 5px; border: 1px solid rgba(255,255,255,0.3); color: #fff;">
            {{ $otp_code ?? '000000' }}
        </div>
        <p style="font-size: 12px; margin-top: 5px; color: rgba(255,255,255,0.9); font-weight: 500;">
            Jangan bagikan kode ini ke siapapun!
        </p>
    </div>
</div>

<!-- Info -->
<div class="card">
    <div class="card-padding">
        <span class="section-label">Detail Verifikasi</span>
        <table class="data-table">
            <tr class="data-row">
                <td class="label">Email</td>
                <td class="value">{{ $email ?? '-' }}</td>
            </tr>
            <tr class="data-row">
                <td class="label">Berlaku Sampai</td>
                <td class="value">{{ $expires_at ?? '10 menit' }}</td>
            </tr>
            @if(isset($ip_address))
            <tr class="data-row">
                <td class="label">IP Address</td>
                <td class="value">{{ $ip_address }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>

<!-- Warning -->
<div class="card">
    <div class="card-center">
        <div class="icon-circle circle-yellow">
            <img src="https://img.icons8.com/fluency/48/high-priority.png" class="widget-icon" alt="Warning">
        </div>
        <div class="widget-title" style="color: #F57F17;">PERINGATAN KEAMANAN</div>
        <p class="widget-desc" style="color: #F57F17;">
            Jika Anda tidak melakukan permintaan ini, segera hubungi tim kami.
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
