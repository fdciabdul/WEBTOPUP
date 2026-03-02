@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Time Filter Tabs -->
<div class="mb-6 flex flex-wrap items-center gap-4 animate-slide-up-fade">
    <div class="hidden md:flex bg-white/50 backdrop-blur border border-slate-200 p-1 rounded-xl gap-1 shadow-sm">
        <a href="{{ route('admin.dashboard', ['period' => 'all']) }}" class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $period == 'all' ? 'bg-white shadow text-brand-600' : 'text-slate-500 hover:text-slate-700' }}">All Time</a>
        <a href="{{ route('admin.dashboard', ['period' => 'year']) }}" class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $period == 'year' ? 'bg-white shadow text-brand-600' : 'text-slate-500 hover:text-slate-700' }}">Yearly</a>
        <a href="{{ route('admin.dashboard', ['period' => 'month']) }}" class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $period == 'month' ? 'bg-white shadow text-brand-600' : 'text-slate-500 hover:text-slate-700' }}">Monthly</a>
        <a href="{{ route('admin.dashboard', ['period' => 'week']) }}" class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $period == 'week' ? 'bg-white shadow text-brand-600' : 'text-slate-500 hover:text-slate-700' }}">Weekly</a>
        <a href="{{ route('admin.dashboard', ['period' => 'today']) }}" class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $period == 'today' ? 'bg-white shadow text-brand-600' : 'text-slate-500 hover:text-slate-700' }}">Daily</a>
    </div>
    <!-- Mobile dropdown -->
    <div class="md:hidden">
        <select onchange="window.location.href=this.value" class="px-4 py-2 text-sm font-bold rounded-xl border border-slate-200 bg-white shadow-sm">
            <option value="{{ route('admin.dashboard', ['period' => 'all']) }}" {{ $period == 'all' ? 'selected' : '' }}>All Time</option>
            <option value="{{ route('admin.dashboard', ['period' => 'year']) }}" {{ $period == 'year' ? 'selected' : '' }}>Yearly</option>
            <option value="{{ route('admin.dashboard', ['period' => 'month']) }}" {{ $period == 'month' ? 'selected' : '' }}>Monthly</option>
            <option value="{{ route('admin.dashboard', ['period' => 'week']) }}" {{ $period == 'week' ? 'selected' : '' }}>Weekly</option>
            <option value="{{ route('admin.dashboard', ['period' => 'today']) }}" {{ $period == 'today' ? 'selected' : '' }}>Daily</option>
        </select>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-6">

    <div class="card-anim rounded-2xl sm:rounded-[2rem] p-4 sm:p-6 bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/30 animate-slide-up-fade relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4"><i class="ri-money-dollar-circle-fill text-7xl sm:text-9xl"></i></div>
        <div class="relative z-10">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 backdrop-blur-md rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4"><i class="ri-wallet-3-fill text-xl sm:text-2xl"></i></div>
            <p class="text-emerald-100 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">Total Pendapatan</p>
            <h3 class="text-lg sm:text-2xl font-extrabold tracking-tight truncate">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <div class="mt-2 sm:mt-3 inline-flex items-center gap-1 bg-white/20 backdrop-blur px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg text-[10px] sm:text-xs font-bold shadow-sm">
                <i class="ri-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}-line"></i> {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}%
            </div>
        </div>
    </div>

    <div class="card-anim rounded-2xl sm:rounded-[2rem] p-4 sm:p-6 bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 animate-slide-up-fade delay-100 relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4"><i class="ri-shopping-bag-3-fill text-7xl sm:text-9xl"></i></div>
        <div class="relative z-10">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 backdrop-blur-md rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4"><i class="ri-shopping-bag-3-fill text-xl sm:text-2xl"></i></div>
            <p class="text-blue-100 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">Item Terjual</p>
            <h3 class="text-lg sm:text-2xl font-extrabold tracking-tight">{{ number_format($stats['total_items_sold'] ?? 0) }}</h3>
            <div class="mt-2 sm:mt-3 inline-flex items-center gap-1 bg-white/20 backdrop-blur px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg text-[10px] sm:text-xs font-bold shadow-sm">
                <i class="ri-arrow-{{ $transactionsGrowth >= 0 ? 'up' : 'down' }}-line"></i> {{ $transactionsGrowth >= 0 ? '+' : '' }}{{ $transactionsGrowth }}%
            </div>
        </div>
    </div>

    <div class="card-anim rounded-2xl sm:rounded-[2rem] p-4 sm:p-6 bg-gradient-to-br from-violet-500 to-fuchsia-600 text-white shadow-lg shadow-purple-500/30 animate-slide-up-fade delay-200 relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4"><i class="ri-file-list-3-fill text-7xl sm:text-9xl"></i></div>
        <div class="relative z-10">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 backdrop-blur-md rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4"><i class="ri-file-list-3-fill text-xl sm:text-2xl"></i></div>
            <p class="text-violet-100 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">Total Order</p>
            <h3 class="text-lg sm:text-2xl font-extrabold tracking-tight">{{ number_format($totalTransactions) }}</h3>
            <div class="mt-2 sm:mt-3 inline-flex items-center gap-1 bg-white/20 backdrop-blur px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg text-[10px] sm:text-xs font-bold shadow-sm"><i class="ri-check-double-line"></i> Valid</div>
        </div>
    </div>

    <div class="card-anim rounded-2xl sm:rounded-[2rem] p-4 sm:p-6 bg-gradient-to-br from-orange-400 to-red-500 text-white shadow-lg shadow-orange-500/30 animate-slide-up-fade delay-300 relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4"><i class="ri-archive-fill text-7xl sm:text-9xl"></i></div>
        <div class="relative z-10">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 backdrop-blur-md rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4"><i class="ri-archive-fill text-xl sm:text-2xl"></i></div>
            <p class="text-orange-100 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">Total Produk</p>
            <h3 class="text-lg sm:text-2xl font-extrabold tracking-tight">{{ $totalProducts }}</h3>
            <div class="mt-2 sm:mt-3 inline-flex items-center gap-1 bg-white/20 backdrop-blur px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg text-[10px] sm:text-xs font-bold shadow-sm"><i class="ri-check-line"></i> {{ $activeProducts }} Aktif</div>
        </div>
    </div>

    <div class="col-span-2 md:col-span-1 card-anim rounded-2xl sm:rounded-[2rem] p-4 sm:p-6 bg-gradient-to-br from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30 animate-slide-up-fade delay-400 relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4"><i class="ri-group-fill text-7xl sm:text-9xl"></i></div>
        <div class="relative z-10">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 backdrop-blur-md rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4"><i class="ri-user-smile-line text-xl sm:text-2xl"></i></div>
            <p class="text-cyan-100 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">Total Member</p>
            <h3 class="text-lg sm:text-2xl font-extrabold tracking-tight">{{ number_format($totalMembers) }}</h3>
            <div class="mt-2 sm:mt-3 inline-flex items-center gap-1 bg-white/20 backdrop-blur px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg text-[10px] sm:text-xs font-bold shadow-sm">
                <i class="ri-user-add-line"></i> {{ $membersGrowth >= 0 ? '+' : '' }}{{ $membersGrowth }}%
            </div>
        </div>
    </div>
