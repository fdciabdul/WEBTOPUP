@extends('layouts.main')

@section('title', $product->name . ' - ' . config('app.name'))

@push('styles')
<style>
    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .item-animate { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    .animation-finished { opacity: 1 !important; animation: none !important; }

    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-4px); }
        100% { transform: translateY(0px); }
    }
    .animate-float { animation: float 4s ease-in-out infinite; }

    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .animate-bounce-slow { animation: bounce-slow 2s infinite ease-in-out; }

    @keyframes bounce-short {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-bounce-short { animation: bounce-short 0.4s ease-in-out; }

    @keyframes error-bounce-loop {
        0%, 100% { transform: translateY(0); box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2); }
        50% { transform: translateY(-4px); box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.4); }
    }
    .animate-error-loop {
        animation: error-bounce-loop 0.8s infinite;
        border: 2px solid #EF4444 !important;
        background-color: #FEF2F2 !important;
    }

    @keyframes pulse-strong {
        0% { box-shadow: 0 0 0 0 rgba(0, 51, 170, 0.5); }
        70% { box-shadow: 0 0 0 14px rgba(0, 51, 170, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 51, 170, 0); }
    }
    .btn-pulse { animation: pulse-strong 2s infinite; }

    /* Card Styles */
    .card-ios {
        background: white;
        border-radius: 32px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.02);
    }

    .input-ios {
        background-color: #F3F4F6;
        border: 1px solid transparent;
        transition: all 0.3s ease;
    }
    .input-ios:focus {
        background-color: #FFFFFF;
        border-color: var(--brand-primary, #0033AA);
        box-shadow: 0 0 0 4px rgba(0, 51, 170, 0.1);
    }

    .card-selected {
        border: 2px solid var(--brand-primary, #0033AA) !important;
        background-color: #F0F9FF !important;
        position: relative;
        transform: scale(1.02);
        z-index: 10;
        box-shadow: 0 8px 20px rgba(0, 51, 170, 0.15);
    }

    .badge-check {
        position: absolute; top: -1px; right: -1px;
        background: var(--brand-primary, #0033AA); color: white;
        border-bottom-left-radius: 12px; border-top-right-radius: 20px;
        width: 26px; height: 26px; display: flex; align-items: center; justify-content: center;
        font-size: 10px; z-index: 10;
    }

    .ribbon-promo {
        position: absolute; top: 0; left: 0;
        background: linear-gradient(135deg, #F43F5E, #E11D48);
        color: white; font-size: 9px; font-weight: 800;
        padding: 3px 10px; border-bottom-right-radius: 12px; border-top-left-radius: 20px;
        z-index: 10;
    }

    .payment-disabled {
        opacity: 0.5;
        filter: grayscale(100%);
        pointer-events: none;
        background-color: #F9FAFB;
    }

    .touch-effect { transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .touch-effect:active { transform: scale(0.96); }

    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* Sticky Header for Product Detail */
    .product-header {
        position: sticky;
        top: 0;
        z-index: 50;
        background: rgba(0, 51, 170, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255,255,255,0.1);
        transition: all 0.3s ease;
        display: flex;
        justify-content: center;
    }
    .product-header > div { width: 100%; max-width: 1440px; }

    @media (min-width: 1024px) {
        .product-header { border-radius: 0 0 40px 40px; }
    }
    .product-header.scrolled {
        box-shadow: 0 4px 30px rgba(0, 51, 170, 0.3);
    }

    /* Hide main layout header on product detail page */
    .header { display: none !important; }
    #scroll-top-btn { display: none !important; }

    /* Account check result */
    @keyframes checkPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    .check-pulse { animation: checkPulse 0.5s ease; }
</style>
@endpush

@section('content')
<div x-data="productOrder()" x-init="init()" class="min-h-screen pb-48 lg:pb-32">

    <!-- Sticky Product Header -->
    <header class="product-header" :class="{ 'scrolled': scrollY > 50 }" x-data="{ scrollY: 0 }" @scroll.window="scrollY = window.scrollY">
        <div class="w-full max-w-[1440px] mx-auto px-4 lg:px-0">
            <div class="py-3 flex items-center justify-between">
                <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('home') }}" class="flex items-center gap-3 text-white touch-effect group">
                    <div class="w-10 h-10 bg-white/15 rounded-full flex items-center justify-center group-active:bg-white/25 transition border border-white/10 backdrop-blur-md">
                        <i class="fa-solid fa-arrow-left text-lg"></i>
                    </div>
                    <div x-show="!searchOpen">
                        <h1 class="text-lg font-heading font-bold leading-tight uppercase tracking-wider">{{ config('app.name') }}</h1>
                        <p class="text-xs text-white/80 font-medium tracking-wide">Official Store</p>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    <button @click="searchOpen = !searchOpen" class="w-10 h-10 rounded-full flex items-center justify-center text-white hover:bg-white/10 touch-effect transition">
                        <i class="fa-solid" :class="searchOpen ? 'fa-xmark' : 'fa-magnifying-glass'"></i>
                    </button>

                    <a href="{{ $whatsappUrl ?? 'https://wa.me/6281234567890' }}" target="_blank" class="w-10 h-10 rounded-full flex items-center justify-center text-white hover:bg-white/10 touch-effect transition relative">
                        <i class="fa-solid fa-headset text-lg"></i>
                        <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-green-400 rounded-full border-2 border-[#0033AA]"></span>
                    </a>

                    @guest
                    <a href="{{ route('login') }}" class="flex items-center gap-2 bg-white text-[#0033AA] px-5 py-2.5 rounded-full text-xs font-bold shadow-lg hover:bg-gray-50 touch-effect transition-transform ml-1">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login</span>
                    </a>
                    @else
                    <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2 bg-white text-[#0033AA] px-4 py-2.5 rounded-full text-xs font-bold shadow-lg hover:bg-gray-50 touch-effect transition-transform ml-1">
                        <i class="fa-solid fa-user"></i>
                        <span class="hidden sm:inline">{{ Str::limit(auth()->user()->name, 10) }}</span>
                    </a>
                    @endguest
                </div>
            </div>

            <!-- Expandable Search -->
            <div x-show="searchOpen" x-collapse class="pb-4">
                <form action="{{ route('search') }}" method="GET" class="relative item-animate">
                    <input type="text" name="q" placeholder="Cari produk lain..." class="w-full pl-11 pr-5 py-3.5 rounded-2xl bg-white/15 border border-white/20 text-white placeholder-white/60 text-sm focus:outline-none focus:bg-white/25 transition-all backdrop-blur-sm">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-4 text-white/60"></i>
                </form>
            </div>
        </div>
    </header>

    <main class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10">

        <!-- Left Column -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-8">

            <!-- Product Header Card -->
            <div class="card-ios p-5 relative overflow-hidden item-animate">
                <div class="flex items-center gap-5 relative z-10">
                    <div class="relative shrink-0 animate-float">
                        <div class="p-1.5 bg-white rounded-[24px] shadow-lg">
                            @if($product->icon)
                                <img src="{{ asset('storage/' . $product->icon) }}"
                                     class="w-20 h-20 rounded-[18px] object-cover" alt="{{ $product->name }}">
                            @elseif($product->category && $product->category->icon)
                                <img src="{{ asset('storage/' . $product->category->icon) }}"
                                     class="w-20 h-20 rounded-[18px] object-cover" alt="{{ $product->name }}">
                            @else
                                <div class="w-20 h-20 rounded-[18px] bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                    <i class="fa-solid fa-gamepad text-white text-3xl"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1 w-full">
                        <div class="flex justify-between items-start mb-1 relative">
                            <h2 class="font-heading font-extrabold text-xl text-slate-900 leading-tight flex items-center gap-2">
                                {{ $product->name }}
                                <i class="fa-solid fa-circle-check text-blue-500 text-lg" title="Verified"></i>
                            </h2>

                            <button @click.prevent.stop="isFav = !isFav"
                                    class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center transition-colors duration-300 relative z-30"
                                    :class="isFav ? 'text-red-500 bg-red-50' : 'text-gray-400 hover:text-red-400'">
                                <i class="fa-solid fa-heart text-lg transform transition-transform duration-300" :class="isFav ? 'scale-110' : 'scale-100'"></i>
                            </button>
                        </div>

                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex text-yellow-400 text-sm">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-700">5.0</span>
                        </div>

                        <!-- Feature Badges -->
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="bg-gradient-to-br from-emerald-100/90 via-teal-50 to-white border border-emerald-200/60 rounded-2xl p-2.5 flex flex-col items-center justify-center text-center gap-1 animate-bounce-slow shadow-sm" style="animation-delay: 0s;">
                                <div class="w-8 h-8 rounded-full bg-white/70 flex items-center justify-center shadow-sm mb-0.5">
                                    <i class="fa-solid fa-bolt text-emerald-600 text-lg"></i>
                                </div>
                                <span class="text-[11px] font-extrabold text-emerald-900 leading-none">Proses</span>
                                <span class="text-[10px] font-semibold text-emerald-700/80 leading-none">Cepat</span>
                            </div>

                            <div class="bg-gradient-to-br from-blue-100/90 via-indigo-50 to-white border border-blue-200/60 rounded-2xl p-2.5 flex flex-col items-center justify-center text-center gap-1 animate-bounce-slow shadow-sm" style="animation-delay: 0.2s;">
                                <div class="w-8 h-8 rounded-full bg-white/70 flex items-center justify-center shadow-sm mb-0.5">
                                    <i class="fa-solid fa-clock text-blue-600 text-lg"></i>
                                </div>
                                <span class="text-[11px] font-extrabold text-blue-900 leading-none">Layanan</span>
                                <span class="text-[10px] font-semibold text-blue-700/80 leading-none">24 Jam</span>
                            </div>

                            <div class="bg-gradient-to-br from-orange-100/90 via-amber-50 to-white border border-orange-200/60 rounded-2xl p-2.5 flex flex-col items-center justify-center text-center gap-1 animate-bounce-slow shadow-sm" style="animation-delay: 0.4s;">
                                <div class="w-8 h-8 rounded-full bg-white/70 flex items-center justify-center shadow-sm mb-0.5">
                                    <i class="fa-solid fa-shield-halved text-orange-600 text-lg"></i>
                                </div>
                                <span class="text-[11px] font-extrabold text-orange-900 leading-none">Garansi</span>
                                <span class="text-[10px] font-semibold text-orange-700/80 leading-none">100%</span>
                            </div>
                        </div>

                        <!-- Description Toggle -->
                        @if($product->description)
                        <div x-data="{ expanded: false }" class="mt-4">
                            <button @click="expanded = !expanded"
                                    class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl bg-[#0033AA]/10 text-[#0033AA] font-bold text-sm transition-all duration-300 hover:bg-[#0033AA]/20 active:scale-95 group">
                                <span class="flex items-center gap-2">
                                    <span x-text="expanded ? 'Tutup Deskripsi' : 'Lihat Rincian & Deskripsi'"></span>
                                </span>
                                <i class="fa-solid fa-chevron-down transition-transform duration-300" :class="expanded ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="expanded" x-collapse>
                                <div class="mt-2 p-4 bg-gray-50 rounded-2xl text-xs text-slate-600 leading-relaxed border border-gray-100 shadow-sm">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Step 1: Select Product/Variant -->
            <div id="section-nominal" class="card-ios overflow-hidden transition-all duration-300 item-animate" style="animation-delay: 100ms;"
                 :class="{'animate-error-loop': errors.product}">
                <div class="bg-gray-50/80 backdrop-blur-sm px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#0033AA] text-white flex items-center justify-center font-bold text-sm shadow-lg shadow-[#0033AA]/20">1</div>
                    <h3 class="font-bold text-slate-800 text-base">Pilih Nominal</h3>
                </div>

                <div class="p-6">
                    @if(isset($variants) && $variants->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($variants as $variant)
                        <div @click="selectVariant({{ $variant->id }}, '{{ addslashes($variant->name) }}', {{ $variant->getPriceByLevel($userLevel) }}); errors.product = false"
                             class="border rounded-[24px] p-5 cursor-pointer transition-all duration-300 touch-effect relative group bg-white h-full flex flex-col justify-between hover:shadow-lg hover:-translate-y-1"
                             :class="selectedVariant?.id === {{ $variant->id }} ? 'card-selected animate-bounce-short' : 'border-gray-100'">

                            <div x-show="selectedVariant?.id === {{ $variant->id }}" class="badge-check"><i class="fa-solid fa-check"></i></div>
                            @if($variant->is_promo ?? false)
                            <div class="ribbon-promo">PROMO</div>
                            @endif

                            <div class="flex items-center gap-3 mb-4 mt-1">
                                <div class="w-10 h-10 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0">
                                    @if($variant->icon ?? null)
                                        <img src="{{ asset('storage/' . $variant->icon) }}" class="w-5 h-5 object-contain drop-shadow-sm transition duration-300" alt="{{ $variant->name }}">
                                    @else
                                        <i class="fa-solid fa-gem text-blue-500"></i>
                                    @endif
                                </div>
                                <span class="text-xs font-bold text-slate-700 leading-tight group-hover:text-[#0033AA] transition-colors">{{ $variant->name }}</span>
                            </div>
                            <p class="text-base font-extrabold text-[#0033AA] tracking-tight">Rp {{ number_format($variant->getPriceByLevel($userLevel), 0, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <!-- Single Product (No Variants) -->
                    <div @click="selectVariant(0, '{{ addslashes($product->name) }}', {{ $price }}); errors.product = false"
                         class="border rounded-[24px] p-5 cursor-pointer transition-all duration-300 touch-effect relative group bg-white hover:shadow-lg hover:-translate-y-1"
                         :class="selectedVariant?.id === 0 ? 'card-selected animate-bounce-short' : 'border-gray-100'">

                        <div x-show="selectedVariant?.id === 0" class="badge-check"><i class="fa-solid fa-check"></i></div>

                        <div class="flex items-center gap-3 mb-4 mt-1">
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-gem text-blue-500"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-700 leading-tight group-hover:text-[#0033AA] transition-colors">{{ $product->name }}</span>
                        </div>
                        <p class="text-base font-extrabold text-[#0033AA] tracking-tight">Rp {{ number_format($price, 0, ',', '.') }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-8">

            <!-- Step 2: Payment Method -->
            <div id="section-payment" class="card-ios overflow-hidden transition-all duration-300 item-animate" style="animation-delay: 200ms;"
                 :class="{'animate-error-loop': errors.payment}">
                <div class="bg-gray-50/80 backdrop-blur-sm px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#0033AA] text-white flex items-center justify-center font-bold text-sm shadow-lg shadow-[#0033AA]/20">2</div>
                    <h3 class="font-bold text-slate-800 text-base">Metode Pembayaran</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div x-show="!selectedVariant" class="text-center py-10 bg-gray-50 rounded-[24px] border border-dashed border-gray-200">
                        <i class="fa-solid fa-basket-shopping text-gray-300 text-3xl mb-3 animate-bounce"></i>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mt-2">Pilih Nominal Terlebih Dahulu</p>
                    </div>

                    <div x-show="selectedVariant" class="space-y-4">
                        <!-- Balance Payment (Auth Users) -->
                        @auth
                        <div class="border border-gray-100 rounded-[24px] overflow-hidden bg-white shadow-sm hover:shadow-md transition-all">
                            <button @click="togglePaymentGroup('saldo')"
                                    class="w-full px-5 py-4 bg-gray-50/50 flex items-center justify-between transition-colors hover:bg-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-slate-500 shadow-sm">
                                        <i class="fa-solid fa-wallet text-xs"></i>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Saldo</h4>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-emerald-600 font-bold">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</span>
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-300" :class="activePaymentGroups.includes('saldo') ? 'rotate-180' : ''"></i>
                                </div>
                            </button>

                            <div x-show="activePaymentGroups.includes('saldo')" x-collapse class="p-4 space-y-3 bg-white border-t border-gray-100">
                                <div @click="selectPayment('balance', 'Saldo', 0, 'flat'); errors.payment = false"
                                     class="border rounded-[20px] p-4 cursor-pointer relative overflow-hidden touch-effect transition-all duration-300 group flex items-center justify-between hover:border-[#0033AA]/30 hover:shadow-lg hover:-translate-y-1"
                                     :class="selectedPayment?.id === 'balance' ? 'card-selected animate-bounce-short' : 'border-gray-100 bg-white'"
                                     :disabled="!canUseBalance">

                                    <div x-show="selectedPayment?.id === 'balance'" class="badge-check"><i class="fa-solid fa-check"></i></div>
                                    <div class="absolute top-0 left-0 bg-green-500 text-white text-[9px] font-bold px-2 py-1 rounded-br-xl z-10 shadow-sm">BEST</div>

                                    <div class="flex items-center gap-4 flex-1 overflow-hidden">
                                        <div class="w-12 h-8 bg-white border border-gray-100 rounded-lg flex items-center justify-center p-1 shadow-sm shrink-0">
                                            <i class="fa-solid fa-wallet text-[#0033AA]"></i>
                                        </div>
                                        <div class="flex flex-col truncate">
                                            <span class="text-xs font-bold text-slate-800 truncate">Saldo {{ config('app.name') }}</span>
                                            <span class="text-[10px] text-slate-400 truncate mt-0.5">Biaya: Gratis</span>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 pl-2">
                                        <p class="text-sm font-extrabold text-[#0033AA]" x-text="formatRupiah(selectedVariant?.price || 0)"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endauth

                        <!-- QRIS / E-Wallet -->
                        <div class="border border-gray-100 rounded-[24px] overflow-hidden bg-white shadow-sm hover:shadow-md transition-all">
                            <button @click="togglePaymentGroup('qris')"
                                    class="w-full px-5 py-4 bg-gray-50/50 flex items-center justify-between transition-colors hover:bg-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-slate-500 shadow-sm">
                                        <i class="fa-solid fa-qrcode text-xs"></i>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">QRIS / E-Wallet</h4>
                                </div>
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-300" :class="activePaymentGroups.includes('qris') ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="activePaymentGroups.includes('qris')" x-collapse class="p-4 space-y-3 bg-white border-t border-gray-100">
                                <div @click="selectPayment('qris', 'QRIS (All Payment)', 0.007, 'percent'); errors.payment = false"
                                     class="border rounded-[20px] p-4 cursor-pointer relative overflow-hidden touch-effect transition-all duration-300 group flex items-center justify-between hover:border-[#0033AA]/30 hover:shadow-lg hover:-translate-y-1"
                                     :class="selectedPayment?.id === 'qris' ? 'card-selected animate-bounce-short' : 'border-gray-100 bg-white'">

                                    <div x-show="selectedPayment?.id === 'qris'" class="badge-check"><i class="fa-solid fa-check"></i></div>

                                    <div class="flex items-center gap-4 flex-1 overflow-hidden">
                                        <div class="w-12 h-8 bg-white border border-gray-100 rounded-lg flex items-center justify-center p-1 shadow-sm shrink-0">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" class="h-full object-contain">
                                        </div>
                                        <div class="flex flex-col truncate">
                                            <span class="text-xs font-bold text-slate-800 truncate">QRIS (All Payment)</span>
                                            <span class="text-[10px] text-slate-400 truncate mt-0.5">Biaya: 0.7%</span>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 pl-2">
                                        <p class="text-sm font-extrabold text-[#0033AA]" x-text="calculateFinalPrice(0.007, 'percent')"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Virtual Account -->
                        <div class="border border-gray-100 rounded-[24px] overflow-hidden bg-white shadow-sm hover:shadow-md transition-all"
                             :class="selectedVariant?.price < 10000 ? 'payment-disabled' : ''">
                            <button @click="togglePaymentGroup('va')"
                                    class="w-full px-5 py-4 bg-gray-50/50 flex items-center justify-between transition-colors hover:bg-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-slate-500 shadow-sm">
                                        <i class="fa-solid fa-building-columns text-xs"></i>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Virtual Account</h4>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span x-show="selectedVariant?.price < 10000" class="text-[9px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold">Min 10rb</span>
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-300" :class="activePaymentGroups.includes('va') ? 'rotate-180' : ''"></i>
                                </div>
                            </button>

                            <div x-show="activePaymentGroups.includes('va')" x-collapse class="p-4 space-y-3 bg-white border-t border-gray-100">
                                @foreach(['BCA' => 4000, 'BRI' => 3000, 'BNI' => 3500, 'Mandiri' => 4000] as $bank => $fee)
                                <div @click="selectPayment('{{ strtolower($bank) }}', '{{ $bank }} VA', {{ $fee }}, 'flat'); errors.payment = false"
                                     class="border rounded-[20px] p-4 cursor-pointer relative overflow-hidden touch-effect transition-all duration-300 group flex items-center justify-between hover:border-[#0033AA]/30 hover:shadow-lg hover:-translate-y-1"
                                     :class="selectedPayment?.id === '{{ strtolower($bank) }}' ? 'card-selected animate-bounce-short' : 'border-gray-100 bg-white'">

                                    <div x-show="selectedPayment?.id === '{{ strtolower($bank) }}'" class="badge-check"><i class="fa-solid fa-check"></i></div>

                                    <div class="flex items-center gap-4 flex-1 overflow-hidden">
                                        <div class="w-12 h-8 bg-white border border-gray-100 rounded-lg flex items-center justify-center p-1 shadow-sm shrink-0">
                                            <span class="text-xs font-bold text-slate-600">{{ $bank }}</span>
                                        </div>
                                        <div class="flex flex-col truncate">
                                            <span class="text-xs font-bold text-slate-800 truncate">{{ $bank }} Virtual Account</span>
                                            <span class="text-[10px] text-slate-400 truncate mt-0.5">Biaya: Rp {{ number_format($fee, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 pl-2">
                                        <p class="text-sm font-extrabold text-[#0033AA]" x-text="calculateFinalPrice({{ $fee }}, 'flat')"></p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Order Data -->
            <div id="section-userdata" class="card-ios overflow-hidden transition-all duration-300 item-animate" style="animation-delay: 300ms;">
                <div class="bg-gray-50/80 backdrop-blur-sm px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#0033AA] text-white flex items-center justify-center font-bold text-sm shadow-lg shadow-[#0033AA]/20">3</div>
                        <h3 class="font-bold text-slate-800 text-base">Data Pesanan</h3>
                    </div>
                    <div class="flex gap-2">
                        <button @click="saveUserData" class="text-xs bg-white text-blue-600 border border-gray-200 px-3 py-1.5 rounded-full hover:bg-gray-50 transition font-bold touch-effect shadow-sm"><i class="fa-solid fa-floppy-disk"></i></button>
                        <button @click="clearUserData" class="text-xs bg-white text-red-600 border border-gray-200 px-3 py-1.5 rounded-full hover:bg-gray-50 transition font-bold touch-effect shadow-sm"><i class="fa-solid fa-eraser"></i></button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-5">
                        <div class="group">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block ml-1">User ID</label>
                            <input type="text" x-model="userId" @input="cleanInput" placeholder="Masukkan User ID"
                                   class="w-full px-5 py-4 rounded-2xl input-ios text-base font-bold text-slate-800 outline-none placeholder-slate-400"
                                   :class="{'animate-error-loop': errors.userId}">
                        </div>
                        <div class="group">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block ml-1">Zone ID</label>
                            <input type="text" x-model="serverId" @input="cleanInput" placeholder="Zone ID"
                                   class="w-full px-5 py-4 rounded-2xl input-ios text-base font-bold text-slate-800 outline-none placeholder-slate-400"
                                   :class="{'animate-error-loop': errors.serverId}">
                        </div>
                    </div>

                    <!-- Cek Akun Otomatis -->
                    <div class="mt-4">
                        <button @click="checkAccount()"
                                :disabled="checkingAccount || !userId || !serverId || accountCheckLimitReached"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 touch-effect"
                                :class="accountCheckLimitReached
                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    : (!userId || !serverId)
                                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                        : 'bg-[#0033AA]/10 text-[#0033AA] hover:bg-[#0033AA]/20 active:scale-95'">
                            <template x-if="checkingAccount">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-spinner animate-spin"></i> Mengecek...
                                </span>
                            </template>
                            <template x-if="!checkingAccount">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <span>Cek Akun Otomatis</span>
                                    <span class="text-[10px] opacity-70" x-text="'(' + accountChecksRemaining + '/5)'"></span>
                                </span>
                            </template>
                        </button>

                        <!-- Account Check Result -->
                        <div x-show="accountResult !== null" x-transition class="mt-3">
                            <template x-if="accountResult?.success">
                                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3 check-pulse">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-extrabold text-emerald-900" x-text="accountResult.nickname"></p>
                                        <p class="text-[11px] text-emerald-700 font-medium" x-show="accountResult.region" x-text="'Region ' + accountResult.region"></p>
                                    </div>
                                </div>
                            </template>
                            <template x-if="accountResult && !accountResult.success">
                                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-amber-900">Akun tidak ditemukan</p>
                                        <p class="text-[11px] text-amber-700 font-medium">Pastikan User ID & Zone ID benar. Anda tetap bisa melanjutkan order.</p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mt-5 bg-yellow-50/70 border border-yellow-100 p-4 rounded-2xl flex gap-3 items-start">
                        <i class="fa-solid fa-triangle-exclamation text-yellow-600 text-lg mt-0.5"></i>
                        <p class="text-sm text-slate-700 leading-relaxed font-bold">
                            Pastikan telah mengisi Data dengan benar.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Step 4: Buyer Data -->
            <div id="section-contact" class="card-ios overflow-hidden transition-all duration-300 item-animate" style="animation-delay: 400ms;">
                <div class="bg-gray-50/80 backdrop-blur-sm px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#0033AA] text-white flex items-center justify-center font-bold text-sm shadow-lg shadow-[#0033AA]/20">4</div>
                        <h3 class="font-bold text-slate-800 text-base">Data Pembeli</h3>
                    </div>
                    <div class="flex gap-2">
                        <button @click="saveContactData" class="text-xs bg-white text-blue-600 border border-gray-200 px-3 py-1.5 rounded-full hover:bg-gray-50 transition font-bold touch-effect shadow-sm"><i class="fa-solid fa-floppy-disk"></i></button>
                        <button @click="clearContactData" class="text-xs bg-white text-red-600 border border-gray-200 px-3 py-1.5 rounded-full hover:bg-gray-50 transition font-bold touch-effect shadow-sm"><i class="fa-solid fa-eraser"></i></button>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="group">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block ml-1">WhatsApp <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none z-10">
                                <i class="fa-brands fa-whatsapp text-lg text-green-500"></i>
                            </div>
                            <input type="tel" x-model="whatsapp" @input="formatWA" placeholder="08xxxxxxxxxx"
                                   class="w-full pl-14 pr-5 py-4 rounded-2xl input-ios text-base font-bold text-slate-800 outline-none placeholder-slate-400"
                                   :class="{'animate-error-loop': errors.whatsapp}">
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block ml-1">Email <span class="text-slate-300 font-normal italic">(Opsional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <i class="fa-regular fa-envelope text-lg text-slate-400"></i>
                            </div>
                            <input type="email" x-model="email" @input="formatEmail" placeholder="nama@email.com"
                                   class="w-full pl-14 pr-5 py-4 rounded-2xl input-ios text-base font-bold text-slate-800 outline-none placeholder-slate-400">
                        </div>
                    </div>

                    <div class="flex items-start gap-3 mt-4">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" x-model="agreeTOS" class="hidden">
                            <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all"
                                 :class="agreeTOS ? 'bg-[#0033AA] border-[#0033AA]' : 'border-slate-300 bg-white',
                                         errors.tos ? 'animate-error-loop' : ''">
                                <i class="fa-solid fa-check text-white text-xs" x-show="agreeTOS"></i>
                            </div>
                            <span class="text-xs font-semibold text-slate-600 group-hover:text-[#0033AA] transition">Saya telah menyetujui syarat & ketentuan yg berlaku.</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-4 bg-emerald-50/70 p-4 rounded-2xl border border-emerald-100/70">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                            <i class="fa-solid fa-receipt text-lg"></i>
                        </div>
                        <p class="text-xs text-emerald-900 font-medium leading-relaxed">
                            Invoice bukti transaksi akan dikirimkan otomatis ke WhatsApp & Email Anda.
                        </p>
                    </div>

                    <button @click="processOrder()"
                            class="w-full bg-gradient-to-r from-[#0033AA] to-[#002288] hover:to-[#0033AA] text-white btn-pulse shadow-xl shadow-[#0033AA]/30 font-bold py-4 rounded-2xl text-base transition-all touch-effect flex items-center justify-center gap-3">
                        <span>BELI SEKARANG</span>
                        <i class="fa-solid fa-arrow-right animate-bounce"></i>
                    </button>
                </div>
            </div>

        </div>
    </main>

    <!-- Sticky Bottom Bar -->
    <div class="fixed bottom-0 left-1/2 transform -translate-x-1/2 w-full max-w-[1440px] z-50 pointer-events-none" x-cloak>
        <div class="bg-white/90 backdrop-blur-xl border-t border-white/20 shadow-[0_-10px_40px_rgba(0,0,0,0.1)] rounded-t-[36px] p-5 lg:px-8 pointer-events-auto transition-transform duration-300 min-h-[90px] flex items-center justify-between gap-6 border-x border-white/20">

            <div class="flex flex-col flex-1 min-w-0">
                <template x-if="selectedVariant">
                    <div class="text-xs font-bold text-slate-600 mb-1 truncate flex items-center gap-2">
                        <span class="bg-[#0033AA]/10 text-[#0033AA] px-2 py-0.5 rounded-md text-[10px] font-bold">ITEM</span>
                        <span x-text="selectedVariant.name"></span>
                    </div>
                </template>
                <template x-if="!selectedVariant">
                    <div class="text-xs font-medium text-slate-400 italic mb-1">
                        Pilih nominal dulu...
                    </div>
                </template>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-extrabold text-[#0033AA] leading-none tracking-tight" x-text="finalPriceDisplay"></span>
                </div>
            </div>

            <button @click="processOrder()"
                    class="flex-[1.3] max-w-[280px] bg-gradient-to-r from-[#0033AA] to-[#002288] hover:to-[#0033AA] text-white btn-pulse shadow-xl shadow-[#0033AA]/30 font-bold py-3.5 px-6 rounded-xl text-sm lg:text-base transition-all touch-effect flex items-center justify-center gap-2 hover:-translate-y-1">
                <span>BELI SEKARANG</span>
                <i class="fa-solid fa-arrow-right animate-bounce"></i>
            </button>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="successModal.show" x-cloak
         @click="successModal.show = false"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-emerald-900/40 backdrop-blur-md transition-opacity"
         x-transition.opacity>
        <div @click.stop class="bg-white rounded-[32px] p-8 w-full max-w-sm text-center shadow-2xl relative border border-white/50">
            <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-500 text-5xl shadow-inner animate-bounce">
                <i class="fa-solid fa-floppy-disk"></i>
            </div>
            <h3 class="font-heading font-extrabold text-2xl text-slate-900 mb-3">BERHASIL!</h3>
            <p class="text-base text-slate-600 mb-8 leading-relaxed font-bold" x-text="successModal.message"></p>
            <button @click="successModal.show = false" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 rounded-2xl text-lg transition touch-effect shadow-lg shadow-emerald-500/20">
                OK, MANTAP!
            </button>
        </div>
    </div>

    <!-- Error Modal -->
    <div x-show="errorModal.show" x-cloak
         @click="closeErrorModal"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-md transition-opacity"
         x-transition.opacity>

        <div @click.stop class="bg-white rounded-[32px] p-8 w-full max-w-sm text-center shadow-2xl relative border border-white/50">
            <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 text-red-500 text-5xl shadow-inner animate-bounce">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="font-heading font-extrabold text-2xl text-slate-900 mb-3">PERHATIAN!</h3>
            <p class="text-base text-slate-600 mb-8 leading-relaxed font-bold" x-text="errorModal.message"></p>
            <button @click="closeErrorModal" class="w-full bg-[#0033AA] hover:bg-[#002288] text-white font-bold py-4 rounded-2xl text-lg transition touch-effect shadow-lg shadow-[#0033AA]/20">
                BAIK, MENGERTI!
            </button>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div x-show="isLoading" class="fixed inset-0 z-[80] flex items-center justify-center bg-white/80 backdrop-blur-xl transition-opacity" x-transition.opacity x-cloak>
        <div class="flex flex-col items-center">
            <div class="relative w-20 h-20 mb-6">
                <div class="absolute inset-0 border-4 border-slate-200 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-[#0033AA] rounded-full border-t-transparent animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid fa-bolt text-[#0033AA] text-xl animate-pulse"></i>
                </div>
            </div>
            <p class="font-heading font-bold text-slate-800 text-xl">Memproses...</p>
            <p class="text-sm text-slate-500 mt-2">Mohon tunggu sebentar</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productOrder() {
    return {
        // Data fields
        userId: '',
        serverId: '',
        whatsapp: '{{ auth()->check() ? auth()->user()->phone : "" }}',
        email: '{{ auth()->check() ? auth()->user()->email : "" }}',
        agreeTOS: false,
        selectedVariant: null,
        selectedPayment: null,
        activePaymentGroups: [],
        isLoading: false,
        isFav: false,
        searchOpen: false,

        // Errors & modals
        errors: { userId: false, serverId: false, product: false, payment: false, whatsapp: false, tos: false },
        errorModal: { show: false, message: '', target: '' },
        successModal: { show: false, message: '' },

        // Account check
        checkingAccount: false,
        accountResult: null,
        accountCheckTimestamps: [],

        // Product info for localStorage keys
        productSlug: '{{ $product->slug }}',
        gameCode: '{{ $product->provider_code ?? "" }}',

        @auth
        userBalance: {{ auth()->user()->balance }},
        @else
        userBalance: 0,
        @endauth

        get finalPriceDisplay() {
            if (!this.selectedVariant) return 'Rp 0';
            let price = this.selectedVariant.price;
            if (this.selectedPayment) {
                price += (this.selectedPayment.feeType === 'percent' ? Math.ceil(price * this.selectedPayment.fee) : this.selectedPayment.fee);
            }
            return this.formatRupiah(price);
        },

        get canUseBalance() {
            return this.selectedVariant && this.userBalance >= this.selectedVariant.price;
        },

        get accountChecksRemaining() {
            const now = Date.now();
            const oneMinuteAgo = now - 60000;
            this.accountCheckTimestamps = this.accountCheckTimestamps.filter(t => t > oneMinuteAgo);
            return Math.max(0, 5 - this.accountCheckTimestamps.length);
        },

        get accountCheckLimitReached() {
            return this.accountChecksRemaining <= 0;
        },

        init() {
            // Load per-product user data
            const savedUser = localStorage.getItem('order_data_' + this.productSlug);
            if (savedUser) {
                try {
                    const data = JSON.parse(savedUser);
                    this.userId = data.userId || '';
                    this.serverId = data.serverId || '';
                } catch(e) {}
            }

            // Load per-product contact data
            const savedContact = localStorage.getItem('order_contact_' + this.productSlug);
            if (savedContact) {
                try {
                    const data = JSON.parse(savedContact);
                    if (!this.whatsapp) this.whatsapp = data.whatsapp || '';
                    if (!this.email) this.email = data.email || '';
                    this.agreeTOS = data.agreeTOS || false;
                } catch(e) {}
            }

            // Load rate limit timestamps
            const savedChecks = localStorage.getItem('account_check_timestamps');
            if (savedChecks) {
                try {
                    this.accountCheckTimestamps = JSON.parse(savedChecks).filter(t => t > Date.now() - 60000);
                } catch(e) {}
            }

            // Remove animation classes after initial load
            setTimeout(() => {
                document.querySelectorAll('.item-animate').forEach(el => el.classList.add('animation-finished'));
            }, 1000);
        },

        // Auto-save to localStorage
        saveToLocal() {
            localStorage.setItem('order_data_' + this.productSlug, JSON.stringify({
                userId: this.userId,
                serverId: this.serverId
            }));
        },

        // Input formatting
        cleanInput(e) {
            const val = e.target.value;
            const clean = val.replace(/[^a-zA-Z0-9@._]/g, '');
            const modelName = e.target.getAttribute('x-model');
            if (modelName === 'userId') { this.userId = clean; if (this.userId) this.errors.userId = false; }
            if (modelName === 'serverId') { this.serverId = clean; if (this.serverId) this.errors.serverId = false; }
            this.saveToLocal();
            this.accountResult = null; // Reset check result on input change
        },

        formatWA(e) {
            this.whatsapp = e.target.value.replace(/[^0-9]/g, '');
            if (this.whatsapp) this.errors.whatsapp = false;
        },

        formatEmail(e) {
            this.email = e.target.value.replace(/[^a-zA-Z0-9.@_]/g, '');
        },

        // Save/Clear buttons
        saveUserData() {
            if (!this.userId || !this.serverId) {
                this.showError('Mohon isi data terlebih dahulu.', 'section-userdata');
                return;
            }
            this.saveToLocal();
            this.successModal = { show: true, message: 'Data Pesanan Berhasil Disimpan!' };
        },

        clearUserData() {
            this.userId = '';
            this.serverId = '';
            this.accountResult = null;
            localStorage.removeItem('order_data_' + this.productSlug);
        },

        saveContactData() {
            if (!this.whatsapp) {
                this.showError('Isi Nomor WhatsApp dulu!', 'section-contact');
                return;
            }
            if (this.whatsapp.length < 8) {
                this.showError('Mohon masukan nomor yg valid!', 'section-contact');
                return;
            }
            localStorage.setItem('order_contact_' + this.productSlug, JSON.stringify({
                whatsapp: this.whatsapp,
                email: this.email,
                agreeTOS: this.agreeTOS
            }));
            this.successModal = { show: true, message: 'Data Kontak Berhasil Disimpan!' };
        },

        clearContactData() {
            this.whatsapp = '';
            this.email = '';
            this.agreeTOS = false;
            localStorage.removeItem('order_contact_' + this.productSlug);
        },

        // Account check via MVStore
        async checkAccount() {
            if (!this.userId || !this.serverId) {
                this.showError('Mohon isi User ID dan Zone ID terlebih dahulu.', 'section-userdata');
                return;
            }

            if (this.accountCheckLimitReached) {
                this.showError('Batas pengecekan tercapai. Silakan tunggu 1 menit.', 'section-userdata');
                return;
            }

            this.checkingAccount = true;
            this.accountResult = null;

            // Record timestamp for rate limiting
            this.accountCheckTimestamps.push(Date.now());
            localStorage.setItem('account_check_timestamps', JSON.stringify(this.accountCheckTimestamps));

            try {
                const response = await fetch('{{ route("order.validate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: this.userId,
                        server_id: this.serverId,
                        game_code: this.gameCode || 'MLGP'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.accountResult = {
                        success: true,
                        nickname: data.data.nickname,
                        region: data.data.region || ''
                    };
                } else {
                    this.accountResult = {
                        success: false,
                        error: data.error || 'Akun tidak ditemukan'
                    };
                }
            } catch (error) {
                this.accountResult = {
                    success: false,
                    error: 'Gagal mengecek akun. Silakan coba lagi.'
                };
            } finally {
                this.checkingAccount = false;
            }
        },

        selectVariant(id, name, price) {
            this.selectedVariant = { id, name, price };
            this.activePaymentGroups = ['qris'];
            @auth
            this.activePaymentGroups.push('saldo');
            @endauth

            if (this.selectedPayment && price < 10000 && !['balance', 'qris'].includes(this.selectedPayment.id)) {
                this.selectedPayment = null;
            }
        },

        selectPayment(id, name, fee, feeType) {
            if (!this.selectedVariant) return;

            @auth
            if (id === 'balance' && !this.canUseBalance) {
                this.showError('Saldo tidak mencukupi untuk produk ini.');
                return;
            }
            @endauth

            this.selectedPayment = { id, name, fee, feeType };
        },

        togglePaymentGroup(group) {
            if (this.activePaymentGroups.includes(group)) {
                this.activePaymentGroups = this.activePaymentGroups.filter(g => g !== group);
            } else {
                this.activePaymentGroups.push(group);
            }
        },

        calculateFinalPrice(fee, feeType) {
            if (!this.selectedVariant) return 'Rp -';
            let price = this.selectedVariant.price;
            price += (feeType === 'percent' ? Math.ceil(price * fee) : fee);
            return this.formatRupiah(price);
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number).replace("Rp", "Rp ");
        },

        showError(msg, target = '') {
            this.errorModal = { show: true, message: msg, target: target };
        },

        closeErrorModal() {
            this.errorModal.show = false;
            if (this.errorModal.target) {
                setTimeout(() => {
                    const el = document.getElementById(this.errorModal.target);
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        },

        processOrder() {
            this.errors = { userId: false, serverId: false, product: false, payment: false, whatsapp: false, tos: false };

            if (!this.selectedVariant) {
                this.errors.product = true;
                this.showError('Pilih "Nominal" terlebih dahulu.', 'section-nominal');
                return;
            }

            if (!this.selectedPayment) {
                this.errors.payment = true;
                this.showError('Pilih "Metode Pembayaran" terlebih dahulu.', 'section-payment');
                return;
            }

            if (!this.userId || !this.serverId) {
                if (!this.userId) this.errors.userId = true;
                if (!this.serverId) this.errors.serverId = true;
                this.showError('Mohon isi "Data Pesanan" terlebih dahulu.', 'section-userdata');
                return;
            }

            if (!this.whatsapp) {
                this.errors.whatsapp = true;
                this.showError('Nomor WhatsApp wajib diisi.', 'section-contact');
                return;
            }

            if (this.whatsapp.length < 7) {
                this.errors.whatsapp = true;
                this.showError('Mohon masukkan nomor yang valid.', 'section-contact');
                return;
            }

            if (this.email && !this.email.includes('@')) {
                this.showError('Format email tidak valid!', 'section-contact');
                return;
            }

            if (!this.agreeTOS) {
                this.errors.tos = true;
                this.showError('Mohon centang syarat & ketentuan yang berlaku.', 'section-contact');
                return;
            }

            // Save data before order
            this.saveToLocal();
            localStorage.setItem('order_contact_' + this.productSlug, JSON.stringify({
                whatsapp: this.whatsapp,
                email: this.email,
                agreeTOS: this.agreeTOS
            }));

            this.isLoading = true;

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('product_id', {{ $product->id }});
            formData.append('variant_id', this.selectedVariant.id);
            formData.append('customer_name', this.userId);
            formData.append('customer_phone', this.whatsapp);
            formData.append('customer_email', this.email);
            formData.append('customer_no', this.userId + '(' + this.serverId + ')');
            formData.append('payment_method', this.selectedPayment.id);
            formData.append('quantity', 1);

            fetch('{{ route("order.create") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                this.isLoading = false;
                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    this.showError(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                }
            })
            .catch(error => {
                this.isLoading = false;
                this.showError('Terjadi kesalahan. Silakan coba lagi.');
            });
        }
    }
}
</script>
@endpush
