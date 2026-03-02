@extends('layouts.admin')

@section('title', 'Kelola Produk')
@section('page-title', 'Produk Management')

@push('styles')
<style>
    .table-container { max-height: 65vh; overflow-y: auto; }
    .sticky-th { position: sticky; top: 0; z-index: 20; }
    .header-solid { background: #0033AA; color: white; }

    .stock-input {
        width: 70px; text-align: center; border-radius: 6px; border: 1px solid #e2e8f0;
        padding: 4px; font-weight: 700; color: #334155; transition: all 0.2s;
    }
    .stock-input:focus { outline: none; border-color: #0033AA; box-shadow: 0 0 0 3px rgba(0,51,170,0.1); }
    .stock-input-updated { animation: flashGreen 1s ease; border-color: #22c55e; color: #15803d; }
    @keyframes flashGreen { 0% { background-color: #dcfce7; } 100% { background-color: white; } }

    .sort-icon { opacity: 0.4; transition: opacity 0.2s; }
    th:hover .sort-icon { opacity: 1; }
    .sort-active .sort-icon { opacity: 1; }

    /* Detail Modal */
    .detail-overlay { position: fixed; inset: 0; z-index: 65; display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .detail-backdrop { position: absolute; inset: 0; background: rgba(15,23,42,0.4); backdrop-filter: blur(4px); }

    /* Delete Modal */
    .delete-overlay { position: fixed; inset: 0; z-index: 70; display: flex; align-items: center; justify-content: center; padding: 1rem; }

    @keyframes popup { 0% { opacity: 0; transform: scale(0.95); } 100% { opacity: 1; transform: scale(1); } }
    .animate-popup { animation: popup 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col 2xl:flex-row justify-between items-start 2xl:items-center gap-4 animate-slide-up-fade">
        <div class="flex flex-wrap gap-3 w-full 2xl:w-auto">
            <a href="{{ route('admin.products.create') }}" class="bg-gradient-to-r from-brand-600 to-blue-500 hover:from-brand-700 hover:to-blue-600 text-white px-5 py-3 rounded-xl font-bold text-sm shadow-lg shadow-brand-500/30 transition-all hover:-translate-y-1 active:scale-95 flex items-center gap-2">
                <i class="ri-add-circle-line text-lg"></i> Tambah
            </a>

            <form action="{{ route('admin.products.sync') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-white border border-slate-200 text-slate-700 hover:text-brand-600 hover:border-brand-200 px-5 py-3 rounded-xl font-bold text-sm shadow-sm transition-all hover:-translate-y-1 active:scale-95 flex items-center gap-2" onclick="this.querySelector('i').classList.add('animate-spin')">
                    <i class="ri-refresh-line text-lg"></i> Sync API
                </button>
            </form>
        </div>

        <div class="flex flex-col md:flex-row gap-3 w-full 2xl:w-auto flex-wrap">
            <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full 2xl:w-auto flex-wrap" id="filter-form">
                {{-- Preserve sort params --}}
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="dir" value="{{ request('dir', 'desc') }}">
                @endif

                <div class="relative w-full md:w-32">
                    <select name="per_page" onchange="document.getElementById('filter-form').submit()" class="w-full appearance-none bg-white border border-slate-200 text-sm font-bold text-slate-600 rounded-xl pl-4 pr-8 py-3 focus:ring-2 focus:ring-brand-500 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 Data</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Data</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Data</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Data</option>
                    </select>
                    <i class="ri-list-settings-line absolute right-3 top-3.5 text-slate-400 pointer-events-none"></i>
                </div>

                <div class="relative group w-full md:w-56">
                    <i class="ri-search-line absolute left-3 top-3 text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                    <input name="search" value="{{ request('search') }}" type="text" placeholder="Cari produk..." class="w-full bg-white border border-slate-200 text-sm font-semibold text-slate-700 rounded-xl pl-10 pr-4 py-3 focus:ring-2 focus:ring-brand-500 shadow-sm transition-all">
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-40">
                        <select name="category_id" onchange="document.getElementById('filter-form').submit()" class="w-full appearance-none bg-white border border-slate-200 text-sm font-bold text-slate-600 rounded-xl pl-4 pr-10 py-3 focus:ring-2 focus:ring-brand-500 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors">
                            <option value="">All Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <i class="ri-filter-3-line absolute right-3 top-3.5 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                <div class="relative w-full md:w-36">
                    <select name="status" onchange="document.getElementById('filter-form').submit()" class="w-full appearance-none bg-white border border-slate-200 text-sm font-bold text-slate-600 rounded-xl pl-4 pr-10 py-3 focus:ring-2 focus:ring-brand-500 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <i class="ri-toggle-line absolute right-3 top-3.5 text-slate-400 pointer-events-none"></i>
                </div>

                <div class="relative w-full md:w-40">
                    <select name="time" onchange="document.getElementById('filter-form').submit()" class="w-full appearance-none bg-white border border-slate-200 text-sm font-bold text-slate-600 rounded-xl pl-4 pr-10 py-3 focus:ring-2 focus:ring-brand-500 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors">
                        <option value="all" {{ ($timeFilter ?? 'all') == 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ ($timeFilter ?? '') == 'today' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ ($timeFilter ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ ($timeFilter ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                    <i class="ri-calendar-line absolute right-3 top-3.5 text-slate-400 pointer-events-none"></i>
                </div>

                <button type="submit" class="px-4 py-3 bg-brand-600 text-white rounded-xl font-bold text-sm shadow-sm hover:bg-brand-700 transition-all">
                    <i class="ri-search-line"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="glass-panel rounded-[2rem] p-1 overflow-hidden animate-slide-up-fade delay-100 shadow-xl shadow-slate-200/50">
        <div class="table-container custom-scrollbar rounded-[1.8rem]">
            <table class="w-full text-left border-collapse">
                <thead>
                    @php
                        $currentSort = $sortBy ?? 'created_at';
                        $currentDir = $sortDir ?? 'desc';

                        // Build sort URL helper
                        $sortUrl = function($field) use ($currentSort, $currentDir) {
                            $newDir = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
                            return request()->fullUrlWithQuery(['sort' => $field, 'dir' => $newDir]);
                        };

                        $sortIcon = function($field) use ($currentSort, $currentDir) {
                            if ($currentSort !== $field) return 'ri-arrow-up-down-line';
                            return $currentDir === 'asc' ? 'ri-sort-asc' : 'ri-sort-desc';
                        };
                    @endphp
                    <tr class="text-xs uppercase tracking-wider">
                        <th class="px-6 py-5 font-extrabold w-20 text-center sticky-th header-solid">Foto</th>
                        <th class="px-6 py-5 font-extrabold sticky-th header-solid {{ $currentSort === 'name' ? 'sort-active' : '' }}">
                            <a href="{{ $sortUrl('name') }}" class="flex items-center gap-1 hover:text-blue-200 transition-colors">
                                Produk <i class="{{ $sortIcon('name') }} sort-icon ml-1"></i>
                            </a>
                        </th>
                        <th class="px-6 py-5 font-extrabold text-right sticky-th header-solid {{ $currentSort === 'price_visitor' ? 'sort-active' : '' }}">
                            <a href="{{ $sortUrl('price_visitor') }}" class="flex items-center gap-1 justify-end hover:text-blue-200 transition-colors">
                                Harga <i class="{{ $sortIcon('price_visitor') }} sort-icon ml-1"></i>
                            </a>
                        </th>
                        <th class="px-6 py-5 font-extrabold text-center sticky-th header-solid">Varian</th>
                        <th class="px-6 py-5 font-extrabold text-center sticky-th header-solid {{ $currentSort === 'transactions_count' ? 'sort-active' : '' }}">
                            <a href="{{ $sortUrl('transactions_count') }}" class="flex items-center gap-1 justify-center hover:text-blue-200 transition-colors">
                                Terjual <i class="{{ $sortIcon('transactions_count') }} sort-icon ml-1"></i>
                            </a>
                        </th>
                        <th class="px-6 py-5 font-extrabold text-right sticky-th header-solid {{ $currentSort === 'transactions_sum_total_amount' ? 'sort-active' : '' }}">
                            <a href="{{ $sortUrl('transactions_sum_total_amount') }}" class="flex items-center gap-1 justify-end hover:text-blue-200 transition-colors">
                                Total Income <i class="{{ $sortIcon('transactions_sum_total_amount') }} sort-icon ml-1"></i>
                            </a>
                        </th>
                        <th class="px-6 py-5 font-extrabold text-center sticky-th header-solid {{ $currentSort === 'status' ? 'sort-active' : '' }}">
                            <a href="{{ $sortUrl('status') }}" class="flex items-center gap-1 justify-center hover:text-blue-200 transition-colors">
                                Status <i class="{{ $sortIcon('status') }} sort-icon ml-1"></i>
                            </a>
                        </th>
                        <th class="px-6 py-5 font-extrabold text-center sticky-th header-solid">Kategori</th>
                        <th class="px-6 py-5 font-extrabold text-right sticky-th header-solid">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-sm font-medium text-slate-600 divide-y divide-slate-50 bg-white">
                    @forelse($products as $product)
                    @php
                        $iconUrl = null;
                        if ($product->icon) {
                            $iconUrl = str_starts_with($product->icon, 'http') ? $product->icon : asset('storage/' . $product->icon);
                        }
                        $variantCount = $product->variants->count();
                        $priceRange = $product->getPriceRange();
                        $totalSold = $product->transactions_sum_quantity ?? $product->transactions_count ?? 0;
                        $totalIncome = $product->transactions_sum_total_amount ?? 0;
                    @endphp
                    <tr class="hover:bg-blue-50/40 transition-colors group">
                        <td class="px-6 py-4 text-center">
                            <div class="relative inline-block group-hover:scale-105 transition-transform">
                                @if($iconUrl)
                                    <img src="{{ $iconUrl }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-xl object-cover shadow-sm border border-slate-100">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                        {{ strtoupper(substr($product->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 mb-1">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="font-bold text-slate-800 text-[15px] group-hover:text-brand-600 transition-colors cursor-pointer hover:underline decoration-dashed underline-offset-4" title="Klik untuk Edit">
                                    {{ $product->name }}
                                </a>
                                <button onclick="copyText('{{ addslashes($product->name) }}')" class="text-slate-300 hover:text-brand-600 transition-colors opacity-0 group-hover:opacity-100 p-1" title="Salin Nama">
                                    <i class="ri-file-copy-line text-sm"></i>
                                </button>
                                @if($product->is_best_seller)
                                    <span class="bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded text-[9px] font-bold">BEST</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 select-all">{{ $product->provider_code ?: $product->slug }}</span>
                                <button onclick="copyText('{{ $product->provider_code ?: $product->slug }}')" class="text-slate-300 hover:text-brand-600 transition-colors" title="Salin SKU">
                                    <i class="ri-file-copy-line text-sm"></i>
                                </button>
                                <span class="font-mono text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">{{ $product->provider }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($priceRange['min'] == $priceRange['max'])
                                <span class="font-black text-slate-700 text-sm block">Rp {{ number_format($priceRange['min'], 0, ',', '.') }}</span>
                            @else
                                <span class="font-black text-slate-700 text-sm block">Rp {{ number_format($priceRange['min'], 0, ',', '.') }}</span>
                                <span class="text-[10px] text-slate-400">- {{ number_format($priceRange['max'], 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($variantCount > 0)
                                <span class="bg-purple-100 text-purple-600 px-2.5 py-1 rounded-lg text-xs font-bold">
                                    {{ $variantCount }} varian
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-1 text-slate-500 font-bold bg-slate-50 px-2 py-1 rounded-lg">
                                <i class="ri-shopping-cart-2-fill text-xs"></i> {{ number_format($totalSold) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-black text-brand-600 text-sm block">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.products.toggle-status', $product->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $product->status === 'active' ? 'bg-emerald-500' : 'bg-slate-200' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm {{ $product->status === 'active' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $categoryColors = [
                                    'Game Topup' => 'bg-orange-500 text-white',
                                    'Source Code' => 'bg-blue-600 text-white',
                                    'SMM Panel' => 'bg-purple-600 text-white',
                                    'Services' => 'bg-emerald-500 text-white',
                                    'E-Course' => 'bg-pink-500 text-white',
                                    'Premium Acc' => 'bg-red-500 text-white',
                                ];
                                $categoryClass = $categoryColors[$product->category->name ?? ''] ?? 'bg-slate-500 text-white';
                            @endphp
                            <span class="{{ $categoryClass }} inline-block px-3 py-1.5 rounded-[5px] text-[11px] font-extrabold shadow-sm tracking-wide">
                                {{ $product->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="showDetail({{ $product->id }})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition shadow-sm flex items-center justify-center" title="Details">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition shadow-sm flex items-center justify-center" title="Edit">
                                    <i class="ri-pencil-fill"></i>
                                </a>
                                <button onclick="confirmDelete({{ $product->id }}, '{{ addslashes($product->name) }}')" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition shadow-sm flex items-center justify-center" title="Delete">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ri-box-3-line text-slate-400 text-4xl"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 mb-2">Belum ada produk</h3>
                            <p class="text-slate-500 text-sm mb-6">Silakan tambah produk secara manual atau sync dari API.</p>
                            <a href="{{ route('admin.products.create') }}" class="px-6 py-3 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all hover:-translate-y-0.5 active:scale-95 inline-flex items-center">
                                <i class="ri-add-line mr-2"></i> Tambah Produk
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="p-4 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/50">
            <div class="text-xs font-bold text-slate-500">
                Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} data
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $products->url(1) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-brand-600 hover:text-white {{ $products->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }} transition shadow-sm">
                    <i class="ri-skip-back-line"></i>
                </a>
                <a href="{{ $products->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-brand-600 hover:text-white {{ $products->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }} transition shadow-sm">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
                <div class="flex items-center gap-2 mx-2">
                    <span class="text-xs font-bold text-slate-500">Page</span>
                    <span class="w-12 h-8 flex items-center justify-center text-xs font-bold border border-slate-200 rounded-lg bg-white">{{ $products->currentPage() }}</span>
                    <span class="text-xs font-bold text-slate-500">of {{ $products->lastPage() }}</span>
                </div>
                <a href="{{ $products->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-brand-600 hover:text-white {{ !$products->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }} transition shadow-sm">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
                <a href="{{ $products->url($products->lastPage()) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-brand-600 hover:text-white {{ !$products->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }} transition shadow-sm">
                    <i class="ri-skip-forward-line"></i>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Detail Modal --}}
@foreach($products as $product)
<div id="detail-modal-{{ $product->id }}" class="detail-overlay hidden">
    <div class="detail-backdrop" onclick="closeDetail({{ $product->id }})"></div>
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm relative overflow-hidden animate-popup">
        <div class="h-32 bg-gradient-to-br from-brand-500 to-indigo-600 relative">
            <button onclick="closeDetail({{ $product->id }})" class="absolute top-4 right-4 bg-black/20 text-white p-1 rounded-full hover:bg-black/30 transition">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>
        <div class="px-6 pb-6 -mt-12 relative z-10 text-center">
            @php
                $detailIcon = $product->icon
                    ? (str_starts_with($product->icon, 'http') ? $product->icon : asset('storage/' . $product->icon))
                    : null;
            @endphp
            @if($detailIcon)
                <img src="{{ $detailIcon }}" alt="{{ $product->name }}" class="w-24 h-24 rounded-2xl mx-auto shadow-lg border-4 border-white mb-4 bg-white object-cover">
            @else
                <div class="w-24 h-24 rounded-2xl mx-auto shadow-lg border-4 border-white mb-4 bg-gradient-to-br from-brand-500 to-indigo-600 flex items-center justify-center text-white font-bold text-3xl">
                    {{ strtoupper(substr($product->name, 0, 2)) }}
                </div>
            @endif
            <h3 class="font-bold text-xl text-slate-800">{{ $product->name }}</h3>
            <span class="text-xs font-mono text-slate-400 block mb-4">{{ $product->provider_code ?: $product->slug }}</span>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-bold">Harga</p>
                    @php $pr = $product->getPriceRange(); @endphp
                    <p class="font-bold text-slate-800">Rp {{ number_format($pr['min'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-bold">Varian</p>
                    <p class="font-bold text-slate-800">{{ $product->variants->count() }} Item</p>
                </div>
            </div>
            <div class="bg-blue-50 p-3 rounded-xl border border-blue-100 text-left mb-4">
                <p class="text-[10px] text-blue-400 uppercase font-bold mb-1">Total Penghasilan</p>
                <p class="font-black text-xl text-blue-600">Rp {{ number_format($product->transactions_sum_total_amount ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.products.show', $product->id) }}" class="flex-1 py-2.5 rounded-xl font-bold text-sm text-brand-600 bg-brand-50 hover:bg-brand-100 transition text-center">
                    <i class="ri-eye-line mr-1"></i> Detail
                </a>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="flex-1 py-2.5 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition text-center">
                    <i class="ri-pencil-line mr-1"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Toast Notification --}}
<div id="toastNotif" class="fixed inset-0 z-[100] flex items-center justify-center pointer-events-none hidden">
    <div class="bg-slate-900/90 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 border border-white/10 pointer-events-auto min-w-[280px]">
        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-xl bg-green-500" id="toastIcon"><i class="ri-checkbox-circle-fill"></i></div>
        <div>
            <h4 class="font-bold text-base" id="toastTitle">Berhasil!</h4>
            <p class="text-xs text-slate-300" id="toastMsg"></p>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="delete-modal" class="delete-overlay hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-filter backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm relative overflow-hidden animate-popup p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-500">
            <i class="ri-delete-bin-2-fill text-3xl"></i>
        </div>
        <h3 class="font-bold text-xl text-slate-800 mb-2">Hapus Produk?</h3>
        <p class="text-sm text-slate-500 mb-6">Produk <strong id="delete-product-name"></strong> akan dihapus secara permanen.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
            <form id="delete-form" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2.5 rounded-xl font-bold text-white bg-red-500 hover:bg-red-600 shadow-lg shadow-red-500/30 transition">Hapus</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Detail Modal
    function showDetail(id) {
        document.getElementById('detail-modal-' + id).classList.remove('hidden');
    }
    function closeDetail(id) {
        document.getElementById('detail-modal-' + id).classList.add('hidden');
    }

    // Delete Modal
    function confirmDelete(id, name) {
        document.getElementById('delete-product-name').textContent = name;
        document.getElementById('delete-form').action = '{{ url("admin/products") }}/' + id;
        document.getElementById('delete-modal').classList.remove('hidden');
    }
    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }

    // Copy text with toast
    function copyText(text) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Disalin!', text);
        });
    }

    function showToast(title, message, type) {
        const toast = document.getElementById('toastNotif');
        const icon = document.getElementById('toastIcon');
        document.getElementById('toastTitle').textContent = title;
        document.getElementById('toastMsg').textContent = message;
        icon.className = 'w-10 h-10 rounded-full flex items-center justify-center text-white text-xl ' + (type === 'error' ? 'bg-red-500' : 'bg-green-500');
        icon.innerHTML = type === 'error' ? '<i class="ri-error-warning-fill"></i>' : '<i class="ri-checkbox-circle-fill"></i>';
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 2500);
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.detail-overlay:not(.hidden)').forEach(el => el.classList.add('hidden'));
            closeDeleteModal();
        }
    });
</script>
@endpush
@endsection
