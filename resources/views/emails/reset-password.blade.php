@extends('emails.layout')
@section('title', 'Reset Password')

@section('content')
<!-- Header Card - Reset Password -->
<div class="card">
    <div class="bg-reset">
        <img src="https://img.icons8.com/fluency/96/key.png" alt="Key" class="icon-hero">
        <h2 class="header-title">Reset Password</h2>
        <p class="header-desc">Permintaan atur ulang kata sandi diterima. Klik tombol di bawah untuk melanjutkan.</p>
        <a href="{{ $reset_url ?? '#' }}" class="btn btn-white" style="color: #FF416C;">Atur Ulang Sekarang</a>
    </div>
</div>

<!-- Info -->
<div class="card">
    <div class="card-padding">
        <span class="section-label">Detail Permintaan</span>
        <table class="data-table">
            <tr class="data-row">
                <td class="label">Email</td>
                <td class="value">{{ $email ?? '-' }}</td>
            </tr>
            <tr class="data-row">
                <td class="label">Berlaku Sampai</td>
                <td class="value">{{ $expires_at ?? '60 menit' }}</td>
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
            Jika Anda tidak meminta reset password, abaikan email ini. Link akan kadaluarsa secara otomatis.
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
