@extends('layouts.app')

@section('title', 'Bonus File - ' . config('app.name'))

@section('content')
<div class="main-content">
    <div class="bonus-container">
        <div class="page-header">
            <h1>Bonus File</h1>
            <p>Download file bonus eksklusif untuk member</p>
        </div>

        @if($bonusFiles->count() > 0)
            <div class="bonus-grid">
                @foreach($bonusFiles as $file)
                    <div class="bonus-card">
                        <div class="bonus-icon">
                            @if(str_ends_with($file->file_path, '.pdf'))
                                <i class="fas fa-file-pdf"></i>
                            @elseif(str_ends_with($file->file_path, '.zip') || str_ends_with($file->file_path, '.rar'))
                                <i class="fas fa-file-archive"></i>
                            @elseif(str_ends_with($file->file_path, '.jpg') || str_ends_with($file->file_path, '.jpeg') || str_ends_with($file->file_path, '.png') || str_ends_with($file->file_path, '.gif'))
                                <i class="fas fa-file-image"></i>
                            @elseif(str_ends_with($file->file_path, '.doc') || str_ends_with($file->file_path, '.docx'))
                                <i class="fas fa-file-word"></i>
                            @elseif(str_ends_with($file->file_path, '.xls') || str_ends_with($file->file_path, '.xlsx'))
                                <i class="fas fa-file-excel"></i>
                            @else
                                <i class="fas fa-file"></i>
                            @endif
                        </div>

                        <div class="bonus-content">
                            <h3 class="bonus-title">{{ $file->title }}</h3>
                            <p class="bonus-description">{{ $file->description }}</p>

                            <div class="bonus-meta">
                                @if($file->file_size)
                                    <span class="meta-item">
                                        <i class="fas fa-hdd"></i>
                                        {{ $file->file_size }} MB
                                    </span>
                                @endif
                                <span class="meta-item">
                                    <i class="fas fa-download"></i>
                                    {{ $file->download_count }} downloads
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    {{ $file->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Access Level Badge -->
                            <div class="bonus-level">
                                <span class="level-badge badge-{{ $file->min_level }}">
                                    Minimal: {{ ucfirst(str_replace('_', ' ', $file->min_level)) }}
                                </span>
                            </div>
                        </div>

                        <div class="bonus-actions">
                            @if($file->canBeDownloadedBy(auth()->user()))
                                <a href="{{ route('dashboard.bonus-file.download', $file) }}" class="btn-download">
                                    <i class="fas fa-download"></i>
                                    Download
                                </a>
                            @else
                                <button class="btn-locked" disabled>
                                    <i class="fas fa-lock"></i>
                                    Terkunci
                                </button>
                                <p class="locked-message">Upgrade ke {{ ucfirst(str_replace('_', ' ', $file->min_level)) }} untuk download</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($bonusFiles->hasPages())
                <div class="pagination-container">
                    {{ $bonusFiles->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-gift"></i>
                </div>
                <h3>Belum ada bonus file</h3>
                <p>Bonus file akan muncul di sini ketika tersedia</p>
            </div>
        @endif

        <!-- Level Info Card -->
        <div class="level-info-card">
            <h3>Level Anda: <span class="current-level">{{ ucfirst(str_replace('_', ' ', auth()->user()->level)) }}</span></h3>
            <p>Upgrade level untuk mendapatkan akses ke lebih banyak bonus file eksklusif!</p>

            <div class="level-grid">
                <div class="level-item {{ auth()->user()->level === 'visitor' ? 'active' : '' }}">
                    <div class="level-name">Visitor</div>
                    <div class="level-desc">Level dasar</div>
                </div>
                <div class="level-item {{ auth()->user()->level === 'reseller' ? 'active' : '' }}">
                    <div class="level-name">Reseller</div>
                    <div class="level-desc">Akses lebih banyak</div>
                </div>
                <div class="level-item {{ auth()->user()->level === 'reseller_vip' ? 'active' : '' }}">
                    <div class="level-name">Reseller VIP</div>
                    <div class="level-desc">Akses premium</div>
                </div>
                <div class="level-item {{ auth()->user()->level === 'reseller_vvip' ? 'active' : '' }}">
                    <div class="level-name">Reseller VVIP</div>
                    <div class="level-desc">Akses eksklusif</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.main-content { padding: 20px; }
.bonus-container { max-width: 1000px; margin: 0 auto; }
.page-header {
    margin-bottom: 32px;
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

.bonus-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}
.bonus-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    transition: all 0.3s;
}
.bonus-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}
.bonus-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: white;
    margin-bottom: 16px;
}
.bonus-title {
    font-size: 18px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 8px;
}
.bonus-description {
    font-size: 14px;
    color: #64748B;
    line-height: 1.6;
    margin-bottom: 16px;
}
.bonus-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid #F1F5F9;
}
.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #94A3B8;
}
.meta-item i {
    font-size: 13px;
}

.bonus-level {
    margin-bottom: 16px;
}
.level-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
}
.badge-visitor { background: #E2E8F0; color: #475569; }
.badge-reseller { background: #DBEAFE; color: #1E40AF; }
.badge-reseller_vip { background: #FEF3C7; color: #92400E; }
.badge-reseller_vvip { background: #FEE2E2; color: #991B1B; }

.bonus-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.btn-download, .btn-locked {
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
    text-decoration: none;
    border: none;
    cursor: pointer;
}
.btn-download {
    background: var(--brand-primary);
    color: white;
}
.btn-download:hover {
    background: var(--brand-secondary);
    transform: scale(1.02);
}
.btn-locked {
    background: #F1F5F9;
    color: #94A3B8;
    cursor: not-allowed;
}
.locked-message {
    text-align: center;
    font-size: 12px;
    color: #EF4444;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 20px;
    margin-bottom: 32px;
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
}

.level-info-card {
    background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
    padding: 28px;
    border-radius: 20px;
    border: 2px solid #3B82F6;
}
.level-info-card h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1E40AF;
    margin-bottom: 8px;
}
.current-level {
    color: #2563EB;
}
.level-info-card p {
    color: #1E40AF;
    margin-bottom: 20px;
    font-size: 14px;
}
.level-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
}
.level-item {
    background: white;
    padding: 16px;
    border-radius: 12px;
    text-align: center;
    border: 2px solid transparent;
    transition: all 0.3s;
}
.level-item.active {
    border-color: #2563EB;
    background: #EFF6FF;
}
.level-name {
    font-size: 13px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 4px;
}
.level-desc {
    font-size: 11px;
    color: #64748B;
}

.pagination-container {
    margin-top: 32px;
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .bonus-grid {
        grid-template-columns: 1fr;
    }
    .level-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endpush
@endsection
