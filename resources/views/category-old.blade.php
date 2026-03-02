@extends('layouts.main')

@section('title', $category->name . ' - ' . config('app.name'))

@section('content')
<div class="main-content">
    <div class="category-header">
        <div class="category-info">
            @if($category->icon)
                <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="category-icon-large">
            @endif
            <div>
                <h1 class="category-title">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="category-description">{{ $category->description }}</p>
                @endif
                <p class="category-count">{{ $products->total() }} produk tersedia</p>
            </div>
        </div>
    </div>

    <div class="products-section">
        @if($products->count() > 0)
            <div class="products-grid">
                @foreach($products as $product)
                    <a href="{{ route('product.show', $product->slug) }}" class="product-card">
                        <div class="product-image">
                            <img src="{{ asset('storage/' . ($product->image ?? 'default-product.png')) }}" alt="{{ $product->name }}">
                            @if(!$product->isInStock())
                                <div class="out-of-stock-badge">Stok Habis</div>
                            @endif
                        </div>
                        <div class="product-details">
                            <h3 class="product-name">{{ $product->name }}</h3>
                            @if($product->description)
                                <p class="product-desc">{{ Str::limit($product->description, 60) }}</p>
                            @endif
                            <div class="product-footer">
                                <div class="product-price">
                                    Rp {{ number_format($product->getPriceByLevel(auth()->check() ? auth()->user()->level : 'visitor'), 0, ',', '.') }}
                                </div>
                                @if($product->isInStock())
                                    <button class="btn-buy">Beli</button>
                                @else
                                    <button class="btn-buy" disabled>Habis</button>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $products->links() }}
            </div>
        @else
            <div class="no-products">
                <i class="fas fa-box-open"></i>
                <p>Belum ada produk di kategori ini</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.main-content { padding: 20px; }
.category-header {
    background: white;
    padding: 24px;
    border-radius: 16px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.category-info {
    display: flex;
    gap: 20px;
    align-items: center;
}
.category-icon-large {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    object-fit: cover;
}
.category-title {
    font-size: 28px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 8px;
}
.category-description {
    color: #64748B;
    margin-bottom: 8px;
    line-height: 1.6;
}
.category-count {
    color: #94A3B8;
    font-size: 14px;
}
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,94,184,0.15);
}
.product-image {
    width: 100%;
    height: 160px;
    position: relative;
    overflow: hidden;
}
.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.out-of-stock-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #EF4444;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.product-details {
    padding: 16px;
}
.product-name {
    font-size: 15px;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.product-desc {
    font-size: 12px;
    color: #64748B;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.product-price {
    font-size: 16px;
    font-weight: 700;
    color: var(--brand-primary);
}
.btn-buy {
    background: var(--brand-primary);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-buy:hover:not(:disabled) {
    background: var(--brand-secondary);
    transform: scale(1.05);
}
.btn-buy:disabled {
    background: #94A3B8;
    cursor: not-allowed;
}
.no-products {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
}
.no-products i {
    font-size: 48px;
    color: #CBD5E1;
    margin-bottom: 16px;
}
.no-products p {
    color: #64748B;
    font-size: 16px;
}
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 24px;
}
</style>
@endpush
@endsection
