@extends('layouts.main')

@section('title', $category['name'] . ' - ' . config('app.name'))

@section('content')
<!-- Modern Popup -->
<div class="popup-overlay" id="modernPopup">
    <div class="popup-box">
        <div class="popup-icon warning" id="popupIcon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="popup-title" id="popupTitle">PERHATIAN !</div>
        <div class="popup-message" id="popupMessage"></div>
        <button class="popup-btn" onclick="closePopup()">BAIK, MENGERTI !</button>
    </div>
</div>

<div class="main-content">
    <!-- Category Header -->
    <div class="category-header" @if(!empty($category['background'])) style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ $category['background'] }}'); background-size: cover; background-position: center;" @endif>
        <div class="category-info">
            @if(!empty($category['image']))
                <img src="{{ $category['image'] }}" alt="{{ $category['name'] }}" class="category-icon-large">
            @endif
            <div>
                <h1 class="category-title">{{ $category['name'] }}</h1>
                @if(!empty($category['sub_name']))
                    <p class="category-subtitle">{{ $category['sub_name'] }}</p>
                @endif
                @if(!empty($category['description']))
                    <p class="category-description">{!! nl2br(e($category['description'])) !!}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Step Indicator -->
    <div class="steps-container">
        <div class="step active" id="step-1">
            <div class="step-number">1</div>
            <div class="step-label">Masukkan Data</div>
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

    <!-- Step 1: User ID Input -->
    <div class="user-input-section" id="user-input-section">
        <div class="input-card">
            <h2>🎮 Masukkan Data Akun</h2>
            @if(!empty($category['info_form']))
                <p class="input-subtitle">{!! $category['info_form'] !!}</p>
            @else
                <p class="input-subtitle">Masukkan data akun untuk melanjutkan pembelian</p>
            @endif

            <form id="user-input-form">
                @if(!empty($category['form_fields']))
                    @foreach($category['form_fields'] as $field)
                        @php
                            $fieldTitle = $field['title'] ?? $field['label'] ?? $field['name'];
                            $fieldOptions = $field['options'] ?? [];
                            // Parse options if it's a JSON string
                            if (is_string($fieldOptions) && !empty($fieldOptions)) {
                                $fieldOptions = json_decode($fieldOptions, true) ?? [];
                            }
                        @endphp
                        <div class="form-group">
                            <label for="field_{{ $field['name'] }}">{{ $fieldTitle }} *</label>
                            @if($field['type'] == 'select' && !empty($fieldOptions))
                                <select id="field_{{ $field['name'] }}" name="{{ $field['name'] }}" class="form-input" required>
                                    <option value="">Pilih {{ $fieldTitle }}</option>
                                    @foreach($fieldOptions as $option)
                                        <option value="{{ $option['value'] ?? $option }}">{{ $option['text'] ?? $option['label'] ?? $option }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="{{ $field['type'] ?? 'text' }}"
                                       id="field_{{ $field['name'] }}"
                                       name="{{ $field['name'] }}"
                                       class="form-input"
                                       placeholder="{{ $field['placeholder'] ?? 'Masukkan ' . $fieldTitle }}"
                                       required>
                            @endif
                        </div>
                    @endforeach
                @else
                    <!-- Default form if no form_fields from API -->
                    <div class="form-group">
                        <label for="user_id">User ID *</label>
                        <input type="text" id="user_id" name="user_id" class="form-input"
                               placeholder="Masukkan User ID" required>
                        <small>Masukkan User ID akun game Anda</small>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary btn-continue">
                    <i class="fas fa-arrow-right"></i>
                    Lanjutkan
                </button>
            </form>

            <!-- User Info Display -->
            <div id="user-info-display" class="user-info-display" style="display:none;">
                <div class="info-badge">
                    <i class="fas fa-user-check"></i>
                    <div class="info-text">
                        <strong id="display-userdata">-</strong>
                    </div>
                    <button type="button" class="btn-edit" onclick="editUserData()">
                        <i class="fas fa-edit"></i> Ubah
                    </button>
                </div>
            </div>

            @if(!empty($category['tutorial']))
                <div class="tutorial-section">
                    <h4><i class="fas fa-info-circle"></i> Cara Mendapatkan User ID</h4>
                    <div class="tutorial-content">{!! $category['tutorial'] !!}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Step 2: Product Selection -->
    <div class="products-section" id="products-section" style="display:none;">
        <h2 class="section-title">💎 Pilih Nominal</h2>

        @if(!empty($products))
            @foreach($products as $productGroup)
                @if(!empty($productGroup['items']))
                    <div class="product-group">
                        <h3 class="product-group-title">{{ $productGroup['jenis_layanan'] ?? 'Produk' }}</h3>
                        <div class="products-grid">
                            @foreach($productGroup['items'] as $product)
                                @php
                                    // API uses 'layanan' for name and 'id' for code
                                    $productName = $product['layanan'] ?? $product['nama_layanan'] ?? '';
                                    $productCode = $product['id'] ?? $product['kode_layanan'] ?? '';
                                    $productPrice = $product['harga'] ?? 0;
                                    $isAvailable = true; // Assume available if no status field
                                @endphp
                                <div class="product-card {{ !$isAvailable ? 'out-of-stock' : '' }}"
                                     data-product-code="{{ $productCode }}"
                                     data-product-name="{{ $productName }}"
                                     data-product-price="{{ $productPrice }}"
                                     onclick="selectProduct(this)">
                                    <div class="product-badge">{{ $productName }}</div>
                                    <div class="product-price">
                                        Rp {{ number_format($productPrice, 0, ',', '.') }}
                                    </div>
                                    @if($isAvailable)
                                        <div class="product-stock">✓ Tersedia</div>
                                    @else
                                        <div class="product-stock out">✗ Gangguan</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Order Summary -->
            <div class="order-summary" id="order-summary" style="display:none;">
                <h3>📋 Ringkasan Pesanan</h3>
                <div class="summary-item">
                    <span>Game:</span>
                    <strong>{{ $category['name'] }}</strong>
                </div>
                <div class="summary-item">
                    <span>Data Akun:</span>
                    <strong id="summary-userdata">-</strong>
                </div>
                <div class="summary-item">
                    <span>Produk:</span>
                    <strong id="summary-product">-</strong>
                </div>
                <div class="summary-item total">
                    <span>Total Bayar:</span>
                    <strong id="summary-price">Rp 0</strong>
                </div>

                <div class="payment-section">
                    <h4>💳 Pilih Pembayaran</h4>
                    @if(!empty($paymentMethods))
                        <div class="payment-methods">
                            @foreach($paymentMethods as $paymentGroup)
                                @php
                                    // API uses 'metode_pembayaran' not 'items'
                                    $methods = $paymentGroup['metode_pembayaran'] ?? $paymentGroup['items'] ?? [];
                                @endphp
                                @if(!empty($methods))
                                    <div class="payment-group">
                                        <div class="payment-group-title">{{ $paymentGroup['jenis'] ?? 'Lainnya' }}</div>
                                        <div class="payment-options">
                                            @foreach($methods as $payment)
                                                @if(($payment['is_gangguan'] ?? '0') != '1')
                                                <label class="payment-option">
                                                    <input type="radio" name="payment_method" value="{{ $payment['id'] ?? $payment['id_pembayaran'] ?? '' }}">
                                                    <div class="payment-content">
                                                        @if(!empty($payment['gambar']))
                                                            <img src="{{ $payment['gambar'] }}" alt="{{ $payment['nama'] ?? '' }}">
                                                        @endif
                                                        <span>{{ $payment['nama'] ?? '' }}</span>
                                                    </div>
                                                </label>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="contact-section">
                    <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-address-book" style="color: var(--primary, #0033AA);"></i> Data Pembeli
                        <button type="button" onclick="clearBuyerData()" style="margin-left: auto; background: none; border: 1px solid #E2E8F0; border-radius: 8px; padding: 4px 10px; font-size: 11px; color: #94A3B8; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-eraser"></i> Clear
                        </button>
                    </h3>
                    <div class="form-group">
                        <label for="customer_phone">WhatsApp <span style="color: #94A3B8; font-weight: 400;">(Opsional)</span></label>
                        <input type="tel" id="customer_phone" name="customer_phone"
                               placeholder="08xxxxxxxxxx" class="form-input">
                        <small>Invoice bukti transaksi akan dikirimkan otomatis ke WhatsApp Anda.</small>
                    </div>
                    <div class="form-group" style="margin-top: 12px;">
                        <label for="customer_email">Email <span style="color: #94A3B8; font-weight: 400;">(Opsional)</span></label>
                        <input type="email" id="customer_email" name="customer_email"
                               placeholder="nama@email.com" class="form-input">
                    </div>
                </div>

                <button type="button" class="btn btn-primary btn-checkout" onclick="processOrder()">
                    <i class="fas fa-shopping-cart"></i>
                    Bayar Sekarang
                </button>
            </div>
        @else
            <div class="no-products">
                <i class="fas fa-box-open"></i>
                <p>Belum ada produk di kategori ini</p>
            </div>
        @endif
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay" style="display:none;">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <p>Memproses pesanan...</p>
    </div>
</div>

@push('styles')
<style>
/* Modern Popup */
.popup-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    backdrop-filter: blur(4px);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    padding: 20px;
    animation: popupFadeIn 0.2s ease;
}
.popup-overlay.active { display: flex; }
@keyframes popupFadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes popupSlideUp { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
.popup-box {
    background: white;
    border-radius: 20px;
    padding: 30px 24px 24px;
    max-width: 360px;
    width: 100%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    animation: popupSlideUp 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
}
.popup-icon { font-size: 48px; margin-bottom: 16px; }
.popup-icon.warning { color: #F59E0B; }
.popup-icon.error { color: #EF4444; }
.popup-icon.success { color: #10B981; }
.popup-icon.info { color: #3B82F6; }
.popup-title { font-size: 16px; font-weight: 700; color: #1E293B; margin-bottom: 8px; }
.popup-message { font-size: 13px; color: #64748B; line-height: 1.5; margin-bottom: 20px; }
.popup-btn {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    background: var(--primary, #0033AA);
    color: white;
    transition: all 0.2s;
}
.popup-btn:active { transform: scale(0.97); }

.main-content {
    padding: 20px;
    max-width: 900px;
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
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.category-title {
    font-size: 24px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 4px;
}

.category-subtitle {
    font-size: 14px;
    color: var(--brand-primary, #0033AA);
    font-weight: 600;
    margin-bottom: 8px;
}

.category-description {
    color: #64748B;
    line-height: 1.6;
    font-size: 14px;
}

/* Steps Indicator */
.steps-container {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
    padding: 16px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    opacity: 0.4;
    transition: all 0.3s;
}

.step.active, .step.completed {
    opacity: 1;
}

.step.completed .step-number {
    background: #22C55E;
    color: white;
}

.step-number {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #E5E5EA;
    color: #8E8E93;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
}

.step.active .step-number {
    background: var(--brand-primary, #0033AA);
    color: white;
}

.step-label {
    font-size: 12px;
    font-weight: 600;
    color: #64748B;
}

.step-line {
    width: 60px;
    height: 2px;
    background: #E5E5EA;
    margin: 0 12px;
}

/* User Input Section */
.input-card {
    background: white;
    padding: 24px;
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.input-card h2 {
    font-size: 20px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 8px;
    text-align: center;
}

.input-subtitle {
    text-align: center;
    color: #64748B;
    margin-bottom: 20px;
    font-size: 14px;
}

.user-info-display {
    margin-top: 16px;
}

.info-badge {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: #F0FDF4;
    border: 2px solid #86EFAC;
    border-radius: 12px;
}

.info-badge i {
    font-size: 20px;
    color: #22C55E;
}

.info-text {
    flex: 1;
}

.info-text strong {
    font-size: 14px;
    color: #1E293B;
}

.btn-edit {
    background: transparent;
    border: 1px solid #64748B;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    color: #64748B;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-edit:hover {
    background: #F1F5F9;
}

.tutorial-section {
    margin-top: 20px;
    padding: 16px;
    background: #FEF3C7;
    border-radius: 12px;
    border: 1px solid #FCD34D;
}

.tutorial-section h4 {
    font-size: 14px;
    color: #92400E;
    margin-bottom: 8px;
}

.tutorial-content {
    font-size: 13px;
    color: #78350F;
    line-height: 1.6;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 6px;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 12px 14px;
    border: 2px solid #E5E5EA;
    border-radius: 12px;
    font-size: 15px;
    font-family: 'Outfit', sans-serif;
    transition: all 0.3s;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: var(--brand-primary, #0033AA);
}

.form-group small {
    display: block;
    color: #94A3B8;
    font-size: 12px;
    margin-top: 4px;
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

/* Products Section */
.products-section {
    margin-top: 24px;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 16px;
}

.product-group {
    margin-bottom: 24px;
}

.product-group-title {
    font-size: 16px;
    font-weight: 600;
    color: #64748B;
    margin-bottom: 12px;
    padding-left: 4px;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
}

.product-card {
    background: white;
    border: 2px solid #E5E5EA;
    border-radius: 14px;
    padding: 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.product-card:hover {
    border-color: var(--brand-primary, #0033AA);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,51,170,0.12);
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
    font-weight: 600;
    color: #1E293B;
    font-size: 14px;
    margin-bottom: 8px;
    line-height: 1.3;
}

.product-price {
    font-size: 16px;
    font-weight: 700;
    color: var(--brand-primary, #0033AA);
    margin-bottom: 6px;
}

.product-stock {
    font-size: 11px;
    color: #22C55E;
    font-weight: 600;
}

.product-stock.out {
    color: #EF4444;
}

/* Order Summary */
.order-summary {
    background: white;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-top: 24px;
}

.order-summary h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 16px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #F1F5F9;
    font-size: 14px;
}

.summary-item.total {
    border-bottom: none;
    padding-top: 12px;
    font-size: 16px;
}

/* Payment Section */
.payment-section {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #E5E5EA;
}

.payment-section h4 {
    font-size: 16px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 12px;
}

.payment-group {
    margin-bottom: 16px;
}

.payment-group-title {
    font-size: 13px;
    font-weight: 600;
    color: #64748B;
    margin-bottom: 8px;
}

.payment-options {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
}

.payment-option {
    cursor: pointer;
}

.payment-option input {
    display: none;
}

.payment-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 12px;
    border: 2px solid #E5E5EA;
    border-radius: 12px;
    transition: all 0.3s;
}

.payment-option input:checked + .payment-content {
    border-color: var(--brand-primary, #0033AA);
    background: rgba(0,51,170,0.05);
}

.payment-content img {
    height: 24px;
    object-fit: contain;
}

.payment-content span {
    font-size: 12px;
    color: #64748B;
    text-align: center;
}

/* Contact Section */
.contact-section {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #E5E5EA;
}

.no-products {
    text-align: center;
    padding: 40px 20px;
    color: #94A3B8;
    background: white;
    border-radius: 16px;
}

.no-products i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-content {
    background: white;
    padding: 32px;
    border-radius: 16px;
    text-align: center;
}

.loading-spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #E5E5EA;
    border-top-color: var(--brand-primary, #0033AA);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 16px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .main-content {
        padding: 16px;
    }

    .category-info {
        flex-direction: column;
        text-align: center;
    }

    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .payment-options {
        grid-template-columns: repeat(2, 1fr);
    }

    .steps-container {
        padding: 12px;
    }

    .step-label {
        font-size: 10px;
    }

    .step-line {
        width: 30px;
    }

    .info-badge {
        flex-wrap: wrap;
    }

    .btn-edit {
        width: 100%;
        margin-top: 8px;
    }
}
</style>
@endpush

@push('scripts')
<script>
let formData = {};
let selectedProduct = null;

// Modern Popup functions
function showPopup(message, type = 'warning') {
    const popup = document.getElementById('modernPopup');
    const icon = document.getElementById('popupIcon');
    const title = document.getElementById('popupTitle');
    const msg = document.getElementById('popupMessage');

    const icons = {
        warning: '<i class="fas fa-exclamation-triangle"></i>',
        error: '<i class="fas fa-times-circle"></i>',
        success: '<i class="fas fa-check-circle"></i>',
        info: '<i class="fas fa-info-circle"></i>'
    };
    const titles = {
        warning: 'PERHATIAN !',
        error: 'GAGAL !',
        success: 'BERHASIL !',
        info: 'INFORMASI'
    };

    icon.className = 'popup-icon ' + type;
    icon.innerHTML = icons[type] || icons.warning;
    title.textContent = titles[type] || titles.warning;
    msg.textContent = message;
    popup.classList.add('active');
}
function closePopup() {
    document.getElementById('modernPopup').classList.remove('active');
}

// Handle User ID Form Submit
document.getElementById('user-input-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Collect all form data
    const form = e.target;
    const inputs = form.querySelectorAll('input, select');
    formData = {};
    let displayText = '';

    inputs.forEach(input => {
        if (input.name && input.value) {
            formData[input.name] = input.value;

            // Build display text
            const label = form.querySelector(`label[for="${input.id}"]`);
            if (label) {
                displayText += (displayText ? ' | ' : '') + label.textContent.replace('*', '').trim() + ': ' + input.value;
            }
        }
    });

    // Check required fields
    const requiredFields = form.querySelectorAll('[required]');
    let allFilled = true;
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            allFilled = false;
            field.classList.add('error');
        } else {
            field.classList.remove('error');
        }
    });

    if (!allFilled) {
        showPopup('Mohon lengkapi semua data yang diperlukan', 'warning');
        return;
    }

    // Hide form and show info display
    form.style.display = 'none';
    document.getElementById('user-info-display').style.display = 'block';
    document.getElementById('display-userdata').textContent = displayText || 'Data tersimpan';

    // Mark step 1 as completed
    document.getElementById('step-1').classList.remove('active');
    document.getElementById('step-1').classList.add('completed');

    // Show products section
    document.getElementById('products-section').style.display = 'block';
    document.getElementById('step-2').classList.add('active');

    // Scroll to products
    setTimeout(() => {
        document.getElementById('products-section').scrollIntoView({ behavior: 'smooth' });
    }, 300);
});

// Edit User Data
function editUserData() {
    document.getElementById('user-input-form').style.display = 'block';
    document.getElementById('user-info-display').style.display = 'none';
    document.getElementById('products-section').style.display = 'none';
    document.getElementById('order-summary').style.display = 'none';

    document.getElementById('step-1').classList.add('active');
    document.getElementById('step-1').classList.remove('completed');
    document.getElementById('step-2').classList.remove('active');
    document.getElementById('step-2').classList.remove('completed');
    document.getElementById('step-3').classList.remove('active');

    document.querySelectorAll('.product-card').forEach(card => {
        card.classList.remove('selected');
    });
    selectedProduct = null;
}

// Select Product
function selectProduct(element) {
    if (element.classList.contains('out-of-stock')) {
        return;
    }

    document.querySelectorAll('.product-card').forEach(card => {
        card.classList.remove('selected');
    });

    element.classList.add('selected');

    selectedProduct = {
        code: element.dataset.productCode,
        name: element.dataset.productName,
        price: parseInt(element.dataset.productPrice)
    };

    // Update order summary
    document.getElementById('order-summary').style.display = 'block';
    document.getElementById('summary-userdata').textContent = document.getElementById('display-userdata').textContent;
    document.getElementById('summary-product').textContent = selectedProduct.name;
    document.getElementById('summary-price').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(selectedProduct.price);

    // Update step indicator
    document.getElementById('step-2').classList.remove('active');
    document.getElementById('step-2').classList.add('completed');
    document.getElementById('step-3').classList.add('active');

    // Scroll to summary
    setTimeout(() => {
        document.getElementById('order-summary').scrollIntoView({ behavior: 'smooth' });
    }, 200);
}

// Auto-save buyer data
function saveBuyerData() {
    const phone = document.getElementById('customer_phone').value;
    const email = document.getElementById('customer_email') ? document.getElementById('customer_email').value : '';
    localStorage.setItem('buyer_phone', phone);
    localStorage.setItem('buyer_email', email);
}
function loadBuyerData() {
    const phone = localStorage.getItem('buyer_phone');
    const email = localStorage.getItem('buyer_email');
    if (phone) document.getElementById('customer_phone').value = phone;
    if (email && document.getElementById('customer_email')) document.getElementById('customer_email').value = email;
}
function clearBuyerData() {
    document.getElementById('customer_phone').value = '';
    if (document.getElementById('customer_email')) document.getElementById('customer_email').value = '';
    localStorage.removeItem('buyer_phone');
    localStorage.removeItem('buyer_email');
}

// Auto-save on input
document.getElementById('customer_phone').addEventListener('input', saveBuyerData);
if (document.getElementById('customer_email')) {
    document.getElementById('customer_email').addEventListener('input', saveBuyerData);
}

// Load saved data on page load
loadBuyerData();

// Process Order
function processOrder() {
    const phone = document.getElementById('customer_phone').value.trim();
    const email = document.getElementById('customer_email') ? document.getElementById('customer_email').value.trim() : '';
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');

    if (!paymentMethod) {
        showPopup('Mohon pilih metode pembayaran', 'warning');
        return;
    }

    if (!selectedProduct) {
        showPopup('Pilih "Produk" terlebih dahulu.', 'warning');
        return;
    }

    // Show loading
    document.getElementById('loading-overlay').style.display = 'flex';

    // Prepare order data
    const orderData = {
        _token: '{{ csrf_token() }}',
        product_code: selectedProduct.code,
        payment_method: paymentMethod.value,
        phone: phone,
        email: email,
        slug: '{{ $category["slug"] }}',
        ...formData
    };

    // Submit order via AJAX
    fetch('{{ route("order.create") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading-overlay').style.display = 'none';

        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        } else if (data.error) {
            showPopup(data.error, 'error');
        } else {
            showPopup('Terjadi kesalahan. Silakan coba lagi.', 'error');
        }
    })
    .catch(error => {
        document.getElementById('loading-overlay').style.display = 'none';
        console.error('Error:', error);
        showPopup('Terjadi kesalahan jaringan. Silakan coba lagi.', 'error');
    });
}
</script>
@endpush
@endsection
