@extends('layouts.main')

@section('title', 'Hasil Pencarian - ' . config('app.name'))

@section('content')
<div class="search-page">
    <!-- Search Header -->
    <div class="search-page-header">
        <h1>Hasil Pencarian</h1>
        <p class="search-query">
            Menampilkan hasil untuk: <strong>"{{ $query }}"</strong>
        </p>
        <p class="search-count">{{ $products->total() }} produk ditemukan</p>
    </div>

    <!-- Search Form -->
    <div class="search-form-wrap">
        <form action="{{ route('search') }}" method="GET" class="search-form">
            <div class="search-input-group">
                <i class="fas fa-search search-icon-left"></i>
                <input type="text" name="q" value="{{ $query }}" placeholder="Cari produk, game, voucher..." required>
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>

    <!-- Products Grid (Matching Homepage Style) -->
    @if($products->count() > 0)
        <div class="search-grid">
            @foreach($products as $product)
                <a href="{{ route('category', $product['slug'] ?? '#') }}" class="search-card">
                    <div class="search-card-icon">
                        <img src="{{ $product['image'] ?? '/images/default-game.png' }}" alt="{{ $product['name'] ?? '' }}" onerror="this.src='/images/default-game.png'">
                    </div>
                    <div class="search-card-name">{{ $product['name'] ?? '-' }}</div>
                    @if(!empty($product['is_popular']))
                        <div class="search-badge">Popular</div>
                    @endif
                </a>
            @endforeach
        </div>

        @if($products->hasPages())
            <div class="search-pagination">
                {{ $products->appends(['q' => $query])->links() }}
            </div>
        @endif
    @else
        <div class="search-empty">
            <div class="search-empty-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Produk tidak ditemukan</h3>
            <p>Maaf, kami tidak dapat menemukan produk yang Anda cari.</p>
            <p>Coba gunakan kata kunci yang berbeda atau lihat kategori produk kami.</p>
            <a href="{{ route('home') }}" class="btn-home">
                <i class="fas fa-home"></i>
                Kembali ke Home
            </a>
        </div>
    @endif
</div>

@push('styles')
<style>
.search-page {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}
.search-page-header {
    text-align: center;
    margin-bottom: 24px;
}
.search-page-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 8px;
}
.search-query {
    font-size: 15px;
    color: #64748B;
    margin-bottom: 6px;
}
.search-query strong {
    color: var(--brand-primary);
}
.search-count {
    font-size: 13px;
    color: #94A3B8;
}
.search-form-wrap {
    max-width: 600px;
    margin: 0 auto 30px;
}
.search-input-group {
    position: relative;
    display: flex;
    background: white;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    overflow: hidden;
}
.search-icon-left {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #94A3B8;
    font-size: 16px;
    z-index: 1;
}
.search-input-group input {
    flex: 1;
    padding: 14px 14px 14px 48px;
    border: none;
    font-size: 15px;
    outline: none;
    background: transparent;
    font-family: inherit;
}
.search-input-group button {
    padding: 14px 22px;
    background: var(--brand-primary);
    color: white;
    border: none;
    cursor: pointer;
    transition: background 0.3s;
    font-size: 15px;
}
.search-input-group button:hover {
    background: var(--brand-secondary);
}

/* Grid — matches homepage .game-grid */
.search-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 36px 12px;
    padding: 0;
    margin-bottom: 40px;
}
.search-card {
    background: transparent;
    text-align: center;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    position: relative;
    text-decoration: none;
    transition: transform 0.2s;
}
.search-card:active {
    transform: scale(0.95);
}
.search-card-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 12px;
    border-radius: var(--radius-icon, 22px);
    overflow: hidden;
    background: #FFFFFF;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06), inset 0 0 0 1px rgba(0,0,0,0.04);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.search-card:hover .search-card-icon {
    border-color: var(--brand-primary);
    box-shadow: 0 8px 24px rgba(0,51,170,0.15);
    transform: translateY(-4px);
}
.search-card-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 2;
    position: relative;
}
.search-card-name {
    font-size: 11px;
    font-weight: 500;
    color: #000000;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 4px;
    letter-spacing: -0.01em;
}
.search-badge {
    position: absolute;
    top: -4px;
    right: calc(50% - 56px);
    background: #EF4444;
    color: white;
    padding: 2px 6px;
    border-radius: 6px;
    font-size: 9px;
    font-weight: 700;
}

/* Empty State */
.search-empty {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}
.search-empty-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 24px;
    background: linear-gradient(135deg, #F1F5F9, #E2E8F0);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 42px;
    color: #94A3B8;
}
.search-empty h3 {
    font-size: 22px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 12px;
}
.search-empty p {
    color: #64748B;
    line-height: 1.6;
    margin-bottom: 6px;
    font-size: 14px;
}
.btn-home {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--brand-primary);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    margin-top: 20px;
    transition: all 0.3s;
    font-size: 14px;
}
.btn-home:hover {
    background: var(--brand-secondary);
    transform: translateY(-2px);
}
.search-pagination {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

/* Small Mobile */
@media (max-width: 360px) {
    .search-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px 8px;
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .search-grid {
        grid-template-columns: repeat(8, 1fr);
        gap: 24px 16px;
    }
    .search-card-icon {
        width: 110px;
        height: 110px;
    }
    .search-card-name {
        font-size: 13px;
        font-weight: 600;
    }
    .search-page-header h1 {
        font-size: 28px;
    }
}
</style>
@endpush
@endsection
