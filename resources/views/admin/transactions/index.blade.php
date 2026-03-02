@extends('layouts.admin')

@section('title', 'Kelola Transaksi')
@section('page-title', 'Transaksi')

@push('styles')
<style>
    .table-header { background: linear-gradient(to right, #0033AA, #0044CC); color: white; }
    .table-row-hover:hover { background-color: #f8fafc; }
    .status-btn { cursor: pointer; position: relative; }
    .status-btn select { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .ios-input { background: #F1F5F9; border: 1px solid transparent; transition: all 0.3s; }
    .ios-input:focus { background: white; border-color: #0033AA; box-shadow: 0 0 0 3px rgba(0, 51, 170, 0.1); }
</style>
@endpush

@section('content')
<div id="trx-app">
    {{-- Header Actions --}}
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <div>
            <p class="text-xs text-slate-500 font-medium">Kelola semua pesanan masuk (FIFO System)</p>
        </div>
        <div class="flex gap-2">
            @if($stats['paid'] > 0)
            <form action="{{ route('admin.transactions.process-all') }}" method="POST" onsubmit="return confirm('Proses semua {{ $stats['paid'] }} transaksi?')">
                @csrf
                <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg shadow-cyan-500/30 flex items-center gap-2 transition">
                    <i class="ri-play-circle-line"></i> Proses Semua ({{ $stats['paid'] }})
                </button>
            </form>
            @endif
            <div class="flex bg-white rounded-xl shadow-sm border border-slate-200 p-1">
                <a href="{{ route('admin.transactions.export', request()->query()) }}" class="px-4 py-2 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition flex items-center gap-2">
                    <i class="ri-download-cloud-2-line"></i> Export
                </a>
                <div class="w-px bg-slate-200 my-1"></div>
                <button onclick="document.getElementById('import-file').click()" class="px-4 py-2 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition flex items-center gap-2">
                    <i class="ri-upload-cloud-2-line"></i> Import
                </button>
                <form id="import-form" action="{{ route('admin.transactions.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" id="import-file" name="file" accept=".json" onchange="document.getElementById('import-form').submit()">
                </form>
            </div>
        </div>
    </div>

    {{-- Status Filter Tabs --}}
    <div class="bg-white p-2 rounded-[1.5rem] shadow-sm border border-slate-200 flex flex-col xl:flex-row gap-2 items-start xl:items-center mb-6">
        <div class="flex overflow-x-auto gap-1 p-1 bg-slate-50/50 rounded-2xl xl:w-fit max-w-full">
            @php
                $statusTabs = [
                    ['key' => 'paid', 'label' => 'Paid', 'icon' => 'ri-check-double-line', 'count' => $stats['paid']],
                    ['key' => 'processing', 'label' => 'Proses', 'icon' => 'ri-loader-4-line', 'count' => $stats['processing']],
                    ['key' => 'completed', 'label' => 'Done', 'icon' => 'ri-checkbox-circle-line', 'count' => $stats['completed']],
                    ['key' => 'cancelled', 'label' => 'Cancel', 'icon' => 'ri-close-circle-line', 'count' => $stats['cancelled']],
                    ['key' => 'pending', 'label' => 'Unpaid', 'icon' => 'ri-time-line', 'count' => $stats['pending']],
                    ['key' => 'failed', 'label' => 'Failed', 'icon' => 'ri-error-warning-line', 'count' => $stats['failed']],
                    ['key' => 'all', 'label' => 'Semua', 'icon' => 'ri-list-check', 'count' => $stats['total']],
                    ['key' => 'complain', 'label' => 'Complain', 'icon' => 'ri-customer-service-line', 'count' => $stats['complain']],
                    ['key' => 'trash', 'label' => 'Trash', 'icon' => 'ri-delete-bin-line', 'count' => $stats['trash']],
                ];
                $activeStatus = request('status', 'all');
            @endphp
            @foreach($statusTabs as $tab)
                <a href="{{ route('admin.transactions.index', array_merge(request()->except('status', 'page'), $tab['key'] !== 'all' ? ['status' => $tab['key']] : [])) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 border border-transparent
                   {{ $activeStatus === $tab['key'] || ($activeStatus === 'all' && $tab['key'] === 'all') || (!request('status') && $tab['key'] === 'all') ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/30' : 'bg-white text-slate-500 hover:bg-white/60 hover:text-slate-700' }}">
                    <i class="{{ $tab['icon'] }}"></i> {{ $tab['label'] }}
                    @if(($activeStatus === $tab['key']) || (!request('status') && $tab['key'] === 'all'))
                        <span class="bg-white/20 text-white px-1.5 py-0.5 rounded text-[10px]">{{ $tab['count'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="flex-1 flex flex-col md:flex-row gap-2 items-center w-full">
            <form method="GET" class="relative w-full flex-1 group">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <i class="ri-search-2-line absolute left-4 top-3 text-slate-400 group-focus-within:text-brand-600 transition-colors"></i>
                <input name="search" type="text" value="{{ request('search') }}" placeholder="Cari Invoice, Produk, User, IP, Catatan..."
                       class="w-full bg-slate-50 border border-transparent pl-11 pr-4 py-2.5 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-200 transition-all">
            </form>

            <div class="flex gap-2 w-full md:w-auto items-center">
                <form method="GET" class="flex gap-2 items-center">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="time_filter" onchange="this.form.submit()" class="bg-slate-50 hover:bg-white border border-transparent hover:border-slate-200 text-slate-600 text-xs font-bold rounded-2xl px-4 py-2.5 cursor-pointer focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all h-[42px]">
                        <option value="">All Time</option>
                        <option value="today" {{ request('time_filter') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('time_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('time_filter') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ request('time_filter') == 'year' ? 'selected' : '' }}>This Year</option>
                    </select>
                </form>
                @if(request()->hasAny(['search', 'status', 'time_filter']))
                    <a href="{{ route('admin.transactions.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-500 w-[42px] h-[42px] rounded-2xl flex items-center justify-center transition-all active:scale-95 flex-shrink-0" title="Reset Filter">
                        <i class="ri-close-line"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        @if($transactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="table-header text-xs uppercase tracking-wider sticky top-0 z-20 shadow-md">
                    <tr>
                        <th class="px-4 py-5 font-extrabold w-14 text-center">Note</th>
                        <th class="px-4 py-5 font-extrabold">
                            <a href="{{ route('admin.transactions.index', array_merge(request()->query(), ['sort' => 'invoice_number', 'dir' => request('sort') == 'invoice_number' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-blue-100 transition">
                                Invoice & Waktu <i class="ri-sort-asc ml-1 opacity-50"></i>
                            </a>
                        </th>
                        <th class="px-4 py-5 font-extrabold">
                            <a href="{{ route('admin.transactions.index', array_merge(request()->query(), ['sort' => 'product_name', 'dir' => request('sort') == 'product_name' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-blue-100 transition">
                                Produk <i class="ri-sort-asc ml-1 opacity-50"></i>
                            </a>
                        </th>
                        <th class="px-4 py-5 font-extrabold">
                            <a href="{{ route('admin.transactions.index', array_merge(request()->query(), ['sort' => 'total_amount', 'dir' => request('sort') == 'total_amount' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-blue-100 transition">
                                Harga & Metode <i class="ri-sort-asc ml-1 opacity-50"></i>
                            </a>
                        </th>
                        <th class="px-4 py-5 font-extrabold">Member & IP</th>
                        <th class="px-4 py-5 font-extrabold">Status</th>
                        <th class="px-4 py-5 font-extrabold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium text-slate-600 divide-y divide-slate-50">
                    @foreach($transactions as $trx)
                    <tr class="table-row-hover group transition-colors duration-200 bg-white" id="trx-row-{{ $trx->id }}">
                        {{-- Note Column --}}
                        <td class="px-4 py-4 text-center">
                            <button onclick="openNoteModal({{ $trx->id }}, {{ json_encode($trx->admin_note) }})"
                                class="relative w-10 h-10 rounded-xl flex items-center justify-center transition-all hover:scale-110 active:scale-95 mx-auto
                                {{ $trx->admin_note ? 'bg-yellow-100 text-yellow-600' : 'bg-slate-50 text-slate-300 hover:bg-slate-100' }}">
                                <i class="ri-sticky-note-fill text-xl"></i>
                                @if($trx->admin_note)
                                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                                @endif
                            </button>
                        </td>

                        {{-- Invoice & Time --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-xs text-brand-600 bg-brand-50 px-2 py-1 rounded border border-brand-100">{{ $trx->invoice_number }}</span>
                                <button onclick="copyText('{{ $trx->invoice_number }}')" class="w-6 h-6 rounded flex items-center justify-center bg-slate-100 text-slate-400 hover:bg-brand-600 hover:text-white transition shadow-sm" title="Salin Invoice">
                                    <i class="ri-file-copy-line text-xs"></i>
                                </button>
                            </div>
                            <div class="text-[11px] text-slate-400 font-bold mt-1.5 flex items-center gap-1">
                                <i class="ri-time-line"></i> {{ $trx->created_at->format('d M Y, H:i') }} WIB
                            </div>
                        </td>

                        {{-- Product --}}
                        <td class="px-4 py-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-brand-50 to-indigo-50 flex items-center justify-center text-brand-500 shadow-sm border border-brand-100 flex-shrink-0">
                                    <i class="ri-gamepad-fill text-lg"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 line-clamp-1 max-w-[180px]">{{ $trx->product_name }}</div>
                                    <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-bold border border-slate-200 inline-block mt-0.5">{{ $trx->category_name }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Price & Method --}}
                        <td class="px-4 py-4">
                            <div class="font-extrabold text-slate-800 text-[13px] mb-1.5">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</div>
                            <div class="flex items-center gap-1.5">
                                @php
                                    $methodIcons = [
                                        'QRIS' => 'ri-qr-code-line',
                                        'qris' => 'ri-qr-code-line',
                                        'BCA VA' => 'ri-bank-line',
                                        'bca_va' => 'ri-bank-line',
                                        'BRI VA' => 'ri-bank-line',
                                        'bri_va' => 'ri-bank-line',
                                        'BNI VA' => 'ri-bank-line',
                                        'bni_va' => 'ri-bank-line',
                                        'Mandiri VA' => 'ri-bank-line',
                                        'mandiri_va' => 'ri-bank-line',
                                        'GoPay' => 'ri-wallet-3-line',
                                        'gopay' => 'ri-wallet-3-line',
                                        'DANA' => 'ri-wallet-3-line',
                                        'dana' => 'ri-wallet-3-line',
                                        'OVO' => 'ri-wallet-3-line',
                                        'ovo' => 'ri-wallet-3-line',
                                        'ShopeePay' => 'ri-shopping-bag-line',
                                        'shopeepay' => 'ri-shopping-bag-line',
                                    ];
                                    $methodIcon = $methodIcons[$trx->payment_method] ?? 'ri-money-dollar-circle-line';
                                @endphp
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold border border-slate-200">
                                    <i class="{{ $methodIcon }}"></i> {{ $trx->payment_method ?? '-' }}
                                </span>
                            </div>
                        </td>

                        {{-- Member & IP --}}
                        <td class="px-4 py-4">
                            <div class="mb-1.5 flex items-center gap-1 group/member">
                                <span class="font-bold text-slate-800 text-xs">{{ $trx->customer_name }}</span>
                                <button onclick="copyText('{{ $trx->customer_name }}')" class="opacity-0 group-hover/member:opacity-100 text-slate-300 hover:text-brand-600 transition ml-1">
                                    <i class="ri-file-copy-line text-xs"></i>
                                </button>
                            </div>
                            @if($trx->ip_address)
                            <div class="flex items-center gap-1.5 group/ip w-fit">
                                <span class="text-[10px] font-mono font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">{{ $trx->ip_address }}</span>
                                <button onclick="copyText('{{ $trx->ip_address }}')" class="opacity-0 group-hover/ip:opacity-100 text-slate-300 hover:text-brand-600 transition">
                                    <i class="ri-file-copy-line text-xs"></i>
                                </button>
                            </div>
                            @endif
                        </td>

                        {{-- Status Dropdown --}}
                        <td class="px-4 py-4">
                            @php
                                $statusColors = [
                                    'paid' => 'bg-gradient-to-r from-blue-500 to-blue-600 text-white',
                                    'processing' => 'bg-gradient-to-r from-orange-400 to-orange-500 text-white',
                                    'completed' => 'bg-gradient-to-r from-emerald-500 to-green-600 text-white',
                                    'cancelled' => 'bg-gradient-to-r from-red-400 to-red-500 text-white',
                                    'pending' => 'bg-gradient-to-r from-slate-400 to-slate-500 text-white',
                                    'failed' => 'bg-gradient-to-r from-rose-500 to-red-600 text-white',
                                    'complain' => 'bg-gradient-to-r from-slate-800 to-slate-900 text-white',
                                ];
                                $statusLabels = [
                                    'paid' => 'Paid',
                                    'processing' => 'Proses',
                                    'completed' => 'Done',
                                    'cancelled' => 'Cancel',
                                    'pending' => 'Unpaid',
                                    'failed' => 'Failed',
                                    'complain' => 'Complain',
                                ];
                            @endphp
                            <div class="relative w-[110px] status-btn">
                                <div class="{{ $statusColors[$trx->status] ?? 'bg-slate-400 text-white' }} flex items-center justify-between px-3 py-1.5 rounded-full shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 border border-white/20">
                                    <span class="text-[10px] font-bold uppercase tracking-wider" id="status-label-{{ $trx->id }}">{{ $statusLabels[$trx->status] ?? ucfirst($trx->status) }}</span>
                                    <i class="ri-arrow-down-s-fill text-xs opacity-80"></i>
                                </div>
                                <select onchange="updateStatus({{ $trx->id }}, this.value)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <option value="paid" {{ $trx->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="processing" {{ $trx->status == 'processing' ? 'selected' : '' }}>Proses</option>
                                    <option value="completed" {{ $trx->status == 'completed' ? 'selected' : '' }}>Done</option>
                                    <option value="cancelled" {{ $trx->status == 'cancelled' ? 'selected' : '' }}>Cancel</option>
                                    <option value="pending" {{ $trx->status == 'pending' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="failed" {{ $trx->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="complain" {{ $trx->status == 'complain' ? 'selected' : '' }}>Complain</option>
                                </select>
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($activeStatus === 'trash')
                                    <form action="{{ route('admin.transactions.restore', $trx->id) }}" method="POST" class="inline" onsubmit="return confirm('Pulihkan transaksi ini?')">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition shadow-sm flex items-center justify-center" title="Pulihkan">
                                            <i class="ri-arrow-go-back-line"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.transactions.force-delete', $trx->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus permanen? Data tidak bisa dikembalikan!')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition shadow-sm flex items-center justify-center" title="Hapus Permanen">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </form>
                                @else
                                    <button onclick="openSendModal({{ $trx->id }}, {{ json_encode(['name' => $trx->customer_name, 'email' => $trx->customer_email, 'phone' => $trx->customer_phone]) }})"
                                        class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition shadow-sm flex items-center justify-center" title="Kirim Pesanan Manual">
                                        <i class="ri-send-plane-fill"></i>
                                    </button>
                                    <a href="{{ route('admin.transactions.show', $trx) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-brand-600 hover:bg-brand-600 hover:text-white transition shadow-sm flex items-center justify-center" title="Detail">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <form action="{{ route('admin.transactions.destroy', $trx) }}" method="POST" class="inline" onsubmit="return confirm('Pindahkan ke trash?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition shadow-sm flex items-center justify-center" title="Trash">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="bg-white border-t border-slate-100 p-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4 flex-wrap">
                <p class="text-xs font-bold text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                    Total Data: <span class="text-brand-600">{{ $transactions->total() }}</span>,
                    Tampilkan: <span class="text-brand-600">{{ $transactions->count() }}</span>
                </p>
                <form method="GET" class="flex items-center gap-2">
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request('time_filter')) <input type="hidden" name="time_filter" value="{{ request('time_filter') }}"> @endif
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Per Page</span>
                    <select name="per_page" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-lg px-2 py-1.5 focus:outline-none cursor-pointer">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>

            <div class="flex items-center gap-2 flex-wrap justify-center">
                <a href="{{ $transactions->url(1) }}" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 {{ $transactions->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}"><i class="ri-skip-back-line"></i></a>
                <a href="{{ $transactions->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 {{ $transactions->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}"><i class="ri-arrow-left-s-line"></i></a>

                <span class="text-xs font-bold text-slate-700 mx-1">Page {{ $transactions->currentPage() }} / {{ $transactions->lastPage() }}</span>

                <a href="{{ $transactions->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 {{ !$transactions->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}"><i class="ri-arrow-right-s-line"></i></a>
                <a href="{{ $transactions->url($transactions->lastPage()) }}" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 {{ !$transactions->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}"><i class="ri-skip-forward-line"></i></a>

                {{-- Go To Page --}}
                @if($transactions->lastPage() > 1)
                <div class="flex items-center gap-1 ml-2 pl-2 border-l border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Go To</span>
                    <input type="number" id="goToPage" min="1" max="{{ $transactions->lastPage() }}" value="{{ $transactions->currentPage() }}"
                        class="w-14 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-lg px-2 py-1.5 text-center focus:outline-none focus:ring-1 focus:ring-brand-500"
                        onkeydown="if(event.key==='Enter'){goToPage();}">
                    <button onclick="goToPage()" class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center hover:bg-brand-700 transition text-xs">
                        <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>

        @else
        <div class="py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-3xl"><i class="ri-inbox-line"></i></div>
            <p class="font-bold text-slate-500">Tidak ada data transaksi.</p>
            <p class="text-sm text-slate-400 mt-1">Transaksi akan muncul di sini setelah ada pesanan masuk</p>
        </div>
        @endif
    </div>
</div>

{{-- Note Modal --}}
<div id="noteModal" class="fixed inset-0 z-[80] flex items-center justify-center p-4 bg-slate-900/30 backdrop-blur-[2px] hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 relative">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg text-slate-800">Catatan Admin</h3>
            <button onclick="document.getElementById('noteModal').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <input type="hidden" id="noteTransactionId">
        <textarea id="noteContent" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none h-48 font-medium text-slate-700" placeholder="Tulis catatan khusus untuk transaksi ini..."></textarea>
        <div class="flex justify-end gap-2 mt-4">
            <button onclick="document.getElementById('noteModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-100 transition">Batal</button>
            <button onclick="saveNote()" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition">Simpan Catatan</button>
        </div>
    </div>
</div>

{{-- Send Modal --}}
<div id="sendModal" class="fixed inset-0 z-[80] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="bg-slate-50 border-b border-slate-200 px-8 py-5">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-bold text-xl text-slate-800">Kirim Pesanan Manual</h3>
                    <p class="text-xs text-slate-500 font-medium">Ke: <span class="font-bold text-indigo-600" id="sendRecipient"></span></p>
                </div>
                <button onclick="closeSendModal()" class="w-9 h-9 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center hover:bg-red-100 hover:text-red-500 transition"><i class="ri-close-line text-lg"></i></button>
            </div>
            <div class="flex gap-2 p-1.5 bg-slate-200/50 rounded-xl" id="sendTabs">
                <button onclick="switchSendTab('akun')" class="flex-1 py-3 rounded-lg text-xs font-bold transition-all uppercase tracking-wide bg-white text-indigo-600 shadow-sm" data-tab="akun">Akun</button>
                <button onclick="switchSendTab('link')" class="flex-1 py-3 rounded-lg text-xs font-bold transition-all uppercase tracking-wide text-slate-500 hover:text-slate-700" data-tab="link">Link</button>
                <button onclick="switchSendTab('custom')" class="flex-1 py-3 rounded-lg text-xs font-bold transition-all uppercase tracking-wide text-slate-500 hover:text-slate-700" data-tab="custom">Custom</button>
            </div>
        </div>

        <div class="p-8 overflow-y-auto flex-1">
            <input type="hidden" id="sendTransactionId">

            {{-- Akun Tab --}}
            <div id="tab-akun" class="space-y-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-1.5 block">Username / Email</label>
                        <input id="send-username" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition" placeholder="Contoh: user123">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-1.5 block">Password</label>
                        <input id="send-password" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition" placeholder="******">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-1.5 block">PIN / Kode</label>
                        <input id="send-pin" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition" placeholder="1234">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-1.5 block">Profile / Info</label>
                        <input id="send-profile" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition" placeholder="Profile A">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase mb-1.5 block">Catatan</label>
                    <textarea id="send-note" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:outline-none focus:border-indigo-500 transition h-24" placeholder="Pesan tambahan untuk pembeli..."></textarea>
                </div>
            </div>

            {{-- Link Tab --}}
            <div id="tab-link" class="space-y-5 hidden">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase mb-1.5 block">Link Download</label>
                    <input id="send-link-url" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition" placeholder="https://drive.google.com/...">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase mb-1.5 block">Password RAR / ZIP</label>
                    <input id="send-rar-pass" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition" placeholder="password123">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase mb-1.5 block">Catatan</label>
                    <textarea id="send-link-note" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:outline-none focus:border-indigo-500 transition h-24" placeholder="Pesan tambahan..."></textarea>
                </div>
            </div>

            {{-- Custom Tab --}}
            <div id="tab-custom" class="space-y-5 hidden">
                <div id="custom-fields">
                    <div class="flex gap-3 items-start mb-3">
                        <div class="flex-1 space-y-2 bg-slate-50 p-3 rounded-xl border border-slate-200 border-dashed">
                            <input type="text" class="custom-title w-full bg-transparent border-b border-slate-300 rounded-none px-0 py-1 text-xs font-bold text-slate-500 uppercase focus:outline-none focus:border-indigo-500" placeholder="JUDUL KOLOM">
                            <input type="text" class="custom-value w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500" placeholder="Isi data...">
                        </div>
                    </div>
                </div>
                <button onclick="addCustomField()" class="w-full py-3 rounded-xl border-2 border-dashed border-slate-300 text-slate-500 font-bold text-xs hover:border-indigo-500 hover:text-indigo-600 hover:bg-indigo-50 transition flex items-center justify-center gap-2">
                    <i class="ri-add-circle-line text-lg"></i> Tambah Kolom
                </button>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-100 space-y-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Opsi Notifikasi Otomatis</p>
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600"><i class="ri-whatsapp-fill text-lg"></i></div>
                            <span class="text-xs font-bold text-slate-700">WhatsApp</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="notify-wa" checked class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600"><i class="ri-mail-fill text-lg"></i></div>
                            <span class="text-xs font-bold text-slate-700">Email</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="notify-email" checked class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-8 pt-0 bg-white">
            <button onclick="submitSend()" class="w-full py-4 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition flex items-center justify-center gap-3 text-sm active:scale-[0.99]">
                <i class="ri-send-plane-2-fill text-lg"></i> KONFIRMASI & KIRIM SEKARANG
            </button>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="toastNotif" class="fixed inset-0 z-[100] flex items-center justify-center pointer-events-none hidden">
    <div class="bg-slate-900/90 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 border border-white/10 pointer-events-auto min-w-[300px]">
        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-xl bg-green-500" id="toastIcon">
            <i class="ri-checkbox-circle-fill"></i>
        </div>
        <div>
            <h4 class="font-bold text-base" id="toastTitle">Berhasil!</h4>
            <p class="text-xs text-slate-300" id="toastMessage"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

// Go To Page
function goToPage() {
    const input = document.getElementById('goToPage');
    const page = parseInt(input.value);
    const maxPage = {{ $transactions->lastPage() }};
    if (page >= 1 && page <= maxPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }
}

// Toast
function showToast(title, message, type = 'success') {
    const toast = document.getElementById('toastNotif');
    const icon = document.getElementById('toastIcon');
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMessage').textContent = message;
    icon.className = 'w-10 h-10 rounded-full flex items-center justify-center text-white text-xl ' + (type === 'success' ? 'bg-green-500' : 'bg-red-500');
    icon.innerHTML = type === 'success' ? '<i class="ri-checkbox-circle-fill"></i>' : '<i class="ri-error-warning-fill"></i>';
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3000);
}

// Copy to clipboard
function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Disalin!', text);
    });
}

// Update status inline
function updateStatus(trxId, newStatus) {
    fetch(`/admin/transactions/${trxId}/status`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ status: newStatus })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Status Diubah', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Gagal', data.message, 'error');
        }
    })
    .catch(() => showToast('Error', 'Terjadi kesalahan', 'error'));
}

// Note Modal
function openNoteModal(trxId, note) {
    document.getElementById('noteTransactionId').value = trxId;
    document.getElementById('noteContent').value = note || '';
    document.getElementById('noteModal').classList.remove('hidden');
}

function saveNote() {
    const trxId = document.getElementById('noteTransactionId').value;
    const note = document.getElementById('noteContent').value;

    fetch(`/admin/transactions/${trxId}/note`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ admin_note: note })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Berhasil', data.message);
            document.getElementById('noteModal').classList.add('hidden');
            setTimeout(() => location.reload(), 1000);
        }
    });
}

// Send Modal
let currentSendTab = 'akun';
function openSendModal(trxId, customer) {
    document.getElementById('sendTransactionId').value = trxId;
    document.getElementById('sendRecipient').textContent = customer.name;
    document.getElementById('sendModal').classList.remove('hidden');
}

function closeSendModal() {
    document.getElementById('sendModal').classList.add('hidden');
}

function switchSendTab(tab) {
    currentSendTab = tab;
    document.querySelectorAll('#sendTabs button').forEach(btn => {
        btn.className = btn.dataset.tab === tab
            ? 'flex-1 py-3 rounded-lg text-xs font-bold transition-all uppercase tracking-wide bg-white text-indigo-600 shadow-sm'
            : 'flex-1 py-3 rounded-lg text-xs font-bold transition-all uppercase tracking-wide text-slate-500 hover:text-slate-700';
    });
    ['akun', 'link', 'custom'].forEach(t => {
        document.getElementById('tab-' + t).classList.toggle('hidden', t !== tab);
    });
}

function addCustomField() {
    const container = document.getElementById('custom-fields');
    const div = document.createElement('div');
    div.className = 'flex gap-3 items-start mb-3';
    div.innerHTML = `
        <div class="flex-1 space-y-2 bg-slate-50 p-3 rounded-xl border border-slate-200 border-dashed">
            <input type="text" class="custom-title w-full bg-transparent border-b border-slate-300 rounded-none px-0 py-1 text-xs font-bold text-slate-500 uppercase focus:outline-none focus:border-indigo-500" placeholder="JUDUL KOLOM">
            <input type="text" class="custom-value w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500" placeholder="Isi data...">
        </div>
        <button onclick="this.parentElement.remove()" class="mt-8 text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-lg transition"><i class="ri-delete-bin-line"></i></button>
    `;
    container.appendChild(div);
}

function submitSend() {
    const trxId = document.getElementById('sendTransactionId').value;
    let deliveryData = {};

    if (currentSendTab === 'akun') {
        deliveryData = {
            username: document.getElementById('send-username').value,
            password: document.getElementById('send-password').value,
            pin: document.getElementById('send-pin').value,
            profile: document.getElementById('send-profile').value,
        };
    } else if (currentSendTab === 'link') {
        deliveryData = {
            link_url: document.getElementById('send-link-url').value,
            rar_password: document.getElementById('send-rar-pass').value,
        };
    } else {
        const titles = document.querySelectorAll('#custom-fields .custom-title');
        const values = document.querySelectorAll('#custom-fields .custom-value');
        titles.forEach((t, i) => {
            if (t.value && values[i].value) deliveryData[t.value] = values[i].value;
        });
    }

    const note = currentSendTab === 'akun' ? document.getElementById('send-note').value : (currentSendTab === 'link' ? document.getElementById('send-link-note').value : '');

    if (!confirm('Kirim pesanan sekarang?')) return;

    fetch(`/admin/transactions/${trxId}/send-order`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({
            send_type: currentSendTab,
            delivery_data: deliveryData,
            note: note,
            notify_wa: document.getElementById('notify-wa').checked,
            notify_email: document.getElementById('notify-email').checked,
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Berhasil!', data.message);
            closeSendModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Gagal', data.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Error', 'Terjadi kesalahan', 'error'));
}
</script>
@endpush
@endsection
