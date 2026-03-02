@extends('layouts.admin')

@section('title', 'Tambah Member')
@section('page-title', 'Tambah Member')

@push('styles')
<style>
    .member-form-card {
        max-width: 720px;
        margin: 0 auto;
        background: white;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 20px 50px -12px rgba(0,0,0,0.12);
    }
    .form-header {
        background: linear-gradient(135deg, #3B5BDB 0%, #5B7CFA 50%, #748FFC 100%);
        padding: 24px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    .form-header::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .form-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .form-header-icon {
        width: 44px;
        height: 44px;
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .form-header h2 {
        color: white;
        font-size: 18px;
        font-weight: 800;
        margin: 0;
    }
    .form-header p {
        color: rgba(255,255,255,0.75);
        font-size: 12px;
        margin-top: 2px;
    }
    .close-btn {
        width: 32px;
        height: 32px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
    }
    .close-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }
    .form-body {
        padding: 28px;
    }
    .section-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748B;
        margin-bottom: 16px;
    }
    .field-group {
        margin-bottom: 16px;
    }
    .field-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .field-input-wrap {
        position: relative;
    }
    .field-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 15px;
        color: #94A3B8;
        z-index: 2;
    }
    .field-input {
        width: 100%;
        padding: 12px 14px 12px 42px;
        border: 1.5px solid #E2E8F0;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        color: #1E293B;
        background: #FAFBFC;
        transition: all 0.2s;
        outline: none;
    }
    .field-input:focus {
        border-color: #3B5BDB;
        background: white;
        box-shadow: 0 0 0 3px rgba(59,91,219,0.1);
    }
    .field-input::placeholder {
        color: #CBD5E1;
    }
    .field-select {
        width: 100%;
        padding: 12px 14px 12px 42px;
        border: 1.5px solid #E2E8F0;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #1E293B;
        background: #FAFBFC;
        transition: all 0.2s;
        outline: none;
        appearance: none;
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
    }
    .field-select:focus {
        border-color: #3B5BDB;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(59,91,219,0.1);
    }
    .credential-input {
        background: #FFFBEB !important;
        border-color: #FDE68A !important;
    }
    .credential-input:focus {
        border-color: #F59E0B !important;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.12) !important;
    }
    .label-lock { color: #F59E0B; }
    .label-shield { color: #EF4444; }
    .eye-toggle {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        cursor: pointer;
        font-size: 16px;
        z-index: 2;
        transition: color 0.2s;
    }
    .eye-toggle:hover { color: #64748B; }
    .saldo-section {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1.5px dashed #E2E8F0;
    }
    .saldo-input-wrap {
        position: relative;
    }
    .saldo-prefix {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        font-weight: 700;
        color: #64748B;
        z-index: 2;
    }
    .saldo-input {
        width: 100%;
        padding: 14px 16px 14px 42px;
        border: 1.5px solid #E2E8F0;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        color: #1E293B;
        background: #FAFBFC;
        transition: all 0.2s;
        outline: none;
    }
    .saldo-input:focus {
        border-color: #3B5BDB;
        background: white;
        box-shadow: 0 0 0 3px rgba(59,91,219,0.1);
    }
    .form-footer {
        padding: 20px 28px;
        border-top: 1px solid #F1F5F9;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        background: #FAFBFC;
    }
    .btn-cancel {
        padding: 10px 24px;
        font-size: 13px;
        font-weight: 600;
        color: #64748B;
        background: none;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: color 0.2s;
    }
    .btn-cancel:hover { color: #1E293B; }
    .btn-save {
        padding: 12px 28px;
        font-size: 13px;
        font-weight: 700;
        color: white;
        background: linear-gradient(135deg, #3B5BDB, #5B7CFA);
        border: none;
        border-radius: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(59,91,219,0.3);
        transition: all 0.2s;
    }
    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(59,91,219,0.4);
    }
    .btn-save:active { transform: scale(0.97); }
    .error-msg {
        color: #EF4444;
        font-size: 11px;
        margin-top: 4px;
        font-weight: 500;
    }
    .alert-box {
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: start;
        gap: 10px;
        font-size: 12px;
        font-weight: 500;
    }
    .alert-danger { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
    .alert-danger i { color: #EF4444; font-size: 16px; margin-top: 1px; }

    @media (max-width: 640px) {
        .form-body { padding: 20px; }
        .two-col { grid-template-columns: 1fr !important; }
        .form-footer { padding: 16px 20px; }
    }
</style>
@endpush

@section('content')
<div class="member-form-card card-anim animate-slide-up-fade">
    <!-- Header -->
    <div class="form-header">
        <div class="form-header-left">
            <div class="form-header-icon">
                <i class="ri-user-add-line"></i>
            </div>
            <div>
                <h2>Tambah Member Baru</h2>
                <p>Lengkapi data informasi member di bawah ini</p>
            </div>
        </div>
        <a href="{{ route('admin.members.index') }}" class="close-btn" title="Kembali">
            <i class="ri-close-line text-lg"></i>
        </a>
    </div>

    <form action="{{ route('admin.members.store') }}" method="POST">
        @csrf

        <div class="form-body">
            @if($errors->any())
                <div class="alert-box alert-danger">
                    <i class="ri-error-warning-fill"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Two Column Layout -->
            <div class="two-col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 28px;">
                <!-- Left: Informasi Pribadi -->
                <div>
                    <div class="section-title">Informasi Pribadi</div>

                    <div class="field-group">
                        <label class="field-label">Nama Lengkap</label>
                        <div class="field-input-wrap">
                            <i class="ri-user-line field-icon"></i>
                            <input type="text" name="name" value="{{ old('name') }}" class="field-input" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        @error('name') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Alamat Email</label>
                        <div class="field-input-wrap">
                            <i class="ri-mail-line field-icon"></i>
                            <input type="email" name="email" value="{{ old('email') }}" class="field-input" placeholder="nama@email.com" required>
                        </div>
                        @error('email') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Nomor WhatsApp</label>
                        <div class="field-input-wrap">
                            <i class="ri-whatsapp-line field-icon" style="color: #25D366;"></i>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="field-input" placeholder="0812×xxx" required>
                        </div>
                        @error('phone') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Right: Akun & Keamanan -->
                <div>
                    <div class="section-title">Akun & Keamanan</div>

                    <div class="field-group">
                        <label class="field-label">Level Akses</label>
                        <div class="field-input-wrap">
                            <i class="ri-vip-crown-line field-icon" style="color: #F59E0B;"></i>
                            <select name="level" class="field-select" required>
                                <option value="visitor" {{ old('level') == 'visitor' ? 'selected' : '' }}>Member</option>
                                <option value="reseller" {{ old('level') == 'reseller' ? 'selected' : '' }}>Reseller</option>
                                <option value="reseller_vip" {{ old('level') == 'reseller_vip' ? 'selected' : '' }}>Reseller VIP</option>
                                <option value="reseller_vvip" {{ old('level') == 'reseller_vvip' ? 'selected' : '' }}>Reseller VVIP</option>
                            </select>
                        </div>
                        @error('level') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label" style="display:flex;align-items:center;gap:6px;">
                            <i class="ri-lock-fill label-lock" style="font-size:13px;"></i> Password Login
                        </label>
                        <div class="field-input-wrap">
                            <i class="ri-lock-line field-icon" style="color: #F59E0B;"></i>
                            <input type="password" name="password" id="inputPassword" class="field-input credential-input" placeholder="••••••" required>
                            <i class="ri-eye-line eye-toggle" onclick="togglePass('inputPassword', this)"></i>
                        </div>
                        @error('password') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label" style="display:flex;align-items:center;gap:6px;">
                            <i class="ri-shield-keyhole-fill label-shield" style="font-size:13px;"></i> PIN Transaksi
                        </label>
                        <div class="field-input-wrap">
                            <i class="ri-shield-keyhole-line field-icon" style="color: #EF4444;"></i>
                            <input type="password" name="pin" id="inputPin" class="field-input credential-input" placeholder="••••••" maxlength="6" pattern="[0-9]{6}" inputmode="numeric">
                            <i class="ri-eye-line eye-toggle" onclick="togglePass('inputPin', this)"></i>
                        </div>
                        @error('pin') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <!-- Saldo Awal -->
            <div class="saldo-section">
                <div class="section-title">Saldo Awal (Opsional)</div>
                <div class="saldo-input-wrap">
                    <span class="saldo-prefix">Rp</span>
                    <input type="number" name="balance" value="{{ old('balance', 0) }}" min="0" step="1000" class="saldo-input" placeholder="0">
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="form-footer">
            <a href="{{ route('admin.members.index') }}" class="btn-cancel">Batal</a>
            <button type="submit" class="btn-save">
                <i class="ri-save-line"></i> Simpan Data Member
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function togglePass(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
    } else {
        input.type = 'password';
        icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
    }
}
</script>
@endpush
@endsection
