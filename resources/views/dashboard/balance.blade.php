@extends('layouts.app')

@section('title', 'Saldo - ' . config('app.name'))

@section('content')
<div class="main-content">
    <div class="balance-container">
        <!-- Balance Header -->
        <div class="balance-header">
            <div class="balance-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="balance-info">
                <div class="balance-label">Saldo Anda</div>
                <div class="balance-amount">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Top Up Form -->
        <div class="topup-card">
            <h2 class="card-title">Top Up Saldo</h2>
            <p class="card-description">Isi saldo Anda untuk melakukan transaksi lebih mudah</p>

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('dashboard.balance.topup') }}" method="POST" class="topup-form" id="topup-form">
                @csrf

                <!-- Amount Selection -->
                <div class="form-group">
                    <label class="form-label">Pilih Nominal</label>
                    <div class="amount-grid">
                        <button type="button" class="amount-btn" onclick="selectAmount(10000)">
                            Rp 10.000
                        </button>
                        <button type="button" class="amount-btn" onclick="selectAmount(20000)">
                            Rp 20.000
                        </button>
                        <button type="button" class="amount-btn" onclick="selectAmount(50000)">
                            Rp 50.000
                        </button>
                        <button type="button" class="amount-btn" onclick="selectAmount(100000)">
                            Rp 100.000
                        </button>
                        <button type="button" class="amount-btn" onclick="selectAmount(200000)">
                            Rp 200.000
                        </button>
                        <button type="button" class="amount-btn" onclick="selectAmount(500000)">
                            Rp 500.000
                        </button>
                    </div>
                </div>

                <!-- Custom Amount Input -->
                <div class="form-group">
                    <label class="form-label">Atau Masukkan Nominal</label>
                    <div class="input-wrapper">
                        <span class="input-prefix">Rp</span>
                        <input
                            type="number"
                            name="amount"
                            id="amount-input"
                            class="form-input"
                            placeholder="Masukkan nominal"
                            min="10000"
                            required
                        >
                    </div>
                    <span class="form-hint">Minimal top up Rp 10.000</span>
                </div>

                <!-- Payment Method -->
                <div class="form-group">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="midtrans">Payment Gateway (Midtrans)</option>
                        <option value="bank_transfer">Transfer Bank</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-credit-card"></i>
                    Top Up Sekarang
                </button>
            </form>
        </div>

        <!-- Balance History -->
        <div class="history-card">
            <div class="history-header">
                <h2 class="card-title">Riwayat Saldo</h2>
                @if($balanceHistories->hasPages())
                    <a href="{{ route('dashboard.balance') }}" class="view-all">Lihat Semua</a>
                @endif
            </div>

            @if($balanceHistories->count() > 0)
                <div class="history-list">
                    @foreach($balanceHistories as $history)
                        <div class="history-item">
                            <div class="history-icon {{ $history->type }}">
                                @if($history->type === 'credit')
                                    <i class="fas fa-arrow-down"></i>
                                @else
                                    <i class="fas fa-arrow-up"></i>
                                @endif
                            </div>
                            <div class="history-details">
                                <div class="history-title">
                                    {{ $history->description ?? ($history->type === 'credit' ? 'Top Up Saldo' : 'Pembayaran') }}
                                </div>
                                <div class="history-date">{{ $history->created_at->format('d M Y H:i') }}</div>
                                @if($history->reference_type)
                                    <div class="history-ref">Ref: {{ $history->reference_type }} #{{ $history->reference_id }}</div>
                                @endif
                            </div>
                            <div class="history-amount {{ $history->type }}">
                                @if($history->type === 'credit')
                                    + Rp {{ number_format($history->amount, 0, ',', '.') }}
                                @else
                                    - Rp {{ number_format($history->amount, 0, ',', '.') }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($balanceHistories->hasPages())
                    <div class="pagination-container">
                        {{ $balanceHistories->links() }}
                    </div>
                @endif
            @else
                <div class="empty-history">
                    <div class="empty-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <p>Belum ada riwayat transaksi</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.main-content { padding: 20px; }
.balance-container { max-width: 700px; margin: 0 auto; }

.balance-header {
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
    padding: 32px 24px;
    border-radius: 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
    box-shadow: 0 8px 24px rgba(0,51,170,0.2);
}
.balance-icon {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
}
.balance-label {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 6px;
}
.balance-amount {
    font-size: 32px;
    font-weight: 700;
}

.topup-card, .history-card {
    background: white;
    padding: 28px;
    border-radius: 20px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}
.card-title {
    font-size: 20px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 8px;
}
.card-description {
    color: #64748B;
    font-size: 14px;
    margin-bottom: 24px;
}

.alert {
    padding: 14px 16px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    gap: 12px;
    align-items: start;
}
.alert-error {
    background: #FEE2E2;
    color: #991B1B;
}
.alert i {
    font-size: 18px;
    margin-top: 2px;
}

.form-group {
    margin-bottom: 24px;
}
.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 12px;
}
.amount-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
.amount-btn {
    padding: 14px;
    background: #F8FAFC;
    border: 2px solid #E2E8F0;
    border-radius: 12px;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all 0.3s;
}
.amount-btn:hover {
    border-color: var(--brand-primary);
    color: var(--brand-primary);
    background: #EFF6FF;
}
.amount-btn.active {
    border-color: var(--brand-primary);
    background: var(--brand-primary);
    color: white;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.input-prefix {
    position: absolute;
    left: 16px;
    font-weight: 600;
    color: #64748B;
}
.form-input, .form-select {
    width: 100%;
    padding: 14px 16px 14px 40px;
    border: 2px solid #E2E8F0;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s;
}
.form-select {
    padding-left: 16px;
}
.form-input:focus, .form-select:focus {
    outline: none;
    border-color: var(--brand-primary);
}
.form-hint {
    display: block;
    font-size: 12px;
    color: #94A3B8;
    margin-top: 6px;
}

.btn-submit {
    width: 100%;
    padding: 16px;
    background: var(--brand-primary);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
}
.btn-submit:hover {
    background: var(--brand-secondary);
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,51,170,0.2);
}

.history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.view-all {
    color: var(--brand-primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
}

.history-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.history-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    background: #F8FAFC;
    border-radius: 12px;
    transition: all 0.3s;
}
.history-item:hover {
    background: #F1F5F9;
}
.history-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.history-icon.credit {
    background: #D1FAE5;
    color: #059669;
}
.history-icon.debit {
    background: #FEE2E2;
    color: #DC2626;
}
.history-details {
    flex: 1;
}
.history-title {
    font-size: 14px;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 4px;
}
.history-date {
    font-size: 12px;
    color: #94A3B8;
}
.history-ref {
    font-size: 11px;
    color: #CBD5E1;
    margin-top: 2px;
}
.history-amount {
    font-size: 15px;
    font-weight: 700;
}
.history-amount.credit {
    color: #059669;
}
.history-amount.debit {
    color: #DC2626;
}

.empty-history {
    text-align: center;
    padding: 60px 20px;
}
.empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 16px;
    background: #F1F5F9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: #CBD5E1;
}
.empty-history p {
    color: #94A3B8;
}

.pagination-container {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .amount-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endpush

@push('scripts')
<script>
function selectAmount(amount) {
    document.getElementById('amount-input').value = amount;

    // Update button states
    document.querySelectorAll('.amount-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
}

// Remove active state when typing custom amount
document.getElementById('amount-input').addEventListener('input', function() {
    document.querySelectorAll('.amount-btn').forEach(btn => {
        btn.classList.remove('active');
    });
});
</script>
@endpush
@endsection
