@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['"Plus Jakarta Sans"', '"Outfit"', 'sans-serif'] },
                colors: {
                    brand: { 50: '#E6F0FF', 100: '#CCE0FF', 500: '#0033AA', 600: '#002288', 700: '#001A66' }
                }
            }
        }
    }
</script>
<style>
    .dashboard-main { font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif; }
    .glass-panel {
        background: rgba(255,255,255,0.7);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.6);
        box-shadow: 0 8px 32px 0 rgba(31,38,135,0.05);
    }
    .card-bounce {
        transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275);
    }
    .card-bounce:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
    }
    .card-bounce:active { transform: scale(0.97); }
    .table-header-solid { background: #0033AA; color: white; }
    .animate-slide-up-fade {
        animation: slideUpFade 0.6s cubic-bezier(0.16,1,0.3,1) forwards;
    }
    @keyframes slideUpFade {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .delay-100 { animation-delay: 0.1s; opacity: 0; }
    .delay-200 { animation-delay: 0.2s; opacity: 0; }
    .delay-300 { animation-delay: 0.3s; opacity: 0; }
    .delay-400 { animation-delay: 0.4s; opacity: 0; }
    .copy-hover { cursor: pointer; transition: all 0.2s; }
    .copy-hover:hover { color: #0033AA; }
    .copy-hover:active { transform: scale(0.95); }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
@endpush

@section('content')
<div class="dashboard-main bg-[#F2F2F7] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 md:px-8 py-6 space-y-6">

        <!-- Header -->
        <div class="animate-slide-up-fade">
            <h2 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">Halo, {{ auth()->user()->name }}!</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Selamat datang di dashboard member kamu</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <!-- Saldo -->
            <div class="card-bounce rounded-[1.5rem] p-5 bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-xl shadow-emerald-500/20 animate-slide-up-fade delay-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center">
                        <i class="ri-wallet-3-fill text-2xl"></i>
                    </div>
                    <a href="{{ route('dashboard.balance') }}" class="text-[10px] font-bold bg-white/20 backdrop-blur px-3 py-1.5 rounded-full hover:bg-white/30 transition">
                        <i class="ri-add-line"></i> Top Up
                    </a>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider opacity-80">Total Saldo Aktif</p>
                <p class="text-2xl font-black mt-1">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
            </div>

            <!-- Total Produk -->
            <div class="card-bounce rounded-[1.5rem] p-5 bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-xl shadow-blue-500/20 animate-slide-up-fade delay-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center">
                        <i class="ri-shopping-bag-3-fill text-2xl"></i>
                    </div>
                    <a href="{{ route('home') }}" class="text-[10px] font-bold bg-white/20 backdrop-blur px-3 py-1.5 rounded-full hover:bg-white/30 transition">
                        Belanja <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider opacity-80">Total Produk</p>
                <p class="text-2xl font-black mt-1">{{ number_format($totalProducts) }}</p>
            </div>

            <!-- Total Transaksi -->
            <div class="card-bounce rounded-[1.5rem] p-5 bg-gradient-to-br from-violet-500 to-fuchsia-600 text-white shadow-xl shadow-violet-500/20 animate-slide-up-fade delay-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center">
                        <i class="ri-file-list-3-fill text-2xl"></i>
                    </div>
                    <a href="{{ route('dashboard.transactions') }}" class="text-[10px] font-bold bg-white/20 backdrop-blur px-3 py-1.5 rounded-full hover:bg-white/30 transition">
                        Riwayat <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider opacity-80">Total Transaksi</p>
                <p class="text-2xl font-black mt-1">{{ number_format(auth()->user()->total_transactions) }}</p>
            </div>

            <!-- Pengeluaran -->
            <div class="card-bounce rounded-[1.5rem] p-5 bg-gradient-to-br from-orange-400 to-red-500 text-white shadow-xl shadow-orange-500/20 animate-slide-up-fade delay-400">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center">
                        <i class="ri-money-dollar-circle-fill text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-bold bg-white/20 backdrop-blur px-3 py-1.5 rounded-full">
                        <i class="ri-arrow-up-line"></i> Spent
                    </span>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider opacity-80">Pengeluaran</p>
                <p class="text-2xl font-black mt-1">Rp {{ number_format(auth()->user()->total_spending, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Two-Column: Best Sellers + Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Best Sellers -->
            <div class="lg:col-span-2 glass-panel rounded-[2rem] p-6 animate-slide-up-fade delay-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-black text-lg text-slate-800 flex items-center gap-2">
                        <i class="ri-fire-fill text-orange-500"></i> Best Sellers
                    </h3>
                    <a href="{{ route('home') }}" class="text-xs font-bold text-brand-500 hover:underline">Lihat Semua</a>
                </div>

                @if($bestSellers->count() > 0)
                <div class="overflow-x-auto rounded-xl">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="table-header-solid text-[10px] uppercase tracking-wider font-extrabold">
                                <th class="px-4 py-3 rounded-tl-xl">#</th>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                                <th class="px-4 py-3 text-right rounded-tr-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm">
                            @foreach($bestSellers as $index => $product)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="w-7 h-7 rounded-lg bg-brand-100 text-brand-600 text-xs font-black flex items-center justify-center">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($product->icon_url)
                                            <img src="{{ $product->icon_url }}" alt="" class="w-10 h-10 rounded-lg object-cover shadow-sm">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                                                <i class="ri-shopping-bag-line text-slate-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm">{{ $product->name }}</p>
                                            @if($product->category)
                                                <span class="text-[10px] font-bold text-brand-500 bg-brand-50 px-2 py-0.5 rounded">{{ $product->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-800">
                                    Rp {{ number_format($product->price_visitor, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-xs font-bold text-brand-500 hover:text-brand-700 transition">
                                        Beli <i class="ri-arrow-right-s-line"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="ri-shopping-bag-line text-4xl text-slate-300 mb-2"></i>
                    <p class="text-sm text-slate-400 font-bold">Belum ada data</p>
                </div>
                @endif
            </div>

            <!-- Quick Actions / Menu Cepat -->
            <div class="glass-panel rounded-[2rem] p-6 animate-slide-up-fade delay-300">
                <h3 class="font-black text-lg text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ri-apps-2-fill text-brand-500"></i> Menu Cepat
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('home') }}" class="card-bounce bg-white rounded-2xl p-4 text-center shadow-sm hover:shadow-lg border border-slate-100 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-brand-500 to-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-brand-500/20 group-hover:scale-110 transition-transform">
                            <i class="ri-shopping-bag-3-fill text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Belanja</span>
                    </a>

                    <a href="{{ route('dashboard.transactions') }}" class="card-bounce bg-white rounded-2xl p-4 text-center shadow-sm hover:shadow-lg border border-slate-100 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-violet-500/20 group-hover:scale-110 transition-transform">
                            <i class="ri-file-list-3-fill text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Transaksi</span>
                    </a>

                    <a href="{{ route('dashboard.balance') }}" class="card-bounce bg-white rounded-2xl p-4 text-center shadow-sm hover:shadow-lg border border-slate-100 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-emerald-500/20 group-hover:scale-110 transition-transform">
                            <i class="ri-wallet-3-fill text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Saldo</span>
                    </a>

                    <a href="{{ route('track.order') }}" class="card-bounce bg-white rounded-2xl p-4 text-center shadow-sm hover:shadow-lg border border-slate-100 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-transform">
                            <i class="ri-search-2-fill text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Cek Order</span>
                    </a>

                    <a href="{{ route('dashboard.bonus-files') }}" class="card-bounce bg-white rounded-2xl p-4 text-center shadow-sm hover:shadow-lg border border-slate-100 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-pink-500/20 group-hover:scale-110 transition-transform">
                            <i class="ri-gift-2-fill text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Bonus File</span>
                    </a>

                    <a href="{{ route('dashboard.profile') }}" class="card-bounce bg-white rounded-2xl p-4 text-center shadow-sm hover:shadow-lg border border-slate-100 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-slate-600 to-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-slate-500/20 group-hover:scale-110 transition-transform">
                            <i class="ri-user-settings-fill text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Profil</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Two-Column: Recent Transactions + Info Center -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Transactions -->
            <div class="lg:col-span-2 glass-panel rounded-[2rem] p-6 animate-slide-up-fade delay-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-black text-lg text-slate-800 flex items-center gap-2">
                        <i class="ri-exchange-funds-fill text-violet-500"></i> Transaksi Terakhir
                    </h3>
                    <a href="{{ route('dashboard.transactions') }}" class="text-xs font-bold text-brand-500 hover:underline">Lihat Semua</a>
                </div>

                @if($recentTransactions->count() > 0)
                <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar">
                    @foreach($recentTransactions as $trx)
                    <a href="{{ route('dashboard.transaction-detail', $trx->order_id) }}" class="flex items-center gap-4 p-4 rounded-xl bg-white hover:bg-blue-50/50 border border-slate-100 transition-all hover:shadow-md group">
                        <!-- Status Icon -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm
                            @if($trx->status === 'completed') bg-emerald-100 text-emerald-600
                            @elseif($trx->status === 'processing' || $trx->status === 'paid') bg-blue-100 text-blue-600
                            @elseif($trx->status === 'pending') bg-amber-100 text-amber-600
                            @else bg-red-100 text-red-600
                            @endif">
                            @if($trx->status === 'completed')
                                <i class="ri-checkbox-circle-fill text-lg"></i>
                            @elseif($trx->status === 'processing' || $trx->status === 'paid')
                                <i class="ri-loader-4-fill text-lg"></i>
                            @elseif($trx->status === 'pending')
                                <i class="ri-time-fill text-lg"></i>
                            @else
                                <i class="ri-close-circle-fill text-lg"></i>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-slate-800 truncate group-hover:text-brand-600 transition-colors">{{ $trx->product_name }}</p>
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $trx->order_id }} &bull; {{ $trx->created_at->format('d M Y H:i') }}</p>
                        </div>

                        <!-- Amount & Status -->
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-sm text-slate-800">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</p>
                            @php
                                $statusClass = match($trx->status) {
                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                    'processing', 'paid' => 'bg-blue-100 text-blue-700',
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-red-100 text-red-700'
                                };
                            @endphp
                            <span class="{{ $statusClass }} text-[10px] font-extrabold uppercase px-2 py-0.5 rounded mt-1 inline-block">{{ ucfirst($trx->status) }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="ri-file-list-3-line text-3xl text-slate-300"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-400">Belum ada transaksi</p>
                    <a href="{{ route('home') }}" class="inline-block mt-3 text-xs font-bold text-brand-500 hover:underline">Mulai Belanja</a>
                </div>
                @endif
            </div>

            <!-- Info Center -->
            <div class="glass-panel rounded-[2rem] p-6 animate-slide-up-fade delay-300">
                <h3 class="font-black text-lg text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ri-notification-4-fill text-amber-500"></i> Info Center
                </h3>
                <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar">
                    <!-- Level Info -->
                    <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-100">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 flex items-center justify-center flex-shrink-0">
                                <i class="ri-shield-star-fill"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm text-slate-800">Level Kamu</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ ucfirst(str_replace('_', ' ', auth()->user()->level)) }} - Nikmati harga spesial sesuai level!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Summary -->
                    <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-100">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-500 flex items-center justify-center flex-shrink-0">
                                <i class="ri-bar-chart-grouped-fill"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm text-slate-800">Ringkasan</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $pendingTransactions }} pesanan pending &bull; {{ $completedTransactions }} selesai
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Promo Info -->
                    <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-100">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-500 flex items-center justify-center flex-shrink-0 relative">
                                <i class="ri-megaphone-fill"></i>
                                <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                            </div>
                            <div>
                                <p class="font-bold text-sm text-slate-800">Info Terbaru</p>
                                <p class="text-xs text-slate-500 mt-0.5">Upgrade level untuk mendapatkan harga yang lebih murah dan akses bonus file!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Help -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 flex items-center justify-center flex-shrink-0">
                                <i class="ri-customer-service-2-fill"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm text-slate-800">Butuh Bantuan?</p>
                                <p class="text-xs text-slate-500 mt-0.5">Hubungi admin melalui WhatsApp untuk bantuan cepat.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