</div>

<!-- Top Kategori & Top Product -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-6">
    <!-- Top Kategori -->
    <div class="glass-panel rounded-[2rem] p-6 card-anim flex flex-col h-full animate-slide-up-fade delay-200">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="ri-pie-chart-2-fill text-brand-600"></i> Top Kategori</h3>
        </div>
        <div class="space-y-5 flex-1">
            @php
                $totalCategoryCount = $topCategories->sum('products_count') ?: 1;
                $categoryColors = ['bg-brand-600', 'bg-orange-500', 'bg-green-500', 'bg-purple-500', 'bg-teal-500'];
                $categoryIcons = ['ri-gamepad-line', 'ri-code-s-slash-line', 'ri-customer-service-2-line', 'ri-book-read-line', 'ri-global-line'];
            @endphp
            @forelse($topCategories as $index => $cat)
            @php
                $percent = round(($cat->products_count / $totalCategoryCount) * 100);
                $barColor = $categoryColors[$index % count($categoryColors)];
                $icon = $categoryIcons[$index % count($categoryIcons)];
            @endphp
            <div class="group">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-slate-600 flex items-center gap-2 group-hover:text-brand-600 transition-colors">
                        <i class="{{ $icon }}"></i> {{ $cat->name }}
                    </span>
                    <span class="text-xs font-bold text-slate-800">{{ $percent }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden shadow-inner">
                    <div class="h-full rounded-full transition-all duration-1000 relative {{ $barColor }}" style="width: {{ $percent }}%">
                        <div class="absolute top-0 left-0 w-full h-full bg-white/30 animate-pulse"></div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-slate-400">
                <i class="ri-pie-chart-line text-4xl mb-2"></i>
                <p class="text-sm">Belum ada data kategori</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Top Product (5 Best Seller) -->
    <div class="lg:col-span-2 glass-panel rounded-[2rem] p-6 card-anim flex flex-col animate-slide-up-fade delay-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="ri-fire-fill text-orange-500 animate-pulse"></i> Top Product (5 Best Seller)</h3>
        </div>
        <div class="overflow-x-auto rounded-xl">
            <table class="w-full text-left">
                <thead class="table-header-gradient">
                    <tr class="text-[10px] uppercase font-bold tracking-wider">
                        <th class="px-4 py-4 rounded-tl-xl">#</th>
                        <th class="px-4 py-4">Produk</th>
                        <th class="px-4 py-4 text-right">Harga</th>
                        <th class="px-4 py-4 text-right rounded-tr-xl">Terjual</th>
                    </tr>
                </thead>
                <tbody class="text-sm bg-white">
                    @php
                        $productColors = ['bg-orange-500', 'bg-brand-600', 'bg-purple-500', 'bg-red-600', 'bg-green-500'];
                    @endphp
                    @forelse($topProducts->take(5) as $index => $prod)
                    @php
                        $priceRange = $prod->getPriceRange();
                        $colorClass = $productColors[$index % count($productColors)];
                        $initials = strtoupper(substr($prod->name, 0, 2));
                        $sold = $prod->transactions_sum_quantity ?? 0;
                    @endphp
                    <tr class="hover:bg-blue-50/50 transition-colors group border-b border-slate-50 last:border-0">
                        <td class="px-4 py-4 font-black text-slate-300 group-hover:text-brand-600">#{{ $index + 1 }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                @if($prod->icon)
                                <img src="{{ str_starts_with($prod->icon, 'http') ? $prod->icon : asset('storage/' . $prod->icon) }}" alt="{{ $prod->name }}" class="w-10 h-10 rounded-xl object-cover shadow-md group-hover:scale-110 transition-transform">
                                @else
                                <div class="{{ $colorClass }} w-10 h-10 rounded-xl flex items-center justify-center text-white text-[10px] font-bold shadow-md group-hover:scale-110 transition-transform">{{ $initials }}</div>
                                @endif
                                <div>
                                    <p class="font-bold text-slate-800 text-sm line-clamp-1 group-hover:text-brand-600 transition-colors">{{ $prod->name }}</p>
                                    <p class="text-[10px] text-slate-500 font-bold bg-slate-100 w-fit px-1.5 py-0.5 rounded mt-1">{{ $prod->category->name ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-right font-bold text-slate-600 text-sm">Rp {{ number_format($priceRange['min'], 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right"><span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold shadow-sm">+{{ $sold }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                            <i class="ri-box-3-line text-4xl mb-2"></i>
                            <p class="text-sm">Belum ada produk</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Content Grid: Member Baru & Transaksi Terakhir -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-3 sm:gap-6">

    <!-- Recent Members -->
    <div class="glass-panel rounded-[2rem] p-6 card-anim flex flex-col animate-slide-up-fade delay-400">
        <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2"><i class="ri-user-add-fill text-pink-500"></i> Member Baru (Top 5)</h3>
        <div class="space-y-4 flex-1">
            @forelse($recentMembers->take(5) as $member)
            <a href="{{ route('admin.members.show', $member->id) }}" class="flex items-center gap-3 p-3 bg-white hover:bg-slate-50 border border-slate-100 hover:border-brand-200 rounded-2xl transition-all cursor-pointer group shadow-sm hover:shadow-md transform hover:-translate-y-1">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=random&bold=true" class="w-10 h-10 rounded-full border-2 border-white shadow-sm group-hover:scale-110 transition-transform">
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-slate-800 text-sm group-hover:text-brand-600 transition-colors truncate">{{ $member->name }}</h4>
                    <p class="text-[10px] text-slate-400 truncate">{{ $member->email }}</p>
                </div>
                <span class="text-[9px] font-bold bg-brand-50 text-brand-600 px-2 py-1 rounded-lg">{{ ucfirst(str_replace('_', ' ', $member->level ?? 'Member')) }}</span>
            </a>
            @empty
            <div class="text-center py-8 text-slate-400">
                <i class="ri-user-3-line text-4xl mb-2"></i>
                <p class="text-sm">Belum ada member</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="xl:col-span-2 glass-panel rounded-[2rem] p-6 card-anim flex flex-col animate-slide-up-fade delay-500">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="ri-file-history-fill text-brand-600"></i> Transaksi Terakhir</h3>

            <div class="flex flex-wrap gap-2 items-center">
                <div class="relative">
                    <select id="status-filter" onchange="filterTransactions()" class="appearance-none bg-white border border-slate-200 text-xs font-bold text-slate-600 rounded-xl pl-3 pr-8 py-2 focus:ring-2 focus:ring-brand-500 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors">
                        <option value="all">Semua Status</option>
                        <option value="paid">Paid</option>
                        <option value="processing">Proses</option>
                        <option value="pending">Unpaid</option>
                        <option value="completed">Done</option>
                        <option value="cancelled">Cancel</option>
                    </select>
                    <i class="ri-arrow-down-s-line absolute right-2 top-2.5 text-slate-400 pointer-events-none"></i>
                </div>
                <a href="{{ route('admin.transactions.index') }}" class="text-xs font-bold text-brand-600 hover:underline">Lihat Semua</a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl">
            <table class="w-full text-left" id="transactions-table">
                <thead class="table-header-gradient">
                    <tr class="text-[10px] uppercase font-bold tracking-wider">
                        <th class="px-6 py-4 rounded-tl-xl">Invoice</th>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right rounded-tr-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm bg-white">
                    @forelse($recentTransactions->take(5) as $trx)
                    <tr class="border-b border-slate-50 last:border-0 hover:bg-blue-50/40 transition-colors group trx-row" data-status="{{ $trx->status }}">
                        <td class="px-6 py-4">
                            <div class="font-mono text-xs font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded w-fit shadow-sm">{{ Str::limit($trx->order_id, 15) }}</div>
                            <div class="text-[10px] text-slate-400 mt-1 font-medium flex items-center gap-1"><i class="ri-time-line"></i> {{ $trx->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800 line-clamp-1 max-w-[150px] group-hover:text-brand-600 transition-colors">{{ Str::limit($trx->product_name, 20) }}</div>
                            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded mt-1 inline-block">{{ $trx->customer_name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 text-[13px]">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusMap = [
                                    'completed' => 'bg-blue-600 shadow-blue-600/30 text-white',
                                    'pending' => 'bg-slate-500 shadow-slate-500/30 text-white',
                                    'processing' => 'bg-yellow-500 shadow-yellow-500/30 text-white',
                                    'paid' => 'bg-emerald-500 shadow-emerald-500/30 text-white',
                                    'failed' => 'bg-rose-600 shadow-rose-600/30 text-white',
                                    'cancelled' => 'bg-rose-600 shadow-rose-600/30 text-white',
                                ];
                                $statusClass = $statusMap[$trx->status] ?? 'bg-gray-500 text-white';
                                $statusLabels = [
                                    'completed' => 'Done',
                                    'pending' => 'Unpaid',
                                    'processing' => 'Proses',
                                    'paid' => 'Paid',
                                    'failed' => 'Failed',
                                    'cancelled' => 'Cancel',
                                ];
                            @endphp
                            <span class="{{ $statusClass }} px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide inline-flex items-center gap-1.5 shadow-sm">
                                {{ $statusLabels[$trx->status] ?? ucfirst($trx->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.transactions.show', $trx->id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-brand-600 hover:bg-white hover:shadow-md transition-all bg-slate-50">
                                <i class="ri-eye-line text-lg"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <i class="ri-inbox-line text-4xl mb-2"></i>
                            <p class="text-sm">Belum ada transaksi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
    <div class="glass-panel rounded-xl sm:rounded-2xl p-3 sm:p-5 card-anim animate-slide-up-fade">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                <i class="ri-time-line text-xl sm:text-2xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xl sm:text-2xl font-extrabold text-slate-800">{{ $pendingTransactions }}</p>
                <p class="text-[10px] sm:text-xs text-slate-500 font-medium">Pending</p>
            </div>
        </div>
    </div>

    <div class="glass-panel rounded-xl sm:rounded-2xl p-3 sm:p-5 card-anim animate-slide-up-fade delay-100">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                <i class="ri-loader-4-line text-xl sm:text-2xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xl sm:text-2xl font-extrabold text-slate-800">{{ $processingTransactions }}</p>
                <p class="text-[10px] sm:text-xs text-slate-500 font-medium">Processing</p>
            </div>
        </div>
    </div>

    <div class="glass-panel rounded-xl sm:rounded-2xl p-3 sm:p-5 card-anim animate-slide-up-fade delay-200">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                <i class="ri-check-double-line text-xl sm:text-2xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xl sm:text-2xl font-extrabold text-slate-800">{{ $completedToday }}</p>
                <p class="text-[10px] sm:text-xs text-slate-500 font-medium truncate">Selesai Hari Ini</p>
            </div>
        </div>
    </div>

    <div class="glass-panel rounded-xl sm:rounded-2xl p-3 sm:p-5 card-anim animate-slide-up-fade delay-300">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                <i class="ri-close-circle-line text-xl sm:text-2xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xl sm:text-2xl font-extrabold text-slate-800">{{ $failedTransactions }}</p>
                <p class="text-[10px] sm:text-xs text-slate-500 font-medium">Gagal</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterTransactions() {
    const filter = document.getElementById('status-filter').value;
    const rows = document.querySelectorAll('.trx-row');

    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        if (filter === 'all' || status === filter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endpush
@endsection
