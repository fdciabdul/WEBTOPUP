@extends('layouts.admin')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="space-y-6" id="detailApp">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.transactions.index') }}" class="w-10 h-10 rounded-xl bg-white/50 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-500 transition-all">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <div>
                <h2 class="text-xl font-black text-slate-900">Detail Pesanan</h2>
                <p class="text-xs font-bold text-slate-400 font-mono mt-0.5">{{ $transaction->order_id }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                <i class="ri-printer-line"></i>
            </button>
            @php
                $statusColors = [
                    'completed' => 'from-green-500 to-emerald-600',
                    'paid' => 'from-blue-500 to-cyan-600',
                    'processing' => 'from-orange-500 to-amber-600',
                    'pending' => 'from-slate-400 to-slate-500',
                    'failed' => 'from-red-500 to-rose-600',
                    'cancelled' => 'from-red-400 to-red-500',
                    'complain' => 'from-slate-800 to-slate-900',
                ];
                $statusBg = $statusColors[$transaction->status] ?? 'from-slate-400 to-slate-500';
            @endphp
            <span class="px-4 py-2 rounded-xl text-xs font-extrabold uppercase tracking-wide text-white bg-gradient-to-r {{ $statusBg }} shadow-lg inline-flex items-center gap-2" id="headerStatusBadge">
                <span class="w-2 h-2 rounded-full bg-white/60"></span>
                {{ ucfirst($transaction->status) }}
            </span>
        </div>
    </div>

    <!-- Queue Alert for Paid Transactions -->
    @if($transaction->status == 'paid')
    <div class="glass-panel rounded-2xl p-5 border-l-4 border-cyan-500 bg-cyan-50/50">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center">
                    <i class="ri-time-line text-cyan-600 text-2xl"></i>
                </div>
                <div>
                    <p class="font-bold text-slate-800">Transaksi Menunggu Diproses</p>
                    <p class="text-sm text-slate-500">Pembayaran sudah diterima, klik tombol untuk memproses top-up</p>
                </div>
            </div>
            <form action="{{ route('admin.transactions.process', $transaction) }}" method="POST" onsubmit="return confirm('Proses transaksi ini sekarang?')">
                @csrf
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-cyan-500/30 hover:shadow-cyan-500/50 transition-all hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-2">
                    <i class="ri-play-circle-line text-lg"></i> Proses Sekarang
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- LEFT COLUMN -->
        <div class="space-y-6">
            <!-- Product Info -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10"><i class="ri-shopping-bag-3-fill text-9xl text-brand-600"></i></div>
                <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2 relative z-10">
                    <i class="ri-box-3-fill text-brand-500"></i> Informasi Produk
                </h3>

                <div class="flex gap-5 relative z-10">
                    @if($transaction->product && $transaction->product->icon)
                        <img src="{{ asset('storage/' . $transaction->product->icon) }}" class="w-24 h-24 rounded-2xl object-cover shadow-md border border-slate-100" alt="{{ $transaction->product_name }}">
                    @else
                        <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-brand-100 to-indigo-100 flex items-center justify-center shadow-md border border-slate-100">
                            <i class="ri-gamepad-line text-3xl text-brand-500"></i>
                        </div>
                    @endif
                    <div class="flex-1 space-y-3">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Nama Produk</label>
                            <div class="font-bold text-slate-800 text-sm leading-snug">{{ $transaction->product_name }}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Kategori</label>
                                <div class="font-bold text-slate-700 text-xs bg-slate-100 px-2 py-1 rounded w-fit">{{ $transaction->category_name ?? '-' }}</div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Qty</label>
                                <div class="font-bold text-slate-700 text-xs">{{ $transaction->quantity }}x</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($transaction->order_data)
                <div class="mt-6 pt-4 border-t border-slate-100 relative z-10">
                    <label class="text-[10px] font-bold text-slate-400 uppercase flex items-center gap-2">
                        Data Inputan
                        <i class="ri-file-copy-line cursor-pointer hover:text-brand-600 transition" onclick="copyText('{{ addslashes(json_encode($transaction->order_data)) }}')"></i>
                    </label>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 mt-1 text-xs font-mono text-slate-600 break-all group-hover:bg-brand-50 transition-colors">
                        @foreach($transaction->order_data as $key => $val)
                            <span class="text-brand-600 font-bold">{{ $key }}:</span> {{ $val }}<br>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($transaction->delivery_data)
                <div class="mt-4 pt-4 border-t border-slate-100 relative z-10">
                    <label class="text-[10px] font-bold text-slate-400 uppercase flex items-center gap-2">
                        <i class="ri-truck-fill text-green-500"></i> Data Pengiriman
                        <i class="ri-file-copy-line cursor-pointer hover:text-brand-600 transition" onclick="copyText('{{ addslashes(json_encode($transaction->delivery_data)) }}')"></i>
                    </label>
                    <div class="bg-green-50 p-3 rounded-xl border border-green-200 mt-1 text-xs font-mono text-green-700 break-all">
                        @foreach($transaction->delivery_data as $key => $val)
                            <span class="font-bold">{{ $key }}:</span> {{ $val }}<br>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Buyer Data -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
                    <i class="ri-user-smile-fill text-orange-500"></i> Data Pembeli
                </h3>
                <div class="space-y-3">
                    <!-- Name -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100 group cursor-pointer" onclick="copyText('{{ $transaction->customer_name }}')">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-lg">
                                {{ strtoupper(substr($transaction->customer_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Nama Lengkap</p>
                                <p class="text-sm font-bold text-slate-800">{{ $transaction->customer_name }}</p>
                            </div>
                        </div>
                        <i class="ri-file-copy-line text-slate-300 group-hover:text-brand-600 transition"></i>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100 group cursor-pointer" onclick="copyText('{{ $transaction->customer_email }}')">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="ri-mail-fill"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Email Address</p>
                                <p class="text-sm font-bold text-slate-800">{{ $transaction->customer_email }}</p>
                            </div>
                        </div>
                        <i class="ri-file-copy-line text-slate-300 group-hover:text-brand-600 transition"></i>
                    </div>

                    <!-- Phone -->
                    @if($transaction->customer_phone)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100 group cursor-pointer" onclick="copyText('{{ $transaction->customer_phone }}')">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <i class="ri-whatsapp-fill"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">WhatsApp</p>
                                <p class="text-sm font-bold text-slate-800">{{ $transaction->customer_phone }}</p>
                            </div>
                        </div>
                        <i class="ri-file-copy-line text-slate-300 group-hover:text-brand-600 transition"></i>
                    </div>
                    @endif

                    <!-- IP Address -->
                    @if($transaction->ip_address)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100 group cursor-pointer" onclick="copyText('{{ $transaction->ip_address }}')">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center text-violet-600">
                                <i class="ri-global-fill"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">IP Address</p>
                                <p class="text-sm font-bold text-slate-800 font-mono">{{ $transaction->ip_address }}</p>
                            </div>
                        </div>
                        <i class="ri-file-copy-line text-slate-300 group-hover:text-brand-600 transition"></i>
                    </div>
                    @endif

                    <!-- Member Link -->
                    @if($transaction->user)
                    <div class="flex items-center justify-between p-3 bg-brand-50 rounded-2xl border border-brand-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600">
                                <i class="ri-vip-crown-fill"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Member</p>
                                <p class="text-sm font-bold text-brand-700">{{ $transaction->user->name }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.members.show', $transaction->user) }}" class="text-xs font-bold text-brand-600 bg-brand-100 px-3 py-1.5 rounded-lg hover:bg-brand-200 transition">
                            Lihat <i class="ri-arrow-right-up-line"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
                    <i class="ri-time-fill text-cyan-500"></i> Timeline
                </h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="ri-add-circle-fill text-slate-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Pesanan Dibuat</p>
                            <p class="text-xs text-slate-400">{{ $transaction->created_at->format('d M Y H:i:s') }}</p>
                        </div>
                    </div>
                    @if($transaction->paid_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="ri-bank-card-fill text-blue-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Pembayaran Diterima</p>
                            <p class="text-xs text-slate-400">{{ $transaction->paid_at->format('d M Y H:i:s') }}</p>
                        </div>
                    </div>
                    @endif
                    @if($transaction->completed_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="ri-check-double-fill text-green-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Pesanan Selesai</p>
                            <p class="text-xs text-slate-400">{{ $transaction->completed_at->format('d M Y H:i:s') }}</p>
                        </div>
                    </div>
                    @endif
                    @if($transaction->is_refunded && $transaction->refunded_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="ri-refund-fill text-amber-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Refund Diproses</p>
                            <p class="text-xs text-slate-400">{{ $transaction->refunded_at->format('d M Y H:i:s') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-6">
            <!-- Status & Payment (Editable) -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
                    <i class="ri-shield-check-fill text-green-500"></i> Status & Pembayaran
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Status Pesanan</label>
                        <select id="editStatus" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 cursor-pointer focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all">
                            @foreach(['pending','paid','processing','completed','failed','cancelled','complain'] as $st)
                                <option value="{{ $st }}" {{ $transaction->status == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Metode Pembayaran</label>
                        <select id="editPayment" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 cursor-pointer focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all">
                            @php
                                $methods = ['QRIS','BCA VA','BRI VA','BNI VA','Mandiri VA','GoPay','DANA','OVO','ShopeePay','LinkAja','Manual'];
                            @endphp
                            @foreach($methods as $m)
                                <option value="{{ $m }}" {{ $transaction->payment_method == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Payment details -->
                <div class="mt-4 pt-4 border-t border-slate-100 space-y-3">
                    @if($transaction->payment_reference)
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-400 font-bold">Payment Ref</span>
                        <span class="text-xs font-mono text-slate-600 bg-slate-50 px-2 py-1 rounded cursor-pointer hover:bg-brand-50" onclick="copyText('{{ $transaction->payment_reference }}')">
                            {{ $transaction->payment_reference }} <i class="ri-file-copy-line text-slate-300 ml-1"></i>
                        </span>
                    </div>
                    @endif
                    @if($transaction->paid_at)
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-400 font-bold">Dibayar</span>
                        <span class="text-xs font-medium text-emerald-600">{{ $transaction->paid_at->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                    @if($transaction->payment_expired_at)
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-400 font-bold">Expired</span>
                        <span class="text-xs font-medium {{ $transaction->payment_expired_at->isPast() ? 'text-red-500' : 'text-slate-600' }}">
                            {{ $transaction->payment_expired_at->format('d M Y H:i') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Financial Details (Dark Card) -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 shadow-lg text-white relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-brand-500 rounded-full blur-[60px] opacity-30"></div>
                <h3 class="font-bold text-lg mb-6 flex items-center gap-2 relative z-10">
                    <i class="ri-calculator-fill text-yellow-400"></i> Rincian Keuangan
                </h3>

                <div class="space-y-4 relative z-10">
                    <!-- Cost Price Input -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase mb-1 block">Harga Modal (Input)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 text-xs font-bold">Rp</span>
                            <input type="text" id="editCostPrice"
                                   value="{{ $transaction->cost_price ? number_format($transaction->cost_price, 0, ',', '.') : '' }}"
                                   oninput="formatCostInput(this); calculateProfit()"
                                   class="w-full bg-white/10 border border-white/10 rounded-xl pl-9 pr-3 py-2.5 text-sm font-bold text-white focus:outline-none focus:bg-white/20 transition placeholder:text-white/30"
                                   placeholder="0">
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="space-y-2 pt-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400">Harga Produk</span>
                            <span class="font-bold">Rp {{ number_format($transaction->product_price, 0, ',', '.') }}</span>
                        </div>
                        @if($transaction->admin_fee > 0)
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400">Admin Fee</span>
                            <span class="font-bold">Rp {{ number_format($transaction->admin_fee, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($transaction->discount > 0)
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400">Diskon</span>
                            <span class="font-bold text-green-400">-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-white/10">
                        <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Total Bayar</p>
                            <p class="text-sm font-bold text-white">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-green-500/20 rounded-xl p-3 border border-green-500/30" id="profitBox">
                            <p class="text-[10px] text-green-300 font-bold uppercase">Estimasi Profit</p>
                            @php
                                $profit = $transaction->cost_price ? $transaction->total_amount - $transaction->cost_price : null;
                            @endphp
                            <p class="text-lg font-black" id="profitValue">
                                @if($profit !== null)
                                    <span class="{{ $profit >= 0 ? 'text-green-400' : 'text-red-400' }}">Rp {{ number_format($profit, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-white/40">-</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="bg-yellow-50 rounded-3xl p-6 shadow-sm border border-yellow-100">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <i class="ri-sticky-note-fill text-yellow-500"></i> Catatan Admin
                    </h3>
                    @if($transaction->admin_note)
                    <button onclick="copyText(document.getElementById('editNote').value)" class="text-xs font-bold text-yellow-600 hover:text-yellow-700 bg-yellow-200/50 px-2 py-1 rounded flex items-center gap-1">
                        <i class="ri-file-copy-line"></i> Salin
                    </button>
                    @endif
                </div>
                <textarea id="editNote"
                          class="w-full bg-white border border-yellow-200 rounded-xl p-3 text-sm text-slate-700 focus:ring-2 focus:ring-yellow-400 focus:outline-none min-h-[100px] placeholder:text-slate-400 font-medium resize-none"
                          placeholder="Tulis catatan khusus untuk transaksi ini...">{{ $transaction->admin_note }}</textarea>
            </div>

            <!-- Provider Info -->
            @if($transaction->provider_order_id || $transaction->provider_status)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
                    <i class="ri-server-fill text-violet-500"></i> Provider Info
                </h3>
                <div class="space-y-3">
                    @if($transaction->provider_order_id)
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-400 font-bold">Provider Order ID</span>
                        <span class="text-xs font-mono text-slate-700 bg-slate-50 px-2 py-1 rounded cursor-pointer" onclick="copyText('{{ $transaction->provider_order_id }}')">
                            {{ $transaction->provider_order_id }} <i class="ri-file-copy-line text-slate-300 ml-1"></i>
                        </span>
                    </div>
                    @endif
                    @if($transaction->provider_status)
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-400 font-bold">Provider Status</span>
                        <span class="text-xs font-bold text-slate-700">{{ $transaction->provider_status }}</span>
                    </div>
                    @endif
                    @if($transaction->provider_response)
                    <div class="mt-3">
                        <button onclick="document.getElementById('providerData').classList.toggle('hidden')" class="text-xs font-bold text-brand-600 hover:underline flex items-center gap-1">
                            <i class="ri-code-s-slash-line"></i> Toggle Raw Data
                        </button>
                        <pre id="providerData" class="hidden mt-2 bg-slate-50 p-3 rounded-xl text-[10px] overflow-x-auto font-mono text-slate-600 border border-slate-200 max-h-48 overflow-y-auto">{{ json_encode($transaction->provider_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Actions Card -->
            @if(in_array($transaction->status, ['paid', 'processing', 'failed', 'completed']))
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
                    <i class="ri-flashlight-fill text-amber-500"></i> Aksi Cepat
                </h3>
                <div class="flex flex-wrap gap-3">
                    @if(in_array($transaction->status, ['processing', 'failed']))
                    <form action="{{ route('admin.transactions.resend-notification', $transaction) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold text-xs shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-1.5">
                            <i class="ri-mail-send-line"></i> Kirim Notifikasi
                        </button>
                    </form>
                    @endif

                    @if(!in_array($transaction->status, ['cancelled', 'completed']))
                    <form action="{{ route('admin.transactions.cancel', $transaction) }}" method="POST" onsubmit="return confirm('Batalkan transaksi ini?')">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-xs shadow-lg shadow-red-500/30 transition-all hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-1.5">
                            <i class="ri-close-circle-line"></i> Batalkan
                        </button>
                    </form>
                    @endif

                    @if($transaction->canBeRefunded())
                    <form action="{{ route('admin.transactions.refund', $transaction) }}" method="POST" onsubmit="return confirm('Refund transaksi ini?')">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-xs shadow-lg shadow-amber-500/30 transition-all hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-1.5">
                            <i class="ri-refund-line"></i> Refund
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Save Button (Sticky) -->
    <div class="sticky bottom-6 z-30">
        <div class="bg-white/80 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-slate-200 flex justify-between items-center">
            <div id="saveMessage" class="text-sm font-bold text-slate-400 flex items-center gap-2">
                <i class="ri-information-line"></i> Edit status, pembayaran, harga modal, atau catatan lalu simpan
            </div>
            <button onclick="saveChanges()" id="saveBtn" class="px-6 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-brand-600 to-indigo-600 shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all hover:-translate-y-0.5 active:scale-95 flex items-center gap-2 text-sm">
                <i class="ri-save-3-line"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="fixed top-6 right-6 z-[100] hidden">
    <div id="toastContent" class="px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-2 border"></div>
</div>

@push('scripts')
<script>
const totalAmount = {{ $transaction->total_amount }};

function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Disalin ke clipboard!', 'success');
    });
}

function formatCostInput(el) {
    let val = el.value.replace(/\D/g, '');
    if (val) {
        el.value = new Intl.NumberFormat('id-ID').format(parseInt(val));
    }
}

function calculateProfit() {
    const costEl = document.getElementById('editCostPrice');
    const profitEl = document.getElementById('profitValue');
    const costVal = parseInt(costEl.value.replace(/\D/g, '') || '0');

    if (costVal > 0) {
        const profit = totalAmount - costVal;
        const formatted = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(profit));
        if (profit >= 0) {
            profitEl.innerHTML = `<span class="text-green-400">${formatted}</span>`;
        } else {
            profitEl.innerHTML = `<span class="text-red-400">-${formatted}</span>`;
        }
    } else {
        profitEl.innerHTML = '<span class="text-white/40">-</span>';
    }
}

function saveChanges() {
    const btn = document.getElementById('saveBtn');
    const msgEl = document.getElementById('saveMessage');
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Menyimpan...';

    const costRaw = document.getElementById('editCostPrice').value.replace(/\D/g, '');

    fetch('{{ route("admin.transactions.update", $transaction) }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: document.getElementById('editStatus').value,
            payment_method: document.getElementById('editPayment').value,
            cost_price: costRaw ? parseInt(costRaw) : null,
            admin_note: document.getElementById('editNote').value
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Perubahan berhasil disimpan!', 'success');
            msgEl.innerHTML = '<i class="ri-check-line text-green-500"></i> <span class="text-green-600">Tersimpan!</span>';

            // Update header status badge
            const newStatus = document.getElementById('editStatus').value;
            const badge = document.getElementById('headerStatusBadge');
            const statusColors = {
                completed: 'from-green-500 to-emerald-600',
                paid: 'from-blue-500 to-cyan-600',
                processing: 'from-orange-500 to-amber-600',
                pending: 'from-slate-400 to-slate-500',
                failed: 'from-red-500 to-rose-600',
                cancelled: 'from-red-400 to-red-500',
                complain: 'from-slate-800 to-slate-900'
            };
            badge.className = `px-4 py-2 rounded-xl text-xs font-extrabold uppercase tracking-wide text-white bg-gradient-to-r ${statusColors[newStatus] || 'from-slate-400 to-slate-500'} shadow-lg inline-flex items-center gap-2`;
            badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-white/60"></span> ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}`;
        } else {
            showToast(data.message || 'Gagal menyimpan', 'error');
        }
    })
    .catch(err => {
        showToast('Terjadi error: ' + err.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-save-3-line"></i> Simpan Perubahan';
    });
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const content = document.getElementById('toastContent');

    if (type === 'success') {
        content.className = 'px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-2 border bg-green-50 text-green-700 border-green-200';
        content.innerHTML = '<i class="ri-check-line"></i> ' + message;
    } else {
        content.className = 'px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-2 border bg-red-50 text-red-700 border-red-200';
        content.innerHTML = '<i class="ri-error-warning-line"></i> ' + message;
    }

    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3000);
}
</script>
@endpush
@endsection
