@extends('layouts.main')

@section('title', 'Status Pesanan - ' . config('app.name'))

@section('content')
<div class="status-page">
    <!-- Header -->
    <div class="page-header">
        <a href="{{ route('home') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="page-title">Status Pesanan</h1>
        <div class="header-spacer"></div>
    </div>

    <!-- Status Content -->
    <div class="status-content">
        @php
            $paymentStatus = $invoiceData['statusPayment'] ?? $invoiceData['status_payment'] ?? 'pending';
            $orderStatus = $invoiceData['statusOrder'] ?? $invoiceData['status_order'] ?? 'pending';
            $paymentStatusLower = strtolower($paymentStatus);
            $orderStatusLower = strtolower($orderStatus);

            $isPaid = in_array($paymentStatusLower, ['success', 'paid', 'completed']);
            $isPaymentFailed = in_array($paymentStatusLower, ['failed', 'expired', 'cancelled']);
            $isOrderSuccess = in_array($orderStatusLower, ['success', 'completed', 'done']);
            $isOrderFailed = in_array($orderStatusLower, ['failed', 'error']);
        @endphp

        <!-- Status Banner -->
        <div class="status-banner {{ $isOrderSuccess ? 'success' : ($isPaymentFailed || $isOrderFailed ? 'failed' : ($isPaid ? 'processing' : 'pending')) }}">
            @if($isOrderSuccess)
                <div class="status-icon"><i class="fas fa-check-circle"></i></div>
                <div class="status-title">Pesanan Berhasil</div>
                <div class="status-desc">Top up telah dikirim ke akun Anda</div>
            @elseif($isOrderFailed)
                <div class="status-icon"><i class="fas fa-times-circle"></i></div>
                <div class="status-title">Pesanan Gagal</div>
                <div class="status-desc">Terjadi kesalahan saat memproses pesanan</div>
            @elseif($isPaymentFailed)
                <div class="status-icon"><i class="fas fa-times-circle"></i></div>
                <div class="status-title">Pembayaran Gagal</div>
                <div class="status-desc">Pembayaran tidak berhasil atau kedaluwarsa</div>
            @elseif($isPaid)
                <div class="status-icon"><i class="fas fa-spinner fa-spin"></i></div>
                <div class="status-title">Sedang Diproses</div>
                <div class="status-desc">Pesanan sedang dalam proses pengiriman</div>
            @else
                <div class="status-icon"><i class="fas fa-clock"></i></div>
                <div class="status-title">Menunggu Pembayaran</div>
                <div class="status-desc">Segera selesaikan pembayaran Anda</div>
            @endif
        </div>

        <!-- Invoice Info -->
        <div class="invoice-card">
            <div class="invoice-label">No. Invoice</div>
            <div class="invoice-number">{{ $invoiceNumber }}</div>
            <button class="copy-btn" onclick="copyInvoice()">
                <i class="fas fa-copy"></i>
            </button>
        </div>

        <!-- Order Timeline -->
        <div class="timeline-card">
            <div class="card-title">Status Pesanan</div>
            <div class="timeline">
                <div class="timeline-item {{ $invoiceNumber ? 'completed' : '' }}">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Pesanan Dibuat</div>
                        <div class="timeline-time">{{ $invoiceData['createdAt'] ?? $invoiceData['created_at'] ?? now()->format('d M Y H:i') }}</div>
                    </div>
                </div>
                <div class="timeline-item {{ $isPaid ? 'completed' : ($isPaymentFailed ? 'failed' : 'pending') }}">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Pembayaran</div>
                        <div class="timeline-time">
                            @if($isPaid)
                                Pembayaran diterima
                            @elseif($isPaymentFailed)
                                {{ ucfirst($paymentStatus) }}
                            @else
                                Menunggu pembayaran
                            @endif
                        </div>
                    </div>
                </div>
                <div class="timeline-item {{ $isOrderSuccess ? 'completed' : ($isOrderFailed ? 'failed' : 'pending') }}">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Pengiriman</div>
                        <div class="timeline-time">
                            @if($isOrderSuccess)
                                Berhasil dikirim
                            @elseif($isOrderFailed)
                                Gagal mengirim
                            @elseif($isPaid)
                                Sedang diproses
                            @else
                                Menunggu pembayaran
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code if pending -->
        @if(!$isPaid && !$isPaymentFailed && $qrCodeUrl)
            <div class="qr-card">
                <div class="card-title">Scan untuk Bayar</div>
                <div class="qr-wrapper">
                    <img src="{{ $qrCodeUrl }}" alt="QRIS Payment" class="qr-image">
                </div>
                <div class="qr-hint">Scan dengan e-wallet atau mobile banking</div>
            </div>
        @endif

        <!-- Order Details -->
        <div class="detail-card">
            <div class="card-title">Detail Pesanan</div>
            <div class="detail-row">
                <span>Produk</span>
                <span>{{ $invoiceData['itemName'] ?? $invoiceData['item_name'] ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span>User ID</span>
                <span>{{ $invoiceData['gameId'] ?? $invoiceData['game_id'] ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span>Nickname</span>
                <span>{{ $invoiceData['nickname'] ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span>Pembayaran</span>
                <span>{{ $invoiceData['paymentName'] ?? $invoiceData['payment_name'] ?? '-' }}</span>
            </div>
            <div class="detail-row total">
                <span>Total</span>
                <span>Rp {{ number_format($invoiceData['total'] ?? $invoiceData['amount'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- SN/Reference if available -->
        @if(!empty($invoiceData['sn']) || !empty($invoiceData['reference']))
            <div class="detail-card">
                <div class="card-title">Info Transaksi</div>
                @if(!empty($invoiceData['sn']))
                    <div class="detail-row">
                        <span>Serial Number</span>
                        <span class="sn-value">{{ $invoiceData['sn'] }}</span>
                    </div>
                @endif
                @if(!empty($invoiceData['reference']))
                    <div class="detail-row">
                        <span>Reference</span>
                        <span>{{ $invoiceData['reference'] }}</span>
                    </div>
                @endif
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="action-buttons">
            @if(!$isPaid && !$isPaymentFailed)
                <button class="btn-primary" onclick="refreshStatus()">
                    <i class="fas fa-sync-alt"></i> Cek Status
                </button>
            @endif
            <a href="{{ route('home') }}" class="btn-secondary">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
            <a href="{{ route('track.order') }}" class="btn-outline">
                <i class="fas fa-search"></i> Cek Pesanan Lain
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
.status-page {
    min-height: 100vh;
    background: var(--ios-bg, #F2F2F7);
}

/* Header */
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    background: white;
    border-bottom: 1px solid #E5E5EA;
}

.back-btn {
    width: 40px;
    height: 40px;
    background: #F8F8F8;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-main, #1C1C1E);
    text-decoration: none;
    transition: all 0.3s;
}

.back-btn:hover {
    background: #E5E5EA;
}

.page-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-main, #1C1C1E);
    margin: 0;
}

.header-spacer {
    width: 40px;
}

/* Content */
.status-content {
    padding: 20px;
}

/* Status Banner */
.status-banner {
    border-radius: 20px;
    padding: 30px 20px;
    text-align: center;
    margin-bottom: 16px;
}

.status-banner.success {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
}

.status-banner.processing {
    background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
}

.status-banner.pending {
    background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
}

.status-banner.failed {
    background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
}

.status-icon {
    font-size: 48px;
    color: white;
    margin-bottom: 12px;
}

.status-title {
    font-size: 20px;
    font-weight: 700;
    color: white;
    margin-bottom: 4px;
}

.status-desc {
    font-size: 14px;
    color: rgba(255,255,255,0.9);
}

/* Invoice Card */
.invoice-card {
    background: white;
    border-radius: 16px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.invoice-label {
    font-size: 12px;
    color: var(--text-sub, #8E8E93);
    margin-right: 12px;
}

.invoice-number {
    flex: 1;
    font-size: 14px;
    font-weight: 700;
    color: var(--text-main, #1C1C1E);
    font-family: monospace;
}

.copy-btn {
    width: 36px;
    height: 36px;
    background: #F8F8F8;
    border: none;
    border-radius: 10px;
    color: var(--text-sub, #8E8E93);
    cursor: pointer;
    transition: all 0.3s;
}

.copy-btn:hover {
    background: #E5E5EA;
    color: var(--brand-primary, #0033AA);
}

/* Timeline Card */
.timeline-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-main, #1C1C1E);
    margin-bottom: 16px;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #E5E5EA;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -30px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
    border: 2px solid #E5E5EA;
    z-index: 1;
}

.timeline-item.completed .timeline-dot {
    background: #10B981;
    border-color: #10B981;
}

.timeline-item.completed .timeline-dot::after {
    content: '\f00c';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    font-size: 10px;
    color: white;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.timeline-item.failed .timeline-dot {
    background: #EF4444;
    border-color: #EF4444;
}

.timeline-item.failed .timeline-dot::after {
    content: '\f00d';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    font-size: 10px;
    color: white;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.timeline-item.pending .timeline-dot {
    background: #F59E0B;
    border-color: #F59E0B;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-main, #1C1C1E);
    margin-bottom: 2px;
}

.timeline-time {
    font-size: 12px;
    color: var(--text-sub, #8E8E93);
}

/* QR Card */
.qr-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.qr-wrapper {
    background: white;
    padding: 16px;
    border-radius: 12px;
    display: inline-block;
    margin: 12px 0;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
}

.qr-image {
    width: 180px;
    height: 180px;
    object-fit: contain;
}

.qr-hint {
    font-size: 12px;
    color: var(--text-sub, #8E8E93);
}

/* Detail Card */
.detail-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #F2F2F7;
    font-size: 14px;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-row span:first-child {
    color: var(--text-sub, #8E8E93);
}

.detail-row span:last-child {
    color: var(--text-main, #1C1C1E);
    font-weight: 500;
    text-align: right;
    max-width: 60%;
    word-break: break-all;
}

.detail-row.total {
    border-top: 2px solid #E5E5EA;
    margin-top: 8px;
    padding-top: 16px;
}

.detail-row.total span:last-child {
    font-size: 18px;
    font-weight: 700;
    color: var(--brand-primary, #0033AA);
}

.sn-value {
    font-family: monospace;
    background: #F8F8F8;
    padding: 4px 8px;
    border-radius: 6px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 8px;
}

.btn-primary {
    width: 100%;
    padding: 16px;
    background: var(--brand-primary, #0033AA);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-primary:hover {
    background: var(--brand-secondary, #002288);
}

.btn-secondary {
    width: 100%;
    padding: 16px;
    background: #10B981;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-secondary:hover {
    background: #059669;
}

.btn-outline {
    width: 100%;
    padding: 16px;
    background: white;
    color: var(--text-main, #1C1C1E);
    border: 2px solid #E5E5EA;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-outline:hover {
    background: #F8F8F8;
}

/* Responsive */
@media (max-width: 480px) {
    .qr-image {
        width: 150px;
        height: 150px;
    }
}
</style>
@endpush

@push('scripts')
<script>
function copyInvoice() {
    const invoiceNumber = '{{ $invoiceNumber }}';
    navigator.clipboard.writeText(invoiceNumber).then(() => {
        const btn = document.querySelector('.copy-btn');
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-copy"></i>';
        }, 2000);
    });
}

function refreshStatus() {
    window.location.reload();
}

// Auto refresh for pending payments
@if(!$isPaid && !$isPaymentFailed)
setTimeout(() => {
    window.location.reload();
}, 15000);
@endif
</script>
@endpush
@endsection
