@extends('layouts.admin')

@section('title', 'Detail Member')
@section('page-title', 'Detail Member')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.members.index') }}" class="w-10 h-10 rounded-xl bg-white/50 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-500 transition-all">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Detail Member</h2>
                <p class="text-sm text-slate-500">{{ $member->name }}</p>
            </div>
        </div>
        <a href="{{ route('admin.members.edit', $member) }}" class="px-6 py-3 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all hover:-translate-y-0.5 active:scale-95">
            <i class="ri-edit-line mr-2"></i> Edit Member
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Member Info & Balance -->
        <div class="space-y-6">
            <!-- Member Profile -->
            <div class="glass-panel rounded-[2rem] p-6 card-anim animate-slide-up-fade">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-brand-500 to-indigo-500 text-white flex items-center justify-center font-bold text-2xl shadow-lg shadow-brand-500/30">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">{{ $member->name }}</h3>
                        @php
                            $levelClass = match($member->level) {
                                'reseller_vvip' => 'bg-red-100 text-red-700',
                                'reseller_vip' => 'bg-amber-100 text-amber-700',
                                'reseller' => 'bg-blue-100 text-blue-700',
                                default => 'bg-slate-200 text-slate-600'
                            };
                        @endphp
                        <span class="{{ $levelClass }} px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide">
                            {{ ucfirst(str_replace('_', ' ', $member->level)) }}
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-3 text-sm">
                        <i class="ri-mail-line text-slate-400 w-5"></i>
                        <span class="text-slate-600">{{ $member->email }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="ri-phone-line text-slate-400 w-5"></i>
                        <span class="text-slate-600">{{ $member->phone }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="ri-calendar-line text-slate-400 w-5"></i>
                        <span class="text-slate-600">Bergabung {{ $member->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="ri-toggle-line text-slate-400 w-5"></i>
                        @if($member->is_active)
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide inline-flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                Aktif
                            </span>
                        @else
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide inline-flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Balance Card -->
            <div class="glass-panel rounded-[2rem] p-6 card-anim animate-slide-up-fade">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ri-wallet-3-line text-emerald-600"></i> Saldo
                </h3>
                <div class="text-center py-4">
                    <p class="text-3xl font-bold text-slate-800 mb-4">Rp {{ number_format($member->balance, 0, ',', '.') }}</p>
                    <div class="flex gap-3 justify-center">
                        <button type="button" onclick="showAddBalanceModal()" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5">
                            <i class="ri-add-line mr-1"></i> Tambah
                        </button>
                        <button type="button" onclick="showDeductBalanceModal()" class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-red-500/30 transition-all hover:-translate-y-0.5">
                            <i class="ri-subtract-line mr-1"></i> Kurangi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-4">
                <div class="glass-panel rounded-2xl p-5 card-anim animate-slide-up-fade">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 flex items-center justify-center text-white text-xl mb-3">
                        <i class="ri-shopping-cart-2-line"></i>
                    </div>
                    <p class="text-2xl font-bold text-slate-800">{{ $member->transactions->count() }}</p>
                    <p class="text-xs text-slate-500">Total Transaksi</p>
                </div>

                <div class="glass-panel rounded-2xl p-5 card-anim animate-slide-up-fade">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-xl mb-3">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <p class="text-lg font-bold text-slate-800">Rp {{ number_format($member->transactions->sum('total_amount'), 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-500">Total Belanja</p>
                </div>
            </div>
        </div>

        <!-- Right Column - Transactions -->
        <div class="lg:col-span-2">
            <div class="glass-panel rounded-[2rem] overflow-hidden card-anim animate-slide-up-fade">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="ri-history-line text-brand-600"></i> Transaksi Terbaru
                    </h3>
                </div>

                @if($member->transactions->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach($member->transactions as $transaction)
                    <a href="{{ route('admin.transactions.show', $transaction) }}" class="flex items-center gap-4 p-5 hover:bg-blue-50/50 transition-colors group">
                        @php
                            $statusClass = match($transaction->status) {
                                'pending' => 'bg-amber-100 text-amber-600',
                                'processing' => 'bg-blue-100 text-blue-600',
                                'completed' => 'bg-emerald-100 text-emerald-600',
                                'failed' => 'bg-red-100 text-red-600',
                                default => 'bg-slate-100 text-slate-600'
                            };
                        @endphp
                        <div class="w-12 h-12 rounded-xl {{ $statusClass }} flex items-center justify-center text-xl">
                            <i class="ri-shopping-cart-2-line"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 group-hover:text-brand-600 transition-colors truncate">{{ Str::limit($transaction->product_name, 40) }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $transaction->order_id }} &bull; {{ $transaction->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-slate-800">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                            @php
                                $badgeClass = match($transaction->status) {
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                    'failed' => 'bg-red-100 text-red-700',
                                    default => 'bg-slate-100 text-slate-700'
                                };
                            @endphp
                            <span class="{{ $badgeClass }} px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide mt-1 inline-block">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="p-16 text-center">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-shopping-cart-2-line text-slate-400 text-4xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-2">Belum ada transaksi</h3>
                    <p class="text-slate-500 text-sm">Transaksi member akan muncul di sini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Balance Modal -->
<div id="addBalanceModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="glass-panel rounded-[2rem] w-full max-w-md mx-4 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="ri-add-circle-line text-emerald-600"></i> Tambah Saldo
            </h3>
            <button type="button" onclick="closeAddBalanceModal()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form action="{{ route('admin.members.add-balance', $member) }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" min="0" step="1000"
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 rounded-xl bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all text-sm resize-none"
                              placeholder="Alasan penambahan saldo..."></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeAddBalanceModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-sm transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/30 transition-all">
                    <i class="ri-add-line mr-1"></i> Tambah Saldo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Deduct Balance Modal -->
<div id="deductBalanceModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="glass-panel rounded-[2rem] w-full max-w-md mx-4 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="ri-subtract-line text-red-600"></i> Kurangi Saldo
            </h3>
            <button type="button" onclick="closeDeductBalanceModal()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form action="{{ route('admin.members.deduct-balance', $member) }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" min="0" max="{{ $member->balance }}" step="1000"
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all text-sm" required>
                    <p class="text-xs text-slate-400 mt-2">Saldo saat ini: Rp {{ number_format($member->balance, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 rounded-xl bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all text-sm resize-none"
                              placeholder="Alasan pengurangan saldo..."></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeDeductBalanceModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-sm transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-red-500/30 transition-all">
                    <i class="ri-subtract-line mr-1"></i> Kurangi Saldo
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showAddBalanceModal() {
    document.getElementById('addBalanceModal').classList.remove('hidden');
    document.getElementById('addBalanceModal').classList.add('flex');
}

function closeAddBalanceModal() {
    document.getElementById('addBalanceModal').classList.add('hidden');
    document.getElementById('addBalanceModal').classList.remove('flex');
}

function showDeductBalanceModal() {
    document.getElementById('deductBalanceModal').classList.remove('hidden');
    document.getElementById('deductBalanceModal').classList.add('flex');
}

function closeDeductBalanceModal() {
    document.getElementById('deductBalanceModal').classList.add('hidden');
    document.getElementById('deductBalanceModal').classList.remove('flex');
}

// Close modal on outside click
window.onclick = function(event) {
    const addModal = document.getElementById('addBalanceModal');
    const deductModal = document.getElementById('deductBalanceModal');
    if (event.target == addModal) {
        closeAddBalanceModal();
    }
    if (event.target == deductModal) {
        closeDeductBalanceModal();
    }
}
</script>
@endpush
@endsection
