@extends('layouts.app')

@section('title', 'Detail Transaksi - ' . config('app.name'))

@section('content')
<div class="main-content">
    <div class="detail-container">
        <!-- Back Button -->
        <div class="back-section">
            <a href="{{ route('dashboard.transactions') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>

        <!-- Status Header -->
        <div class="status-header {{ $transaction->status }}">
            <div class="status-icon">
                @if($transaction->status === 'completed')
                    <i class="fas fa-check-circle"></i>
                @elseif($transaction->status === 'processing' || $transaction->status === 'paid')
                    <i class="fas fa-spinner fa-spin"></i>
                @elseif($transaction->status === 'pending')
                    <i class="fas fa-clock"></i>
                @else
                    <i class="fas fa-times-circle"></i>
                @endif
            </div>
            <h1>{{ ucfirst($transaction->status) }}</h1>
            <p class="status-message">
                @if($transaction->status === 'completed')
                    Transaksi berhasil diproses
                @elseif($transaction->status === 'processing' || $transaction->status === 'paid')
                    Pesanan sedang diproses
                @elseif($transaction->status === 'pending')
                    Menunggu pembayaran
                @elseif($transaction->status === 'failed')
                    Transaksi gagal
                @else
                    Transaksi dibatalkan
                @endif
            </p>
        </div>

        <!-- Order Information -->
        <div class="info-card">
            <h3 class="card-title">Informasi Pesanan</h3>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Order ID</span>
                    <span class="info-value">{{ $transaction->order_id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Invoice</span>
                    <span class="info-value">{{ $transaction->invoice_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal</span>
                    <span class="info-value">{{ $transaction->created_at->format('d M Y H:i:s') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="badge badge-{{ $transaction->status }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Product Information -->
        <div class="info-card">
            <h3 class="card-title">Detail Produk</h3>
            <div class="product-section">
                <div class="product-header">
                    <div class="product-name">{{ $transaction->product_name }}</div>
                    <div class="product-category">{{ $transaction->category_name }}</div>
                </div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">Tujuan</span>
                        <span class="info-value">{{ $transaction->order_data['customer_no'] ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Jumlah</span>
                        <span class="info-value">{{ $transaction->quantity }}</span>
                    </div>
                    @if(isset($transaction->order_data['zone_id']))
                        <div class="info-row">
                            <span class="info-label">Zone ID</span>
                            <span class="info-value">{{ $transaction->order_data['zone_id'] }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Result Information (if completed) -->
        @if($transaction->status === 'completed' && $transaction->result_data)
            <div class="info-card result-card">
                <h3 class="card-title">
                    <i class="fas fa-check-circle"></i>
                    Hasil Top Up
                </h3>
                @if(isset($transaction->result_data['serial_number']))
                    <div class="result-item">
                        <span class="result-label">Serial Number</span>
                        <div class="sn-code">
                            {{ $transaction->result_data['serial_number'] }}
                            <button class="copy-btn" onclick="copyToClipboard('{{ $transaction->result_data['serial_number'] }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                @endif
                @if(isset($transaction->result_data['message']))
                    <div class="result-message">
                        <i class="fas fa-info-circle"></i>
                        {{ $transaction->result_data['message'] }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Payment Information -->
        <div class="info-card">
            <h3 class="card-title">Rincian Pembayaran</h3>
            <div class="payment-breakdown">
                <div class="breakdown-row">
                    <span>Harga Produk</span>
                    <span>Rp {{ number_format($transaction->product_price, 0, ',', '.') }}</span>
                </div>
                @if($transaction->admin_fee > 0)
                    <div class="breakdown-row">
                        <span>Biaya Admin</span>
                        <span>Rp {{ number_format($transaction->admin_fee, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($transaction->discount > 0)
                    <div class="breakdown-row discount">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="breakdown-row total">
                    <span>Total Pembayaran</span>
                    <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="info-row" style="margin-top: 16px;">
                <span class="info-label">Metode Pembayaran</span>
                <span class="info-value">{{ ucfirst($transaction->payment_method) }}</span>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="info-card">
            <h3 class="card-title">Informasi Kontak</h3>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Nama</span>
                    <span class="info-value">{{ $transaction->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">WhatsApp</span>
                    <span class="info-value">{{ $transaction->customer_phone }}</span>
                </div>
                @if($transaction->customer_email)
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $transaction->customer_email }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            @if($transaction->status === 'pending')
                <a href="{{ route('payment', $transaction->order_id) }}" class="btn btn-primary">
                    <i class="fas fa-credit-card"></i>
                    Bayar Sekarang
                </a>
            @endif
            <a href="{{ route('dashboard.transactions') }}" class="btn btn-secondary">
                <i class="fas fa-receipt"></i>
                Lihat Transaksi Lain
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
.main-content { padding: 20px; }
.detail-container { max-width: 700px; margin: 0 auto; }
.back-section { margin-bottom: 16px; }
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #64748B;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: color 0.3s;
}
.back-btn:hover { color: var(--brand-primary); }

.status-header {
    padding: 40px 24px;
    border-radius: 16px;
    text-align: center;
    margin-bottom: 20px;
    color: white;
}
.status-header.completed { background: linear-gradient(135deg, #059669, #10B981); }
.status-header.processing, .status-header.paid { background: linear-gradient(135deg, #2563EB, #3B82F6); }
.status-header.pending { background: linear-gradient(135deg, #F59E0B, #FBBF24); }
.status-header.failed, .status-header.cancelled { background: linear-gradient(135deg, #DC2626, #EF4444); }
.status-icon { font-size: 64px; margin-bottom: 16px; }
.status-header h1 { font-size: 32px; font-weight: 700; margin-bottom: 8px; }
.status-message { font-size: 16px; opacity: 0.95; }

.info-card {
    background: white;
    padding: 24px;
    border-radius: 16px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.card-title {
    font-size: 18px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.info-grid {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.info-label {
    font-size: 14px;
    color: #64748B;
}
.info-value {
    font-size: 14px;
    font-weight: 600;
    color: #1E293B;
    text-align: right;
}
.badge {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}
.badge-completed { background: #D1FAE5; color: #065F46; }
.badge-processing, .badge-paid { background: #DBEAFE; color: #1E40AF; }
.badge-pending { background: #FEF3C7; color: #92400E; }
.badge-failed, .badge-cancelled { background: #FEE2E2; color: #991B1B; }

.product-section {
    background: #F8FAFC;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 16px;
}
.product-header {
    margin-bottom: 16px;
}
.product-name {
    font-size: 16px;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 4px;
}
.product-category {
    font-size: 13px;
    color: #64748B;
}

.result-card {
    background: linear-gradient(135deg, #ECFDF5, #D1FAE5);
    border: 2px solid #10B981;
}
.result-card .card-title {
    color: #065F46;
}
.result-item {
    margin-bottom: 16px;
}
.result-label {
    display: block;
    font-size: 13px;
    color: #065F46;
    font-weight: 600;
    margin-bottom: 8px;
}
.sn-code {
    background: white;
    padding: 14px 16px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    color: #059669;
    letter-spacing: 1px;
    word-break: break-all;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}
.copy-btn {
    background: #D1FAE5;
    border: none;
    padding: 8px 12px;
    border-radius: 8px;
    color: #059669;
    cursor: pointer;
    transition: all 0.3s;
    flex-shrink: 0;
}
.copy-btn:hover {
    background: #A7F3D0;
}
.result-message {
    background: white;
    padding: 12px 16px;
    border-radius: 10px;
    color: #047857;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.payment-breakdown {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 16px;
}
.breakdown-row {
    display: flex;
    justify-content: space-between;
    color: #64748B;
    font-size: 14px;
}
.breakdown-row.discount { color: #059669; }
.breakdown-row.total {
    border-top: 2px solid #F1F5F9;
    padding-top: 12px;
    margin-top: 8px;
    font-size: 18px;
    font-weight: 700;
    color: #1E293B;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-top: 24px;
}
.btn {
    padding: 14px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
}
.btn-primary {
    background: var(--brand-primary);
    color: white;
}
.btn-primary:hover {
    background: var(--brand-secondary);
    transform: translateY(-2px);
}
.btn-secondary {
    background: #64748B;
    color: white;
}
.btn-secondary:hover {
    background: #475569;
    transform: translateY(-2px);
}
</style>
@endpush

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Serial number berhasil disalin!');
    }).catch(function() {
        alert('Gagal menyalin serial number');
    });
}
</script>
@endpush
@endsection
