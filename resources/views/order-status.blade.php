@extends('layouts.main')

@section('title', 'Status Pesanan - ' . config('app.name'))

@section('content')
<div class="main-content">
    <div class="order-status-container">
        <div class="status-header {{ $transaction->status }}">
            <div class="status-icon">
                @if($transaction->status === 'completed')
                    <i class="fas fa-check-circle"></i>
                @elseif($transaction->status === 'processing' || $transaction->status === 'paid')
                    <i class="fas fa-spinner fa-spin"></i>
                @elseif($transaction->status === 'pending')
                    <i class="fas fa-clock"></i>
                @elseif($transaction->status === 'failed' || $transaction->status === 'cancelled')
                    <i class="fas fa-times-circle"></i>
                @endif
            </div>
            <h1>{{ ucfirst($transaction->status) }}</h1>
            <p class="status-message">
                @if($transaction->status === 'completed')
                    Pesanan Anda telah selesai!
                @elseif($transaction->status === 'processing' || $transaction->status === 'paid')
                    Pesanan Anda sedang diproses
                @elseif($transaction->status === 'pending')
                    Menunggu pembayaran
                @elseif($transaction->status === 'failed')
                    Pesanan gagal diproses
                @elseif($transaction->status === 'cancelled')
                    Pesanan dibatalkan
                @endif
            </p>
        </div>

        <div class="order-details">
            <div class="detail-section">
                <h3>Informasi Pesanan</h3>
                <div class="detail-row">
                    <span>Order ID</span>
                    <strong>{{ $transaction->order_id }}</strong>
                </div>
                <div class="detail-row">
                    <span>Invoice</span>
                    <span>{{ $transaction->invoice_number }}</span>
                </div>
                <div class="detail-row">
                    <span>Tanggal</span>
                    <span>{{ $transaction->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span>Status</span>
                    <span class="badge badge-{{ $transaction->status }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h3>Detail Produk</h3>
                <div class="product-info">
                    <div class="product-name">{{ $transaction->product_name }}</div>
                    <div class="product-category">{{ $transaction->category_name }}</div>
                </div>
                <div class="detail-row">
                    <span>Tujuan</span>
                    <strong>{{ $transaction->order_data['customer_no'] ?? '-' }}</strong>
                </div>
                <div class="detail-row">
                    <span>Jumlah</span>
                    <span>{{ $transaction->quantity }}</span>
                </div>
            </div>

            @if($transaction->status === 'completed' && $transaction->result_data)
                <div class="detail-section result-section">
                    <h3>Hasil Top Up</h3>
                    @if(isset($transaction->result_data['serial_number']))
                        <div class="result-item">
                            <span>Serial Number</span>
                            <strong class="sn-code">{{ $transaction->result_data['serial_number'] }}</strong>
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

            <div class="detail-section">
                <h3>Rincian Pembayaran</h3>
                <div class="detail-row">
                    <span>Harga Produk</span>
                    <span>Rp {{ number_format($transaction->product_price, 0, ',', '.') }}</span>
                </div>
                @if($transaction->admin_fee > 0)
                    <div class="detail-row">
                        <span>Biaya Admin</span>
                        <span>Rp {{ number_format($transaction->admin_fee, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($transaction->discount > 0)
                    <div class="detail-row discount">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="detail-row total">
                    <span>Total</span>
                    <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
                </div>
                <div class="detail-row">
                    <span>Metode Pembayaran</span>
                    <span>{{ ucfirst($transaction->payment_method) }}</span>
                </div>
            </div>

            <div class="detail-section">
                <h3>Informasi Kontak</h3>
                <div class="detail-row">
                    <span>Nama</span>
                    <span>{{ $transaction->customer_name }}</span>
                </div>
                <div class="detail-row">
                    <span>WhatsApp</span>
                    <span>{{ $transaction->customer_phone }}</span>
                </div>
                @if($transaction->customer_email)
                    <div class="detail-row">
                        <span>Email</span>
                        <span>{{ $transaction->customer_email }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Kembali ke Home
            </a>
            @if($transaction->status === 'pending')
                <a href="{{ route('payment', $transaction->order_id) }}" class="btn btn-success">
                    <i class="fas fa-credit-card"></i>
                    Bayar Sekarang
                </a>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.main-content { padding: 20px; }
.order-status-container { max-width: 700px; margin: 0 auto; }
.status-header {
    padding: 40px 24px;
    border-radius: 16px;
    text-align: center;
    margin-bottom: 24px;
    color: white;
}
.status-header.completed { background: linear-gradient(135deg, #059669, #10B981); }
.status-header.processing, .status-header.paid { background: linear-gradient(135deg, #2563EB, #3B82F6); }
.status-header.pending { background: linear-gradient(135deg, #F59E0B, #FBBF24); }
.status-header.failed, .status-header.cancelled { background: linear-gradient(135deg, #DC2626, #EF4444); }
.status-icon { font-size: 64px; margin-bottom: 16px; }
.status-header h1 { font-size: 32px; font-weight: 700; margin-bottom: 8px; }
.status-message { font-size: 16px; opacity: 0.95; }
.order-details {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.detail-section {
    margin-bottom: 28px;
    padding-bottom: 28px;
    border-bottom: 2px solid #F1F5F9;
}
.detail-section:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}
.detail-section h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 16px;
}
.product-info {
    background: #F8FAFC;
    padding: 16px;
    border-radius: 12px;
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
.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    color: #64748B;
    font-size: 14px;
}
.detail-row.total {
    border-top: 2px solid #F1F5F9;
    padding-top: 12px;
    margin-top: 12px;
    font-size: 18px;
    color: #1E293B;
}
.detail-row.discount { color: #059669; }
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
.result-section {
    background: linear-gradient(135deg, #ECFDF5, #D1FAE5);
    padding: 20px;
    border-radius: 12px;
    border: 2px solid #10B981;
}
.result-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}
.result-item span {
    font-size: 13px;
    color: #065F46;
    font-weight: 600;
}
.sn-code {
    background: white;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 700;
    color: #059669;
    letter-spacing: 1px;
    word-break: break-all;
}
.result-message {
    background: white;
    padding: 12px 16px;
    border-radius: 8px;
    color: #047857;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}
.action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
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
    background: #64748B;
    color: white;
}
.btn-primary:hover {
    background: #475569;
    transform: translateY(-2px);
}
.btn-success {
    background: #059669;
    color: white;
}
.btn-success:hover {
    background: #047857;
    transform: translateY(-2px);
}
</style>
@endpush
@endsection
