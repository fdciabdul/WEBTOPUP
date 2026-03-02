@extends('layouts.main')

@section('title', 'Pembayaran #' . $transaction->order_id . ' - ' . config('app.name'))

@push('styles')
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<style>
    /* Hide main layout header */
    .header { display: none !important; }
    #scroll-top-btn { display: none !important; }

    /* Animations */
    @keyframes slideInUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .slide-up { animation: slideInUp 0.5s ease-out forwards; }

    @keyframes scanLine { 0% { top: 0%; opacity: 0; } 50% { opacity: 1; } 100% { top: 100%; opacity: 0; } }
    .scan-line { position: absolute; left: 0; width: 100%; height: 4px; background: #ef4444; box-shadow: 0 0 20px #ef4444; animation: scanLine 2.5s infinite; z-index: 10; }

    @keyframes shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
    .animate-shimmer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to right, transparent 0%, rgba(255,255,255,0.4) 50%, transparent 100%); animation: shimmer 2s infinite; pointer-events: none; }

    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    .animate-shake { animation: shake 0.3s ease-in-out; border-color: #ef4444 !important; color: #ef4444 !important; }

    .card-glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
    .bg-pattern { background-image: radial-gradient(#0033AA 0.5px, transparent 0.5px), radial-gradient(#0033AA 0.5px, #F4F5FA 0.5px); background-size: 24px 24px; opacity: 0.05; }

    .touch-effect { transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .touch-effect:active { transform: scale(0.96); }

    .accordion-content { transition: max-height 0.3s ease-out, opacity 0.3s ease-out; max-height: 0; opacity: 0; overflow: hidden; }
    .accordion-content.active { max-height: 1000px; opacity: 1; }

    .invoice-scroll::-webkit-scrollbar { width: 6px; }
    .invoice-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
    .invoice-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
@endpush

@section('content')
<!-- Sticky Header -->
<header class="w-full bg-[#0033AA]/95 backdrop-blur-xl shadow-lg sticky top-0 z-50 border-b border-white/10">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3 text-white touch-effect group">
            <div class="w-10 h-10 bg-white/15 rounded-full flex items-center justify-center group-active:bg-white/25 transition border border-white/10 backdrop-blur-md">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg font-heading font-bold leading-tight uppercase tracking-wider">{{ config('app.name') }}</h1>
                <p class="text-xs text-white/80 font-medium tracking-wide">Official Store</p>
            </div>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ \App\Models\Setting::get('whatsapp_url', 'https://wa.me/6281234567890') }}" target="_blank" class="w-10 h-10 rounded-full flex items-center justify-center text-white hover:bg-white/10 touch-effect transition relative">
                <i class="fa-solid fa-headset text-lg"></i>
                <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-green-400 rounded-full border-2 border-[#0033AA] animate-pulse"></span>
            </a>
        </div>
    </div>
</header>

<div class="absolute inset-0 bg-pattern z-0 pointer-events-none"></div>

<div x-data="paymentLogic()" x-init="initPayment()" class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6 relative z-10 pb-32">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10">

        <!-- Left Column (7/12) -->
        <div class="lg:col-span-7 order-1 space-y-6">

            <!-- Timer: UNPAID -->
            <div x-show="paymentStatus === 'UNPAID'" class="bg-gradient-to-r from-blue-700 to-[#0033AA] rounded-[24px] p-6 shadow-lg shadow-blue-900/20 slide-up relative overflow-hidden text-white" style="animation-delay: 0.1s;">
                <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                <div class="flex flex-row items-center justify-between gap-4 relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/20">
                            <i class="fa-regular fa-clock text-xl animate-[pulse_2s_infinite]"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-blue-100 uppercase tracking-wide opacity-80">Sisa Waktu Pembayaran</p>
                            <p class="text-3xl font-mono font-bold tracking-tight tabular-nums" x-text="formatTime(timeLeft)"></p>
                        </div>
                    </div>
                    <div class="bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/10">
                        <span class="text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-400 animate-ping"></span>
                            Menunggu
                        </span>
                    </div>
                </div>
            </div>

            <!-- Timer: PAID -->
            <div x-show="paymentStatus === 'PAID'" class="bg-gradient-to-r from-green-600 to-emerald-700 rounded-[24px] p-6 shadow-lg shadow-green-900/20 slide-up relative overflow-hidden text-white" x-cloak>
                <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                <div class="flex flex-row items-center justify-between gap-4 relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/20">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-green-100 uppercase tracking-wide opacity-80">Status Pembayaran</p>
                            <p class="text-2xl font-bold tracking-tight">Pembayaran Berhasil</p>
                        </div>
                    </div>
                    <div class="bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/10">
                        <span class="text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-white"></span>
                            LUNAS
                        </span>
                    </div>
                </div>
            </div>

            <!-- Timer: EXPIRED -->
            <div x-show="paymentStatus === 'EXPIRED'" class="bg-red-600 rounded-[24px] p-6 shadow-lg shadow-red-900/20 slide-up relative overflow-hidden text-white" x-cloak>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/20">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Waktu Pembayaran Habis!</p>
                        <p class="text-xs text-red-100">Silahkan lakukan pemesanan ulang.</p>
                    </div>
                </div>
            </div>

            <!-- Main Payment Card -->
            <div class="card-glass rounded-[32px] p-6 lg:p-8 slide-up relative overflow-hidden shadow-xl" style="animation-delay: 0.2s;">

                <!-- PAID State -->
                <div x-show="paymentStatus === 'PAID'" class="text-center py-8" x-cloak>
                    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                        <div class="absolute inset-0 rounded-full bg-green-400 animate-ping opacity-20"></div>
                        <i class="fa-solid fa-check text-4xl text-green-600"></i>
                    </div>
                    <h2 class="font-heading font-extrabold text-3xl text-slate-800 mb-2">Pembayaran Sukses!</h2>
                    <p class="text-slate-500 mb-8 max-w-sm mx-auto">Terima kasih. Pesanan Anda telah dikonfirmasi dan invoice siap diunduh.</p>
                    <div class="space-y-3">
                        <button @click="showInvoice = true" class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl shadow-xl hover:bg-slate-800 transition flex items-center justify-center gap-2 touch-effect group">
                            <i class="fa-solid fa-file-invoice group-hover:scale-110 transition-transform"></i> Lihat & Download Invoice
                        </button>
                        @auth
                        <a href="{{ route('dashboard.transactions') }}" class="block w-full bg-[#0033AA] text-white font-bold py-4 rounded-2xl shadow-xl hover:bg-[#002288] transition text-center touch-effect">
                            <i class="fa-solid fa-receipt mr-2"></i> Lihat Riwayat Transaksi
                        </a>
                        @endauth
                    </div>
                </div>

                <!-- EXPIRED State -->
                <div x-show="paymentStatus === 'EXPIRED'" class="text-center py-10" x-cloak>
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 grayscale opacity-60">
                        <i class="fa-regular fa-calendar-xmark text-4xl text-slate-500"></i>
                    </div>
                    <h2 class="font-heading font-extrabold text-2xl text-slate-400 mb-2">Invoice Kadaluarsa</h2>
                    <p class="text-slate-400 mb-6 text-sm">Maaf, sesi pembayaran Anda telah berakhir.</p>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-slate-100 text-slate-600 px-6 py-3 rounded-xl font-bold hover:bg-slate-200 transition">
                        <i class="fa-solid fa-rotate-right"></i> Buat Pesanan Baru
                    </a>
                </div>

                <!-- UNPAID State -->
                <div x-show="paymentStatus === 'UNPAID'">

                    <!-- Payment Method Header -->
                    <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 border border-gray-100 rounded-2xl bg-white shadow-sm">
                                <img :src="getMethodLogo()" class="h-8 w-auto object-contain">
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Metode Pembayaran</span>
                                <span class="text-xl font-bold text-slate-800 leading-none">{{ ucfirst($transaction->payment_method) }}</span>
                            </div>
                        </div>
                    </div>

                    @if($transaction->payment_method === 'qris')
                    <!-- QRIS Display -->
                    <div class="flex flex-col items-center">
                        <div class="relative group cursor-pointer w-full max-w-[280px] mx-auto mb-4">
                            <div class="bg-white p-4 rounded-[32px] border-[3px] border-slate-100 shadow-2xl shadow-slate-200/50 relative overflow-hidden">
                                <div class="scan-line"></div>
                                @if(isset($transaction->payment_reference))
                                <img src="{{ $transaction->payment_reference }}" class="w-full h-auto object-contain block mx-auto rounded-xl" alt="QRIS Code">
                                @else
                                <div class="w-full aspect-square bg-gray-100 rounded-xl flex items-center justify-center">
                                    <i class="fa-solid fa-qrcode text-6xl text-gray-300"></i>
                                </div>
                                @endif
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-lg shadow-sm border border-gray-100">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" class="h-5 w-auto">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mb-3">Scan QR Code menggunakan E-Wallet atau M-Banking</p>
                    </div>
                    @endif

                    @if(in_array($transaction->payment_method, ['bca', 'bri', 'bni', 'mandiri']))
                    <!-- Virtual Account Display -->
                    <div class="space-y-6">
                        <div class="bg-slate-50 rounded-[24px] p-6 border border-slate-200 text-center">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Nomor Virtual Account</p>
                            <div class="flex items-center justify-center gap-3 mb-2">
                                <p class="text-3xl font-heading font-extrabold text-slate-800 tracking-wide font-mono">{{ $transaction->payment_reference ?? '-' }}</p>
                                <button @click="copyText('{{ $transaction->payment_reference ?? '' }}')" class="text-[#0033AA] bg-white p-2.5 rounded-xl shadow-sm hover:scale-110 transition border border-slate-200 touch-effect"><i class="fa-regular fa-copy"></i></button>
                            </div>
                            <p class="text-xs text-slate-400">Dicek Otomatis</p>
                        </div>
                    </div>
                    @endif

                    <!-- Total Payment -->
                    <div class="bg-white rounded-[24px] p-5 border border-gray-100 flex items-center justify-between shadow-sm mt-6">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Tagihan</p>
                            <p class="text-xl font-bold text-[#0033AA]">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <button @click="copyText('{{ $transaction->total_amount }}')" class="text-slate-500 hover:text-[#0033AA] transition font-bold text-xs bg-gray-50 px-4 py-2.5 rounded-xl border border-gray-200 touch-effect">Salin Jumlah</button>
                    </div>

                    @if($transaction->payment_method === 'midtrans' && isset($snap_token))
                    <!-- Midtrans Pay Button -->
                    <div class="mt-6">
                        <button id="pay-button"
                                class="w-full bg-gradient-to-r from-[#0033AA] to-[#002288] text-white font-bold py-4 rounded-2xl shadow-xl shadow-[#0033AA]/40 hover:shadow-[#0033AA]/60 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group touch-effect">
                            <div class="animate-shimmer"></div>
                            <span class="relative z-10 flex items-center justify-center gap-2 text-lg">
                                <i class="fa-solid fa-credit-card"></i>
                                Bayar Sekarang
                                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </span>
                        </button>
                    </div>
                    @else
                    <!-- Check Status Button -->
                    <div class="mt-6">
                        <button @click="checkPaymentStatus()"
                                :disabled="isLoading"
                                class="w-full bg-gradient-to-r from-[#0033AA] to-[#002288] text-white font-bold py-4 rounded-2xl shadow-xl shadow-[#0033AA]/40 hover:shadow-[#0033AA]/60 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group touch-effect disabled:opacity-75 disabled:cursor-not-allowed disabled:transform-none">
                            <div class="animate-shimmer" x-show="!isLoading"></div>
                            <span class="relative z-10 flex items-center justify-center gap-2 text-lg">
                                <template x-if="isLoading">
                                    <span class="flex items-center gap-3">
                                        <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                                        <span>Mengecek Pembayaran...</span>
                                    </span>
                                </template>
                                <template x-if="!isLoading">
                                    <span class="flex items-center gap-2">
                                        <span>Cek Status Pembayaran</span>
                                        <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                    </span>
                                </template>
                            </span>
                        </button>
                    </div>
                    @endif

                    <p class="text-[10px] text-center text-slate-400 mt-3 font-medium flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-green-500"></i> Transaksi Aman & Terenkripsi SSL
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column (5/12) -->
        <div class="lg:col-span-5 order-2">
            <div class="sticky top-24 space-y-4">

                <!-- Order Status Info -->
                <div class="bg-white rounded-[24px] shadow-lg shadow-slate-200/50 overflow-hidden slide-up border border-slate-100 p-5 relative" style="animation-delay: 0.05s;">
                    <div class="flex items-center gap-3 mb-4 border-b border-dashed border-gray-100 pb-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-[#0033AA] flex items-center justify-center">
                            <i class="fa-solid fa-circle-info text-sm"></i>
                        </div>
                        <h3 class="font-heading font-bold text-slate-800 text-sm">Informasi Pesanan</h3>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500 font-medium">Status Pembayaran</span>
                            <span class="font-bold px-2 py-1 rounded-md text-[10px] uppercase tracking-wider"
                                  :class="{
                                      'bg-yellow-100 text-yellow-700': paymentStatus === 'UNPAID',
                                      'bg-green-100 text-green-700': paymentStatus === 'PAID',
                                      'bg-red-100 text-red-700': paymentStatus === 'EXPIRED'
                                  }"
                                  x-text="paymentStatus"></span>
                        </div>

                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500 font-medium">Status Pesanan</span>
                            <span class="font-bold px-2 py-1 rounded-md text-[10px] uppercase tracking-wider"
                                  :class="{
                                      'bg-blue-50 text-blue-600': orderStatus === 'pending',
                                      'bg-indigo-50 text-indigo-600': orderStatus === 'processing',
                                      'bg-green-50 text-green-600': orderStatus === 'completed',
                                      'bg-red-50 text-red-600': ['cancelled','expired','failed'].includes(orderStatus),
                                  }"
                                  x-text="orderStatus"></span>
                        </div>

                        <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 mt-2">
                            <p class="text-[11px] leading-relaxed font-medium text-slate-600">
                                <i class="fa-solid mr-1"
                                   :class="{
                                       'fa-hourglass-half text-blue-500': orderStatus === 'pending',
                                       'fa-gears text-indigo-500': orderStatus === 'processing',
                                       'fa-circle-check text-green-500': orderStatus === 'completed',
                                       'fa-ban text-red-500': ['cancelled','expired','failed'].includes(orderStatus),
                                   }"></i>
                                <span x-text="getOrderMessage()"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Accordion -->
                <div x-data="{ summaryOpen: false }" class="bg-white rounded-[24px] shadow-lg shadow-slate-200/50 overflow-hidden slide-up border border-slate-100" style="animation-delay: 0.3s;">

                    <div @click="summaryOpen = !summaryOpen" class="p-4 cursor-pointer hover:bg-slate-50 transition relative group select-none">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-white rounded-xl p-2 shadow-sm border border-slate-100 flex items-center justify-center shrink-0">
                                @if($transaction->product && $transaction->product->icon)
                                <img src="{{ asset('storage/' . $transaction->product->icon) }}" class="w-full h-full object-contain">
                                @else
                                <i class="fa-solid fa-gamepad text-2xl text-slate-300"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-heading font-bold text-slate-800 text-sm leading-tight mb-0.5">{{ $transaction->product_name }}</h3>
                                <p class="font-extrabold text-[#0033AA] text-lg leading-tight">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                    Lihat Detail Pesanan <i class="fa-solid fa-chevron-down transition-transform duration-300" :class="summaryOpen ? 'rotate-180' : ''"></i>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-content bg-slate-50 border-t border-slate-100" :class="summaryOpen ? 'active' : ''">
                        <div class="p-5 space-y-4">
                            <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-gray-200">
                                <span class="text-slate-500 font-medium">No. Pesanan</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800 font-mono">#{{ $transaction->order_id }}</span>
                                    <button @click.stop="copyText('{{ $transaction->order_id }}')" class="text-[#0033AA] hover:text-[#002288] transition"><i class="fa-regular fa-copy"></i></button>
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-gray-200">
                                <span class="text-slate-500 font-medium">Waktu Transaksi</span>
                                <span class="font-bold text-slate-800 text-right">{{ $transaction->created_at->format('d M Y H:i') }} WIB</span>
                            </div>
                            <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-gray-200">
                                <span class="text-slate-500 font-medium">Data Pembelian</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800">{{ $transaction->order_data['customer_no'] ?? '-' }}</span>
                                    <button @click.stop="copyText('{{ $transaction->order_data['customer_no'] ?? '' }}')" class="text-[#0033AA] hover:text-[#002288] transition"><i class="fa-regular fa-copy"></i></button>
                                </div>
                            </div>
                            @if($transaction->customer_email)
                            <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-gray-200">
                                <span class="text-slate-500 font-medium">Email</span>
                                <span class="font-bold text-slate-800">{{ $transaction->customer_email }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-gray-200">
                                <span class="text-slate-500 font-medium">WhatsApp</span>
                                <span class="font-bold text-slate-800">{{ $transaction->customer_phone }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-gray-200">
                                <span class="text-slate-500 font-medium">Harga Produk</span>
                                <span class="font-bold text-slate-800">Rp {{ number_format($transaction->product_price, 0, ',', '.') }}</span>
                            </div>
                            @if($transaction->admin_fee > 0)
                            <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-gray-200">
                                <span class="text-slate-500 font-medium">Biaya Admin</span>
                                <span class="font-bold text-slate-800">Rp {{ number_format($transaction->admin_fee, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if($transaction->discount > 0)
                            <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-gray-200">
                                <span class="text-slate-500 font-medium text-green-600">Diskon</span>
                                <span class="font-bold text-green-600">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center text-sm py-2 border-t border-slate-200">
                                <span class="font-extrabold text-slate-900 uppercase">Total</span>
                                <span class="font-extrabold text-[#0033AA] text-lg">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CS Help Card -->
                <a href="{{ \App\Models\Setting::get('whatsapp_url', 'https://wa.me/6281234567890') }}" target="_blank" class="block bg-gradient-to-br from-[#002288] to-[#0033AA] rounded-[24px] p-5 text-white shadow-lg relative overflow-hidden group cursor-pointer touch-effect">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-white/10 rounded-full blur-xl -mr-8 -mt-8 group-hover:bg-white/20 transition"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white">
                            <i class="fa-solid fa-headset text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-blue-100 font-medium">Butuh Bantuan?</p>
                            <p class="text-sm font-bold">Hubungi Customer Service</p>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square ml-auto text-white/50 group-hover:text-white transition"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-[100] w-auto max-w-[90%]" x-cloak>
        <div class="bg-slate-900/95 backdrop-blur-md text-white pl-4 pr-6 py-3 rounded-full shadow-2xl flex items-center gap-3 border border-white/10 ring-2 ring-white/20">
            <div class="rounded-full w-6 h-6 flex items-center justify-center text-xs shadow-lg animate-bounce bg-emerald-500 shadow-emerald-500/30">
                <i class="fa-solid fa-check"></i>
            </div>
            <span class="font-bold text-sm tracking-wide" x-text="toastMessage">Berhasil disalin!</span>
        </div>
    </div>

    <!-- Invoice Modal -->
    <div x-show="showInvoice" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center" x-cloak>
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="showInvoice = false" x-transition.opacity></div>

        <div class="w-full h-full sm:h-auto sm:max-h-[85vh] sm:w-[800px] relative z-10 flex flex-col bg-white sm:rounded-2xl shadow-2xl overflow-hidden transition-all transform">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-white shrink-0 z-20">
                <h3 class="font-heading font-bold text-slate-800 text-lg">Preview Invoice</h3>
                <div class="hidden sm:flex items-center gap-3">
                    <button @click="downloadInvoice" class="bg-[#0033AA] hover:bg-[#002288] text-white text-xs font-bold py-2.5 px-5 rounded-full transition shadow-md hover:shadow-lg flex items-center gap-2 transform active:scale-95">
                        <i class="fa-solid fa-download"></i> Download Invoice
                    </button>
                    <button @click="showInvoice = false" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 p-2 rounded-full transition w-9 h-9 flex items-center justify-center"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
                <button @click="showInvoice = false" class="sm:hidden text-slate-500 p-2 bg-slate-100 rounded-full w-9 h-9 flex items-center justify-center"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <div class="flex-1 overflow-y-auto bg-slate-50 relative invoice-scroll pb-24 sm:pb-0">
                <div id="invoice-capture" class="bg-white shadow-sm relative text-slate-800 mx-auto w-full max-w-[800px]" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <div class="h-2 w-full bg-[#0033AA]"></div>
                    <div class="p-6 sm:p-12">
                        <!-- Invoice Header -->
                        <div class="flex flex-col sm:flex-row justify-between items-start mb-8 sm:mb-12 border-b border-gray-100 pb-8 gap-6">
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-[#0033AA] rounded text-white flex items-center justify-center font-bold text-xl">{{ substr(config('app.name'), 0, 1) }}</div>
                                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ config('app.name') }}</h1>
                                </div>
                            </div>
                            <div class="text-left sm:text-right">
                                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-200 uppercase tracking-widest leading-none mb-2">INVOICE</h2>
                                <p class="text-sm font-bold text-slate-800">No: <span class="font-mono">#{{ $transaction->order_id }}</span></p>
                                <p class="text-xs text-slate-500">{{ $transaction->created_at->format('d M Y H:i') }} WIB</p>
                                <div class="mt-4 bg-green-50 text-green-700 px-3 py-1 rounded inline-block text-xs font-bold border border-green-100 uppercase tracking-wide">
                                    LUNAS / PAID
                                </div>
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div class="mb-8">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Ditagihkan Kepada:</h3>
                            <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                                <table class="w-full text-sm">
                                    <tbody>
                                        <tr>
                                            <td class="text-slate-500 w-20 py-1 font-medium">Nama</td>
                                            <td class="text-slate-400 w-4 px-2">:</td>
                                            <td class="font-bold text-slate-800">{{ $transaction->customer_name ?: 'Guest' }}</td>
                                        </tr>
                                        @if($transaction->customer_email)
                                        <tr>
                                            <td class="text-slate-500 w-20 py-1 font-medium">Email</td>
                                            <td class="text-slate-400 w-4 px-2">:</td>
                                            <td class="font-bold text-slate-800 break-all">{{ $transaction->customer_email }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="text-slate-500 w-20 py-1 font-medium">No HP</td>
                                            <td class="text-slate-400 w-4 px-2">:</td>
                                            <td class="font-bold text-slate-800">{{ $transaction->customer_phone }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="mb-8 overflow-x-auto">
                            <table class="w-full text-sm min-w-[500px] sm:min-w-0">
                                <thead>
                                    <tr class="bg-[#0033AA] text-white text-xs uppercase tracking-wider">
                                        <th class="py-3 px-4 text-left font-bold sm:rounded-l-lg">Deskripsi Item</th>
                                        <th class="py-3 px-4 text-center font-bold w-16">Qty</th>
                                        <th class="py-3 px-4 text-right font-bold w-28">Harga</th>
                                        <th class="py-3 px-4 text-right font-bold w-28 sm:rounded-r-lg">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="text-slate-700">
                                    <tr class="border-b border-gray-100">
                                        <td class="py-4 px-4 align-top">
                                            <p class="font-bold text-slate-900">{{ $transaction->product_name }}</p>
                                            <p class="text-xs text-slate-500 mt-1">{{ $transaction->category_name }}</p>
                                            <p class="text-[10px] text-slate-400 mt-1 font-mono">{{ $transaction->order_data['customer_no'] ?? '' }}</p>
                                        </td>
                                        <td class="py-4 px-4 text-center align-top font-medium">{{ $transaction->quantity }}</td>
                                        <td class="py-4 px-4 text-right align-top">Rp {{ number_format($transaction->product_price, 0, ',', '.') }}</td>
                                        <td class="py-4 px-4 text-right align-top font-bold text-slate-900">Rp {{ number_format($transaction->product_price * $transaction->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals -->
                        <div class="flex flex-col sm:flex-row gap-8 items-start mb-12">
                            <div class="w-full sm:w-1/2 order-2 sm:order-1"></div>
                            <div class="w-full sm:w-1/2 order-1 sm:order-2">
                                <div class="space-y-3">
                                    <div class="flex justify-between text-xs text-slate-600">
                                        <span class="font-medium">Sub Total</span>
                                        <span>Rp {{ number_format($transaction->product_price * $transaction->quantity, 0, ',', '.') }}</span>
                                    </div>
                                    @if($transaction->admin_fee > 0)
                                    <div class="flex justify-between text-xs text-slate-600">
                                        <span class="font-medium">Biaya Admin</span>
                                        <span>Rp {{ number_format($transaction->admin_fee, 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    @if($transaction->discount > 0)
                                    <div class="flex justify-between text-xs text-green-600">
                                        <span class="font-bold">Diskon</span>
                                        <span class="font-bold">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    <div class="h-px bg-slate-200 w-full my-2"></div>
                                    <div class="flex justify-between items-center">
                                        <span class="font-extrabold text-slate-900 text-sm uppercase">TOTAL PEMBAYARAN</span>
                                        <span class="font-extrabold text-2xl text-[#0033AA]">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="border-t-2 border-slate-100 pt-6">
                            <p class="font-bold text-xs text-slate-800 mb-1">CATATAN PENTING:</p>
                            <p class="text-[10px] text-slate-500 leading-relaxed mb-4">
                                Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan. Mohon simpan invoice ini sebagai bukti pembelian yang sah.
                            </p>
                            <p class="text-[10px] text-slate-400">
                                Terima kasih atas kepercayaan Anda kepada {{ config('app.name') }}.
                            </p>
                        </div>
                    </div>
                    <div class="h-2 w-full bg-slate-100"></div>
                </div>
            </div>

            <!-- Mobile Download Button -->
            <div class="block sm:hidden absolute bottom-0 left-0 w-full bg-white border-t border-gray-100 p-4 shadow-[0_-5px_20px_rgba(0,0,0,0.05)] z-30">
                <button @click="downloadInvoice" class="w-full bg-[#0033AA] text-white py-3.5 rounded-xl font-bold shadow-lg shadow-[#0033AA]/20 flex items-center justify-center gap-2 active:scale-95 transition">
                    <i class="fa-solid fa-download"></i> Download Invoice (JPG)
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($transaction->payment_method === 'midtrans' && isset($snap_token))
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
document.getElementById('pay-button')?.addEventListener('click', function () {
    snap.pay('{{ $snap_token }}', {
        onSuccess: function(result){ window.location.reload(); },
        onPending: function(result){ window.location.reload(); },
        onError: function(result){ alert('Pembayaran gagal. Silakan coba lagi.'); },
        onClose: function(){}
    });
});
</script>
@endif

<script>
function paymentLogic() {
    return {
        paymentStatus: '{{ $transaction->status === "pending" ? "UNPAID" : (in_array($transaction->status, ["paid","processing","completed"]) ? "PAID" : "EXPIRED") }}',
        orderStatus: '{{ $transaction->status }}',
        isLoading: false,
        timeLeft: {{ $transaction->payment_expired_at ? max(0, $transaction->payment_expired_at->diffInSeconds(now(), false) * -1) : 0 }},
        showInvoice: false,
        showToast: false,
        toastMessage: '',
        timerInterval: null,

        initPayment() {
            if (this.paymentStatus === 'UNPAID' && this.timeLeft > 0) {
                this.startTimer();
            } else if (this.paymentStatus === 'UNPAID' && this.timeLeft <= 0) {
                this.paymentStatus = 'EXPIRED';
                this.orderStatus = 'expired';
            }

            // Auto-check status every 15 seconds for UNPAID
            if (this.paymentStatus === 'UNPAID') {
                setInterval(() => this.checkPaymentStatus(true), 15000);
            }

            // Trigger confetti if already paid
            if (this.paymentStatus === 'PAID') {
                setTimeout(() => this.triggerConfetti(), 500);
            }
        },

        startTimer() {
            this.timerInterval = setInterval(() => {
                if (this.timeLeft > 0 && this.paymentStatus === 'UNPAID') {
                    this.timeLeft--;
                } else if (this.timeLeft <= 0 && this.paymentStatus === 'UNPAID') {
                    this.paymentStatus = 'EXPIRED';
                    this.orderStatus = 'expired';
                    clearInterval(this.timerInterval);
                }
            }, 1000);
        },

        formatTime(seconds) {
            if (seconds <= 0) return '0:00';
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            if (h > 0) return `${h}:${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
            return `${m}:${s < 10 ? '0' : ''}${s}`;
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        },

        getMethodLogo() {
            const method = '{{ $transaction->payment_method }}';
            const logos = {
                'qris': 'https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg',
                'balance': 'https://cdn-icons-png.flaticon.com/512/2382/2382461.png',
                'bca': 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg',
                'bri': 'https://upload.wikimedia.org/wikipedia/commons/6/68/BANK_BRI_logo.svg',
                'bni': 'https://upload.wikimedia.org/wikipedia/commons/5/59/Logo_BNI.svg',
                'mandiri': 'https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg',
            };
            return logos[method] || 'https://cdn-icons-png.flaticon.com/512/2830/2830284.png';
        },

        getOrderMessage() {
            const messages = {
                'pending': 'Pesananmu sedang menunggu pembayaran.',
                'paid': 'Pembayaran diterima, pesanan sedang diproses.',
                'processing': 'Pesananmu sedang dalam proses.',
                'completed': 'Pesananmu telah selesai diproses.',
                'cancelled': 'Pesananmu telah dibatalkan.',
                'expired': 'Waktu pembayaranmu telah habis.',
                'failed': 'Pesanan gagal diproses.',
            };
            return messages[this.orderStatus] || 'Menunggu update status...';
        },

        async checkPaymentStatus(silent = false) {
            if (!silent) this.isLoading = true;
            try {
                const response = await fetch('{{ route("order.refresh-status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_id: '{{ $transaction->order_id }}' })
                });
                const data = await response.json();
                if (data.status) {
                    this.orderStatus = data.status;
                    if (['paid', 'processing', 'completed'].includes(data.status)) {
                        this.paymentStatus = 'PAID';
                        clearInterval(this.timerInterval);
                        if (!silent) this.triggerConfetti();
                    } else if (['expired', 'cancelled', 'failed'].includes(data.status)) {
                        this.paymentStatus = 'EXPIRED';
                        clearInterval(this.timerInterval);
                    }
                }
            } catch (e) {
                if (!silent) console.error('Status check failed:', e);
            } finally {
                this.isLoading = false;
            }
        },

        downloadInvoice() {
            const el = document.getElementById('invoice-capture');
            html2canvas(el, { scale: 2, useCORS: true, backgroundColor: '#ffffff', windowWidth: 1000 }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Invoice-{{ $transaction->order_id }}.jpg';
                link.href = canvas.toDataURL('image/jpeg', 0.9);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                this.showToastMsg('Invoice berhasil didownload!');
            }).catch(err => {
                console.error(err);
                this.showToastMsg('Gagal download invoice');
            });
        },

        copyText(text) {
            navigator.clipboard.writeText(text);
            this.showToastMsg('Berhasil disalin!');
        },

        showToastMsg(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => this.showToast = false, 2500);
        },

        triggerConfetti() {
            if (typeof confetti === 'undefined') return;
            var duration = 3000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };
            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();
                if (timeLeft <= 0) return clearInterval(interval);
                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: Math.random() * 0.3 + 0.1, y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: Math.random() * 0.3 + 0.6, y: Math.random() - 0.2 } }));
            }, 250);
        }
    }
}
</script>
@endpush
