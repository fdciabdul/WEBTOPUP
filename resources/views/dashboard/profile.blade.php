@extends('layouts.app')

@section('title', 'Profil - ' . config('app.name'))

@section('content')
<div class="main-content">
    <div class="profile-container">
        <div class="page-header">
            <h1>Profil Saya</h1>
            <p>Kelola informasi dan keamanan akun Anda</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

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

        <!-- Profile Information -->
        <div class="profile-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-user"></i>
                    Informasi Profil
                </h2>
            </div>

            <form action="{{ route('dashboard.profile.update') }}" method="POST" class="profile-form">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', auth()->user()->name) }}"
                            class="form-input"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', auth()->user()->email) }}"
                            class="form-input"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input
                        type="tel"
                        name="phone"
                        value="{{ old('phone', auth()->user()->phone) }}"
                        class="form-input"
                        placeholder="08xxxxxxxxxx"
                    >
                    <span class="form-hint">Format: 08xxxxxxxxxx</span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Stats -->
        <div class="stats-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-line"></i>
                    Statistik Akun
                </h2>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3B82F6, #2563EB);">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Level</div>
                        <div class="stat-value">{{ ucfirst(str_replace('_', ' ', auth()->user()->level)) }}</div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10B981, #059669);">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Transaksi</div>
                        <div class="stat-value">{{ auth()->user()->total_transactions }}</div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Pengeluaran</div>
                        <div class="stat-value">Rp {{ number_format(auth()->user()->total_spending, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED);">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Bergabung</div>
                        <div class="stat-value">{{ auth()->user()->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="profile-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-lock"></i>
                    Ubah Password
                </h2>
            </div>

            <form action="{{ route('dashboard.profile.change-password') }}" method="POST" class="profile-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Password Lama</label>
                    <input
                        type="password"
                        name="current_password"
                        class="form-input"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input
                        type="password"
                        name="password"
                        class="form-input"
                        required
                    >
                    <span class="form-hint">Minimal 8 karakter</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-input"
                        required
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i>
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Actions -->
        <div class="profile-card danger-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Zona Bahaya
                </h2>
            </div>

            <div class="danger-content">
                <div class="danger-item">
                    <div class="danger-info">
                        <h4>Logout dari Semua Perangkat</h4>
                        <p>Logout dari semua sesi aktif kecuali perangkat ini</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>

                <div class="danger-item">
                    <div class="danger-info">
                        <h4>Hapus Akun</h4>
                        <p>Hapus akun Anda secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <button class="btn btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash"></i>
                        Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.main-content { padding: 20px; }
.profile-container { max-width: 800px; margin: 0 auto; }
.page-header {
    margin-bottom: 28px;
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

.alert {
    padding: 14px 16px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    gap: 12px;
    align-items: start;
}
.alert-success {
    background: #D1FAE5;
    color: #065F46;
}
.alert-error {
    background: #FEE2E2;
    color: #991B1B;
}
.alert i {
    font-size: 18px;
    margin-top: 2px;
}

.profile-card, .stats-card {
    background: white;
    border-radius: 16px;
    padding: 28px;
    margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}
.danger-card {
    border: 2px solid #FEE2E2;
}
.card-header {
    margin-bottom: 24px;
}
.card-title {
    font-size: 18px;
    font-weight: 700;
    color: #1E293B;
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-form .form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}
.form-group {
    margin-bottom: 20px;
}
.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 8px;
}
.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #E2E8F0;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s;
}
.form-input:focus {
    outline: none;
    border-color: var(--brand-primary);
}
.form-hint {
    display: block;
    font-size: 12px;
    color: #94A3B8;
    margin-top: 6px;
}

.form-actions {
    margin-top: 24px;
}
.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    text-decoration: none;
}
.btn-primary {
    background: var(--brand-primary);
    color: white;
}
.btn-primary:hover {
    background: var(--brand-secondary);
    transform: translateY(-2px);
}
.btn-warning {
    background: #F59E0B;
    color: white;
}
.btn-warning:hover {
    background: #D97706;
}
.btn-danger {
    background: #EF4444;
    color: white;
}
.btn-danger:hover {
    background: #DC2626;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}
.stat-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    background: #F8FAFC;
    border-radius: 12px;
}
.stat-icon {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 22px;
}
.stat-label {
    font-size: 12px;
    color: #64748B;
    margin-bottom: 4px;
}
.stat-value {
    font-size: 16px;
    font-weight: 700;
    color: #1E293B;
}

.danger-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.danger-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #FEF2F2;
    border-radius: 12px;
    border: 1px solid #FEE2E2;
}
.danger-info h4 {
    font-size: 15px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 4px;
}
.danger-info p {
    font-size: 13px;
    color: #64748B;
}

@media (max-width: 768px) {
    .profile-form .form-row {
        grid-template-columns: 1fr;
    }
    .stats-grid {
        grid-template-columns: 1fr;
    }
    .danger-item {
        flex-direction: column;
        gap: 12px;
        align-items: start;
    }
    .danger-item .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan!')) {
        if (confirm('Konfirmasi sekali lagi. Semua data Anda akan dihapus permanen!')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("dashboard.profile.delete") }}';
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    }
}
</script>
@endpush
@endsection
