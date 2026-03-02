@extends('layouts.main')

@section('title', $category->name . ' - ' . config('app.name'))

@section('content')
<div class="main-content">
    <!-- Category Header -->
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
            </div>
        </div>
    </div>

    <!-- Step Indicator -->
    <div class="steps-container">
        <div class="step active" id="step-1">
            <div class="step-number">1</div>
            <div class="step-label">Masukkan User ID</div>
        </div>
        <div class="step-line"></div>
        <div class="step" id="step-2">
            <div class="step-number">2</div>
            <div class="step-label">Pilih Nominal</div>
        </div>
        <div class="step-line"></div>
        <div class="step" id="step-3">
            <div class="step-number">3</div>
            <div class="step-label">Pembayaran</div>
        </div>
    </div>

    <!-- Step 1: User ID Verification -->
    <div class="verification-section" id="verification-section">
        <div class="verification-card">
            <h2>🎮 Verifikasi Akun Game</h2>
            <p class="verification-subtitle">Masukkan User ID untuk melanjutkan pembelian</p>

            <form id="verification-form">
                <div class="form-group">
                    <label for="user_id">User ID / Game ID *</label>
                    <input type="text" id="user_id" name="user_id" class="form-input"
                           placeholder="Contoh: 123456789" required>
                    <small>Pastikan User ID yang Anda masukkan sudah benar</small>
                </div>

                @if(in_array($category->game_code, ['ml', 'aov', 'sausageman']))
                <div class="form-group">
                    <label for="zone_id">Zone ID / Server ID *</label>
                    <input type="text" id="zone_id" name="zone_id" class="form-input"
                           placeholder="Contoh: 1234" required>
                </div>
                @endif

                <button type="submit" class="btn btn-primary btn-verify">
                    <i class="fas fa-check-circle"></i>
                    Verifikasi User ID
                </button>
            </form>

            <!-- Verification Result -->
            <div id="verification-result" class="verification-result" style="display:none;">
                <div class="account-verified">
                    <i class="fas fa-check-circle"></i>
                    <div class="account-info">
                        <strong id="account-username">-</strong>
                        <span id="account-userid">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Product Selection (Hidden initially) -->
    <div class="products-section" id="products-section" style="display:none;">
        <h2 class="section-title">💎 Pilih Nominal</h2>

        @if($products->count() > 0)
            <div class="products-grid">
                @foreach($products as $product)
                    <div class="product-card {{ !$product->isInStock() ? 'out-of-stock' : '' }}"
                         data-product-id="{{ $product->id }}"
                         data-product-name="{{ $product->name }}"
                         data-product-price="{{ $product->getPriceByLevel(auth()->check() ? auth()->user()->level : 'visitor') }}"
                         onclick="selectProduct(this)">
                        <div class="product-badge">{{ $product->name }}</div>
                        <div class="product-price">
                            Rp {{ number_format($product->getPriceByLevel(auth()->check() ? auth()->user()->level : 'visitor'), 0, ',', '.') }}
                        </div>
                        @if($product->isInStock())
                            <div class="product-stock">✓ Tersedia</div>
                        @else
                            <div class="product-stock out">✗ Stok Habis</div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="order-summary" id="order-summary" style="display:none;">
                <h3>📋 Ringkasan Pesanan</h3>
                <div class="summary-item">
                    <span>Game:</span>
                    <strong>{{ $category->name }}</strong>
                </div>
                <div class="summary-item">
                    <span>User ID:</span>
                    <strong id="summary-userid">-</strong>
                </div>
                <div class="summary-item">
                    <span>Username:</span>
                    <strong id="summary-username">-</strong>
                </div>
                <div class="summary-item">
                    <span>Produk:</span>
                    <strong id="summary-product">-</strong>
                </div>
                <div class="summary-item total">
                    <span>Total Bayar:</span>
                    <strong id="summary-price">Rp 0</strong>
                </div>

                <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                    @csrf
                    <input type="hidden" name="product_id" id="selected_product_id">
                    <input type="hidden" name="customer_no" id="customer_no">
                    <input type="hidden" name="zone_id" id="customer_zone_id">
                    <input type="hidden" name="verified_username" id="verified_username">
                    <input type="hidden" name="category_id" value="{{ $category->id }}">

                    <div class="form-group">
                        <label for="customer_phone">No. WhatsApp *</label>
                        <input type="tel" id="customer_phone" name="customer_phone"
                               value="{{ auth()->check() ? auth()->user()->phone : old('customer_phone') }}"
                               placeholder="08123456789" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="customer_email">Email (Opsional)</label>
                        <input type="email" id="customer_email" name="customer_email"
                               value="{{ auth()->check() ? auth()->user()->email : old('customer_email') }}"
                               class="form-input">
                    </div>

                    <button type="submit" class="btn btn-primary btn-checkout">
                        <i class="fas fa-shopping-cart"></i>
                        Lanjutkan Pembayaran
                    </button>
                </form>
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
.main-content {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Category Header */
.category-header {
    background: white;
    padding: 24px;
    border-radius: 20px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
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
    line-height: 1.6;
}

/* Steps Indicator */
.steps-container {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 32px;
    padding: 20px;
    background: white;
    border-radius: 16px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    opacity: 0.4;
    transition: all 0.3s;
}

.step.active {
    opacity: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #E5E5EA;
    color: #8E8E93;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 18px;
}

.step.active .step-number {
    background: var(--brand-primary, #0033AA);
    color: white;
}

.step-label {
    font-size: 13px;
    font-weight: 600;
    color: #64748B;
}

.step-line {
    width: 80px;
    height: 2px;
    background: #E5E5EA;
    margin: 0 16px;
}

/* Verification Section */
.verification-section {
    margin-bottom: 32px;
}

.verification-card {
    background: white;
    padding: 32px;
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    max-width: 600px;
    margin: 0 auto;
}

.verification-card h2 {
    font-size: 24px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 8px;
    text-align: center;
}

.verification-subtitle {
    text-align: center;
    color: #64748B;
    margin-bottom: 24px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #E5E5EA;
    border-radius: 12px;
    font-size: 15px;
    font-family: 'Outfit', sans-serif;
    transition: all 0.3s;
}

.form-input:focus {
    outline: none;
    border-color: var(--brand-primary, #0033AA);
}

.form-group small {
    display: block;
    color: #94A3B8;
    font-size: 12px;
    margin-top: 6px;
}

.btn {
    padding: 14px 24px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
    width: 100%;
}

.btn-primary {
    background: var(--brand-primary, #0033AA);
    color: white;
}

.btn-primary:hover {
    background: var(--brand-secondary, #002288);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,51,170,0.3);
}

.verification-result {
    margin-top: 24px;
    padding: 20px;
    background: #F0FDF4;
    border: 2px solid #86EFAC;
    border-radius: 12px;
}

.account-verified {
    display: flex;
    align-items: center;
    gap: 16px;
}

.account-verified i {
    font-size: 32px;
    color: #22C55E;
}

.account-info {
    display: flex;
    flex-direction: column;
}

.account-info strong {
    font-size: 18px;
    color: #1E293B;
}

.account-info span {
    font-size: 14px;
    color: #64748B;
}

/* Products Section */
.products-section {
    margin-top: 32px;
}

.section-title {
    font-size: 22px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 20px;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}

.product-card {
    background: white;
    border: 2px solid #E5E5EA;
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.product-card:hover {
    border-color: var(--brand-primary, #0033AA);
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,51,170,0.15);
}

.product-card.selected {
    border-color: var(--brand-primary, #0033AA);
    background: rgba(0,51,170,0.05);
}

.product-card.out-of-stock {
    opacity: 0.5;
    cursor: not-allowed;
}

.product-badge {
    font-weight: 700;
    color: #1E293B;
    font-size: 16px;
    margin-bottom: 12px;
}

.product-price {
    font-size: 18px;
    font-weight: 700;
    color: var(--brand-primary, #0033AA);
    margin-bottom: 8px;
}

.product-stock {
    font-size: 12px;
    color: #22C55E;
    font-weight: 600;
}

.product-stock.out {
    color: #EF4444;
}

/* Order Summary */
.order-summary {
    background: white;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    max-width: 600px;
    margin: 32px auto 0;
}

.order-summary h3 {
    font-size: 20px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 20px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #F1F5F9;
}

.summary-item.total {
    border-bottom: none;
    padding-top: 16px;
    font-size: 18px;
}

.no-products {
    text-align: center;
    padding: 60px 20px;
    color: #94A3B8;
}

.no-products i {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .steps-container {
        padding: 16px;
    }

    .step-label {
        font-size: 11px;
    }

    .step-line {
        width: 40px;
    }
}
</style>
@endpush

@push('scripts')
<script>
let verifiedUserId = null;
let verifiedUsername = null;
let verifiedZoneId = null;

// Verify User ID
document.getElementById('verification-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const userId = document.getElementById('user_id').value;
    const zoneId = document.getElementById('zone_id') ? document.getElementById('zone_id').value : '';
    const btn = document.querySelector('.btn-verify');

    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memverifikasi...';

    try {
        // Use GET request with query params to avoid WAF blocking
        const params = new URLSearchParams({
            game: '{{ $category->game_code ?? "ml" }}',
            u: userId
        });
        if (zoneId) {
            params.append('z', zoneId);
        }

        const response = await fetch(`/verify-game-id?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Show verification result
            document.getElementById('verification-result').style.display = 'block';
            document.getElementById('account-username').textContent = data.username || 'Pengguna Valid';
            document.getElementById('account-userid').textContent = `ID: ${userId}${zoneId ? ` (${zoneId})` : ''}`;

            // Store verified data
            verifiedUserId = userId;
            verifiedZoneId = zoneId;
            verifiedUsername = data.username || 'Valid User';

            // Show products section
            setTimeout(() => {
                document.getElementById('products-section').style.display = 'block';
                document.getElementById('products-section').scrollIntoView({ behavior: 'smooth' });

                // Update step indicator
                document.getElementById('step-2').classList.add('active');
            }, 1000);
        } else {
            alert('❌ ' + (data.message || 'User ID tidak valid. Silakan cek kembali!'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan. Silakan coba lagi!');
    } finally {
        // Reset button
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Verifikasi User ID';
    }
});

// Select Product
function selectProduct(element) {
    if (element.classList.contains('out-of-stock')) {
        return;
    }

    // Remove previous selection
    document.querySelectorAll('.product-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Add selection
    element.classList.add('selected');

    // Get product data
    const productId = element.dataset.productId;
    const productName = element.dataset.productName;
    const productPrice = element.dataset.productPrice;

    // Update order summary
    document.getElementById('order-summary').style.display = 'block';
    document.getElementById('summary-userid').textContent = verifiedUserId + (verifiedZoneId ? ` (${verifiedZoneId})` : '');
    document.getElementById('summary-username').textContent = verifiedUsername;
    document.getElementById('summary-product').textContent = productName;
    document.getElementById('summary-price').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(productPrice);

    // Set hidden form fields
    document.getElementById('selected_product_id').value = productId;
    document.getElementById('customer_no').value = verifiedUserId;
    document.getElementById('customer_zone_id').value = verifiedZoneId;
    document.getElementById('verified_username').value = verifiedUsername;

    // Update step indicator
    document.getElementById('step-3').classList.add('active');

    // Scroll to summary
    document.getElementById('order-summary').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endpush
@endsection
