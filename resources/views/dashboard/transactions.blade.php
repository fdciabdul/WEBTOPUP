@extends('layouts.app')

@section('title', 'Transaksi Saya - ' . config('app.name'))

@section('content')
<div class="main-content">
    <div class="transactions-container">
        <div class="page-header">
            <h1>Transaksi Saya</h1>
            <p>Riwayat semua transaksi Anda</p>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="{{ route('dashboard.transactions') }}" class="tab {{ !request('status') ? 'active' : '' }}">
                Semua
                <span class="count">{{ $allCount }}</span>
            </a>
            <a href="{{ route('dashboard.transactions', ['status' => 'pending']) }}" class="tab {{ request('status') === 'pending' ? 'active' : '' }}">
                Pending
                <span class="count">{{ $pendingCount }}</span>
            </a>
            <a href="{{ route('dashboard.transactions', ['status' => 'processing']) }}" class="tab {{ request('status') === 'processing' ? 'active' : '' }}">
                Proses
                <span class="count">{{ $processingCount }}</span>
            </a>
            <a href="{{ route('dashboard.transactions', ['status' => 'completed']) }}" class="tab {{ request('status') === 'completed' ? 'active' : '' }}">
                Selesai
                <span class="count">{{ $completedCount }}</span>
            </a>
            <a href="{{ route('dashboard.transactions', ['status' => 'failed']) }}" class="tab {{ request('status') === 'failed' ? 'active' : '' }}">
                Gagal
                <span class="count">{{ $failedCount }}</span>
            </a>
        </div>

        <!-- Transactions List -->
        @if($transactions->count() > 0)
            <div class="transactions-list">
                @foreach($transactions as $transaction)
                    <a href="{{ route('dashboard.transaction.detail', $transaction->order_id) }}" class="transaction-card">
                        <div class="transaction-header">
                            <div class="transaction-icon-wrapper">
                                @if($transaction->status === 'completed')
                                    <div class="status-icon completed">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                @elseif($transaction->status === 'processing' || $transaction->status === 'paid')
                                    <div class="status-icon processing">
                                        <i class="fas fa-spinner fa-pulse"></i>
                                    </div>
                                @elseif($transaction->status === 'pending')
                                    <div class="status-icon pending">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                @else
                                    <div class="status-icon failed">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="transaction-info">
                                <div class="transaction-product">{{ $transaction->product_name }}</div>
                                <div class="transaction-category">{{ $transaction->category_name }}</div>
                            </div>
                            <span class="badge badge-{{ $transaction->status }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </div>

                        <div class="transaction-body">
                            <div class="transaction-detail">
                                <div class="detail-item">
                                    <span class="detail-label">Order ID</span>
                                    <span class="detail-value">{{ $transaction->order_id }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Tujuan</span>
                                    <span class="detail-value">{{ $transaction->order_data['customer_no'] ?? '-' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Tanggal</span>
                                    <span class="detail-value">{{ $transaction->created_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>

                            <div class="transaction-amount-section">
                                <div class="amount-label">Total Pembayaran</div>
                                <div class="amount-value">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="transaction-footer">
                            @if($transaction->status === 'pending')
                                <span class="footer-action">
                                    <i class="fas fa-credit-card"></i>
                                    Bayar Sekarang
                                </span>
                            @else
                                <span class="footer-text">Lihat Detail</span>
                            @endif
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="pagination-container">
                    {{ $transactions->appends(['status' => request('status')])->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3>Belum ada transaksi</h3>
                <p>Transaksi Anda akan muncul di sini</p>
                <a href="{{ route('home') }}" class="btn-shop">
                    <i class="fas fa-shopping-bag"></i>
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.main-content { padding: 20px; }
.transactions-container { max-width: 900px; margin: 0 auto; }
.page-header {
    margin-bottom: 24px;
}
.page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 6px;
}
.page-header p {
    font-size: 14px;
    color: #64748B;
}

.filter-tabs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    margin-bottom: 24px;
    padding-bottom: 4px;
    -webkit-overflow-scrolling: touch;
}
.filter-tabs::-webkit-scrollbar {
    height: 4px;
}
.filter-tabs::-webkit-scrollbar-thumb {
    background: #CBD5E1;
    border-radius: 4px;
}
.tab {
    padding: 10px 16px;
    background: white;
    border: 2px solid #E2E8F0;
    border-radius: 10px;
    text-decoration: none;
    color: #64748B;
    font-weight: 600;
    font-size: 13px;
    white-space: nowrap;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 6px;
}
.tab:hover {
    border-color: var(--brand-primary);
    color: var(--brand-primary);
}
.tab.active {
    background: var(--brand-primary);
    border-color: var(--brand-primary);
    color: white;
}
.tab .count {
    background: rgba(0,0,0,0.1);
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 11px;
}
.tab.active .count {
    background: rgba(255,255,255,0.2);
}

.transactions-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.transaction-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.transaction-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.transaction-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid #F1F5F9;
}
.status-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}
.status-icon.completed {
    background: #D1FAE5;
    color: #059669;
}
.status-icon.processing {
    background: #DBEAFE;
    color: #2563EB;
}
.status-icon.pending {
    background: #FEF3C7;
    color: #D97706;
}
.status-icon.failed {
    background: #FEE2E2;
    color: #DC2626;
}
.transaction-info {
    flex: 1;
}
.transaction-product {
    font-size: 15px;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 4px;
}
.transaction-category {
    font-size: 12px;
    color: #94A3B8;
}
.badge {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
}
.badge-pending { background: #FEF3C7; color: #92400E; }
.badge-paid, .badge-processing { background: #DBEAFE; color: #1E40AF; }
.badge-completed { background: #D1FAE5; color: #065F46; }
.badge-failed, .badge-cancelled { background: #FEE2E2; color: #991B1B; }

.transaction-body {
    margin-bottom: 16px;
}
.transaction-detail {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 16px;
}
.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.detail-label {
    font-size: 13px;
    color: #64748B;
}
.detail-value {
    font-size: 13px;
    font-weight: 600;
    color: #1E293B;
}
.transaction-amount-section {
    background: #F8FAFC;
    padding: 14px 16px;
    border-radius: 10px;
}
.amount-label {
    font-size: 12px;
    color: #64748B;
    margin-bottom: 4px;
}
.amount-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--brand-primary);
}

.transaction-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    border-top: 1px solid #F1F5F9;
}
.footer-action {
    color: var(--brand-primary);
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}
.footer-text {
    color: #64748B;
    font-size: 14px;
    font-weight: 600;
}
.transaction-footer i.fa-chevron-right {
    color: #CBD5E1;
    font-size: 14px;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}
.empty-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 24px;
    background: linear-gradient(135deg, #F1F5F9, #E2E8F0);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #94A3B8;
}
.empty-state h3 {
    font-size: 22px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 8px;
}
.empty-state p {
    color: #64748B;
    margin-bottom: 24px;
}
.btn-shop {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    background: var(--brand-primary);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-shop:hover {
    background: var(--brand-secondary);
    transform: translateY(-2px);
}

.pagination-container {
    margin-top: 32px;
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .transaction-header {
        flex-wrap: wrap;
    }
    .badge {
        width: 100%;
        text-align: center;
    }
}
</style>
@endpush
@endsection
