@extends('layouts.admin')

@section('title', 'Detail Produk')
@section('page-title', 'Detail Produk')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products.index') }}" class="w-10 h-10 rounded-xl bg-white/50 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-500 transition-all">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Detail Produk</h2>
                <p class="text-sm text-slate-500">{{ $product->name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.edit', $product) }}" class="px-5 py-3 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-2">
                <i class="ri-pencil-fill"></i> Edit
            </a>
            @if($product->status === 'active')
                <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span> Aktif
                </span>
            @else
                <span class="bg-red-100 text-red-700 px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span> Nonaktif
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Product Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Info Card -->
            <div class="glass-panel rounded-[2rem] overflow-hidden card-anim animate-slide-up-fade">
                <div class="p-6 border-b border-slate-100 flex items-center gap-2">
                    <i class="ri-box-3-fill text-brand-600"></i>
                    <h3 class="text-lg font-bold text-slate-800">Informasi Produk</h3>
                </div>
                <div class="p-6">
                    <div class="flex gap-5 mb-6">
                        @php
                            $iconUrl = null;
                            if ($product->icon) {
                                $iconUrl = str_starts_with($product->icon, 'http') ? $product->icon : asset('storage/' . $product->icon);
                            }
                        @endphp
                        @if($iconUrl)
                            <img src="{{ $iconUrl }}" alt="{{ $product->name }}" class="w-20 h-20 rounded-2xl object-cover shadow-md border border-slate-100">
                        @else
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-brand-500 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl shadow-md">
                                {{ strtoupper(substr($product->name, 0, 2)) }}
                            </div>
                        @endif
                        <div class="flex-1 space-y-2">
                            <h4 class="text-lg font-bold text-slate-800">{{ $product->name }}</h4>
                            <div class="flex flex-wrap gap-2">
                                <span class="font-mono text-[10px] text-slate-500 bg-slate-100 px-2 py-1 rounded border border-slate-200">{{ $product->slug }}</span>
                                <span class="font-mono text-[10px] text-slate-500 bg-slate-100 px-2 py-1 rounded border border-slate-200">{{ $product->provider_code }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Kategori</label>
                            <p class="text-sm font-bold text-slate-800 mt-1">{{ $product->category->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Provider</label>
                            <p class="text-sm font-bold text-slate-800 mt-1">{{ $product->provider }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Provider Code</label>
                            <p class="text-sm font-mono font-bold text-slate-800 mt-1">{{ $product->provider_code }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Status</label>
                            <p class="mt-1">
                                @if($product->status === 'active')
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-xs font-bold">Aktif</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-xs font-bold">Nonaktif</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($product->description)
                    <div class="mt-5 pt-5 border-t border-slate-100">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Deskripsi</label>
                        <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $product->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Variants -->
            @if($product->variants && $product->variants->count() > 0)
            <div class="glass-panel rounded-[2rem] overflow-hidden card-anim animate-slide-up-fade delay-100">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="ri-list-check text-purple-600"></i>
                        <h3 class="text-lg font-bold text-slate-800">Varian Produk</h3>
                    </div>
                    <span class="bg-purple-100 text-purple-600 px-3 py-1 rounded-lg text-xs font-bold">{{ $product->variants->count() }} varian</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Nama Varian</th>
                                <th class="px-6 py-4 text-right">Harga</th>
                                <th class="px-6 py-4 text-center">Kode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($product->variants as $variant)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800 text-sm">{{ $variant->name }}</td>
                                <td class="px-6 py-4 text-right font-bold text-emerald-600 text-sm">Rp {{ number_format($variant->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-mono text-[10px] text-slate-500 bg-slate-100 px-2 py-1 rounded border border-slate-200">{{ $variant->code ?? '-' }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Pricing Card -->
            <div class="glass-panel rounded-[2rem] p-6 card-anim animate-slide-up-fade delay-100">
                <h3 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <i class="ri-money-dollar-circle-fill text-emerald-600"></i> Harga
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                        <span class="text-xs font-bold text-slate-500">Harga Dasar</span>
                        <span class="text-sm font-black text-emerald-600">Rp {{ number_format($product->base_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-500">Visitor</span>
                        <span class="text-sm font-bold text-slate-800">Rp {{ number_format($product->visitor_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl border border-blue-100">
                        <span class="text-xs font-bold text-blue-600">Reseller</span>
                        <span class="text-sm font-bold text-slate-800">Rp {{ number_format($product->reseller_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-amber-50 rounded-xl border border-amber-100">
                        <span class="text-xs font-bold text-amber-600">Reseller VIP</span>
                        <span class="text-sm font-bold text-slate-800">Rp {{ number_format($product->reseller_vip_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-xl border border-red-100">
                        <span class="text-xs font-bold text-red-600">Reseller VVIP</span>
                        <span class="text-sm font-bold text-slate-800">Rp {{ number_format($product->reseller_vvip_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="glass-panel rounded-[2rem] p-6 card-anim animate-slide-up-fade delay-200">
                <h3 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <i class="ri-settings-3-fill text-slate-500"></i> Pengaturan
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-500">Stok</span>
                        <span class="text-sm font-bold text-slate-800">{{ $product->stock ?? 'Unlimited' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-500">Urutan</span>
                        <span class="text-sm font-bold text-slate-800">{{ $product->sort_order }}</span>
                    </div>
                    @if($product->is_best_seller)
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-500">Best Seller</span>
                        <span class="bg-orange-100 text-orange-600 px-2 py-0.5 rounded text-[10px] font-bold">BEST</span>
                    </div>
                    @endif
                    <div class="pt-3 border-t border-slate-100 space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400 font-bold">Dibuat</span>
                            <span class="text-slate-600 font-medium">{{ $product->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400 font-bold">Diupdate</span>
                            <span class="text-slate-600 font-medium">{{ $product->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="glass-panel rounded-[2rem] overflow-hidden border-2 border-red-200 card-anim animate-slide-up-fade delay-300">
                <div class="p-5 bg-red-50 border-b border-red-200">
                    <h3 class="font-bold text-red-700 flex items-center gap-2 text-sm">
                        <i class="ri-error-warning-line"></i> Danger Zone
                    </h3>
                </div>
                <div class="p-5">
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-xs shadow-lg shadow-red-500/30 transition-all hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-1.5">
                            <i class="ri-delete-bin-line"></i> Hapus Produk
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
