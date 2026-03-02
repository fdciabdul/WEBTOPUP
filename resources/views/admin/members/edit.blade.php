@extends('layouts.admin')

@section('title', 'Edit Member')
@section('page-title', 'Edit Member')

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
    .form-header-left { display: flex; align-items: center; gap: 14px; }
    .form-header-icon {
        width: 44px; height: 44px;
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 20px;
    }
    .form-header h2 { color: white; font-size: 18px; font-weight: 800; margin: 0; }
    .form-header p { color: rgba(255,255,255,0.75); font-size: 12px; margin-top: 2px; }
    .close-btn {
        width: 32px; height: 32px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; cursor: pointer; transition: all 0.2s;
        text-decoration: none; border: none;
    }
    .close-btn:hover { background: rgba(255,255,255,0.3); transform: scale(1.1); }
    .form-body { padding: 28px; }
    .section-title {
        font-size: 11px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: #64748B; margin-bottom: 16px;
    }
    .field-group { margin-bottom: 16px; }
    .field-label { display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px; }
    .field-input-wrap { position: relative; }
    .field-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 15px; color: #94A3B8; z-index: 2; }
    .field-input {
        width: 100%; padding: 12px 14px 12px 42px;
        border: 1.5px solid #E2E8F0; border-radius: 10px;
        font-size: 13px; font-weight: 500; color: #1E293B;
        background: #FAFBFC; transition: all 0.2s; outline: none;
    }
    .field-input:focus { border-color: #3B5BDB; background: white; box-shadow: 0 0 0 3px rgba(59,91,219,0.1); }
    .field-input::placeholder { color: #CBD5E1; }
    .field-select {
        width: 100%; padding: 12px 14px 12px 42px;
        border: 1.5px solid #E2E8F0; border-radius: 10px;
        font-size: 13px; font-weight: 600; color: #1E293B;
        background: #FAFBFC; transition: all 0.2s; outline: none;
        appearance: none; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 14px center;
    }
    .field-select:focus { border-color: #3B5BDB; background-color: white; box-shadow: 0 0 0 3px rgba(59,91,219,0.1); }
    .credential-input { background: #FFFBEB !important; border-color: #FDE68A !important; }
    .credential-input:focus { border-color: #F59E0B !important; box-shadow: 0 0 0 3px rgba(245,158,11,0.12) !important; }
    .eye-toggle {
        position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
        color: #94A3B8; cursor: pointer; font-size: 16px; z-index: 2; transition: color 0.2s;
    }
    .eye-toggle:hover { color: #64748B; }
    .stats-row {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
        margin-top: 24px; padding-top: 20px; border-top: 1.5px dashed #E2E8F0;
    }
    .stat-box {
        background: #F8FAFC; padding: 14px 16px; border-radius: 12px;
        text-align: center; border: 1px solid #F1F5F9;
    }
    .stat-box .stat-val { font-size: 16px; font-weight: 800; color: #3B5BDB; }
    .stat-box .stat-lab { font-size: 10px; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px; }
    .status-toggle { margin-top: 20px; display: flex; gap: 10px; }
    .status-option { flex: 1; cursor: pointer; }
    .status-option input { display: none; }
    .status-option .status-btn {
        display: block; padding: 12px; border-radius: 10px;
        text-align: center; font-size: 12px; font-weight: 700;
        border: 2px solid #E2E8F0; color: #64748B; transition: all 0.2s;
    }
    .status-option input:checked + .status-btn.btn-active {
        background: linear-gradient(135deg, #3B5BDB, #5B7CFA); border-color: #3B5BDB; color: white;
    }
    .status-option input:checked + .status-btn.btn-inactive {
        background: #64748B; border-color: #64748B; color: white;
    }
    .form-footer {
        padding: 20px 28px; border-top: 1px solid #F1F5F9;
        display: flex; align-items: center; justify-content: flex-end; gap: 12px; background: #FAFBFC;
    }
    .btn-cancel { padding: 10px 24px; font-size: 13px; font-weight: 600; color: #64748B; text-decoration: none; transition: color 0.2s; }
    .btn-cancel:hover { color: #1E293B; }
    .btn-save {
        padding: 12px 28px; font-size: 13px; font-weight: 700; color: white;
        background: linear-gradient(135deg, #3B5BDB, #5B7CFA); border: none; border-radius: 12px;
        cursor: pointer; display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 14px rgba(59,91,219,0.3); transition: all 0.2s;
    }
    .btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(59,91,219,0.4); }
    .btn-save:active { transform: scale(0.97); }
    .error-msg { color: #EF4444; font-size: 11px; margin-top: 4px; font-weight: 500; }
    .alert-box { padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: start; gap: 10px; font-size: 12px; font-weight: 500; }
    .alert-danger { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
    .alert-success { background: #F0FDF4; color: #166534; border: 1px solid #BBF7D0; }
    .danger-zone {
        max-width: 720px; margin: 20px auto 0;
        background: white; border: 2px solid #FEE2E2; border-radius: 1.2rem; overflow: hidden;
    }
    .danger-header { padding: 16px 24px; background: #FEF2F2; border-bottom: 1px solid #FEE2E2; }
    .danger-header h3 { font-size: 14px; font-weight: 700; color: #DC2626; display: flex; align-items: center; gap: 8px; }
    .danger-body { padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; }
    .danger-body p { font-size: 12px; color: #64748B; }
    .btn-delete {
        padding: 10px 20px; font-size: 12px; font-weight: 700; color: white; background: #EF4444;
        border: none; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 6px;
        transition: all 0.2s; box-shadow: 0 4px 12px rgba(239,68,68,0.25);
    }
    .btn-delete:hover { background: #DC2626; transform: translateY(-1px); }

    @media (max-width: 640px) {
        .form-body { padding: 20px; }
        .two-col { grid-template-columns: 1fr !important; }
        .stats-row { grid-template-columns: 1fr; }
        .form-footer { padding: 16px 20px; }
        .danger-body { flex-direction: column; gap: 12px; align-items: start; }
    }
</style>
@endpush

@section('content')
<div class="member-form-card card-anim animate-slide-up-fade">
    <!-- Header -->
    <div class="form-header">
        <div class="form-header-left">
            <div class="form-header-icon">
                <i class="ri-pencil-line"></i>
            </div>
            <div>
                <h2>Edit Member</h2>
                <p>{{ $member->name }} &bull; ID: {{ $member->id }}</p>
            </div>
        </div>
        <a href="{{ route('admin.members.index') }}" class="close-btn" title="Kembali">
            <i class="ri-close-line text-lg"></i>
        </a>
    </div>

    <form action="{{ route('admin.members.update', $member) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-body">
            @if($errors->any())
                <div class="alert-box alert-danger">
                    <i class="ri-error-warning-fill" style="color:#EF4444;font-size:16px;margin-top:1px;"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert-box alert-success">
                    <i class="ri-checkbox-circle-fill" style="color:#22C55E;font-size:16px;margin-top:1px;"></i>
                    <span>{{ session('success') }}</span>
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
                            <input type="text" name="name" value="{{ old('name', $member->name) }}" class="field-input" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        @error('name') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Alamat Email</label>
                        <div class="field-input-wrap">
                            <i class="ri-mail-line field-icon"></i>
                            <input type="email" name="email" value="{{ old('email', $member->email) }}" class="field-input" placeholder="nama@email.com" required>
                        </div>
                        @error('email') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Nomor WhatsApp</label>
                        <div class="field-input-wrap">
                            <i class="ri-whatsapp-line field-icon" style="color: #25D366;"></i>
                            <input type="text" name="phone" value="{{ old('phone', $member->phone) }}" class="field-input" placeholder="0812×xxx" required>
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
                                <option value="visitor" {{ old('level', $member->level) == 'visitor' ? 'selected' : '' }}>Member</option>
                                <option value="reseller" {{ old('level', $member->level) == 'reseller' ? 'selected' : '' }}>Reseller</option>
                                <option value="reseller_vip" {{ old('level', $member->level) == 'reseller_vip' ? 'selected' : '' }}>Reseller VIP</option>
                                <option value="reseller_vvip" {{ old('level', $member->level) == 'reseller_vvip' ? 'selected' : '' }}>Reseller VVIP</option>
                            </select>
                        </div>
                        @error('level') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label" style="display:flex;align-items:center;gap:6px;">
                            <i class="ri-lock-fill" style="font-size:13px;color:#F59E0B;"></i> Password Login
                        </label>
                        <div class="field-input-wrap">
                            <i class="ri-lock-line field-icon" style="color: #F59E0B;"></i>
                            <input type="password" name="password" id="inputPassword" class="field-input credential-input" placeholder="Kosongkan jika tidak diubah">
                            <i class="ri-eye-line eye-toggle" onclick="togglePass('inputPassword', this)"></i>
                        </div>
                        @error('password') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label" style="display:flex;align-items:center;gap:6px;">
                            <i class="ri-shield-keyhole-fill" style="font-size:13px;color:#EF4444;"></i> PIN Transaksi
                        </label>
                        <div class="field-input-wrap">
                            <i class="ri-shield-keyhole-line field-icon" style="color: #EF4444;"></i>
                            <input type="password" name="pin" id="inputPin" class="field-input credential-input" placeholder="{{ $member->pin ? '••••••' : 'Belum diatur' }}" maxlength="6" pattern="[0-9]{6}" inputmode="numeric">
                            <i class="ri-eye-line eye-toggle" onclick="togglePass('inputPin', this)"></i>
                        </div>
                        @error('pin') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <!-- Status Toggle -->
            <div class="status-toggle">
                <label class="status-option">
                    <input type="radio" name="is_active" value="1" {{ old('is_active', $member->is_active) == 1 ? 'checked' : '' }}>
                    <div class="status-btn btn-active"><i class="ri-checkbox-circle-line"></i> Aktif</div>
                </label>
                <label class="status-option">
                    <input type="radio" name="is_active" value="0" {{ old('is_active', $member->is_active) == 0 ? 'checked' : '' }}>
                    <div class="status-btn btn-inactive"><i class="ri-close-circle-line"></i> Nonaktif</div>
                </label>
            </div>

            <!-- Stats -->
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-val">Rp {{ number_format($member->balance, 0, ',', '.') }}</div>
                    <div class="stat-lab">Saldo</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val" style="color:#1E293B;">{{ $member->created_at->format('d M Y') }}</div>
                    <div class="stat-lab">Bergabung</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val" style="color:#1E293B;">{{ $member->total_transactions ?? 0 }}</div>
                    <div class="stat-lab">Total Transaksi</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="form-footer">
            <a href="{{ route('admin.members.index') }}" class="btn-cancel">Batal</a>
            <button type="submit" class="btn-save">
                <i class="ri-save-line"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<!-- Danger Zone -->
<div class="danger-zone card-anim animate-slide-up-fade">
    <div class="danger-header">
        <h3><i class="ri-error-warning-line"></i> Zona Bahaya</h3>
    </div>
    <div class="danger-body">
        <p>Hapus member ini secara permanen. Tidak dapat dibatalkan.</p>
        <form action="{{ route('admin.members.destroy', $member) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus member {{ addslashes($member->name) }}?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete">
                <i class="ri-delete-bin-line"></i> Hapus Member
            </button>
        </form>
    </div>
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
