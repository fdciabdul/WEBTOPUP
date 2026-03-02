@extends('layouts.admin')

@section('title', 'Detail Kategori')
@section('page-title', 'Detail Kategori')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.categories.index') }}" class="w-10 h-10 rounded-xl bg-white/50 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-500 transition-all">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">{{ $category->name }}</h2>
                <p class="text-sm text-slate-500">Detail kategori dan produk</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categories.edit', $category) }}" class="px-5 py-3 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-2">
                <i class="ri-pencil-fill"></i> Edit
            </a>
            @if($category->is_active)
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
        <!-- Left Column - Category Info -->
        <div class="space-y-6">
            <!-- Category Card -->
            <div class="glass-panel rounded-[2rem] p-6 card-anim animate-slide-up-fade">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-slate-100 to-white border border-slate-200 flex items-center justify-center text-brand-600 text-3xl shadow-sm overflow-hidden">
                        @if($category->icon && str_starts_with($category->icon, 'ri-'))
                            <i class="{{ $category->icon }}"></i>
                        @elseif($category->icon)
                            <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                        @else
                            <i class="ri-layout-grid-fill"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">{{ $category->name }}</h3>
                        <span class="font-mono text-[10px] text-slate-500 bg-slate-100 px-2 py-1 rounded border border-slate-200">{{ $category->slug }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-500">Total Produk</span>
                        <span class="bg-blue-50 text-brand-700 px-3 py-1 rounded-lg text-xs font-bold border border-blue-100">{{ $category->products->count() }} Item</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-500">Status</span>
                        @if($category->is_active)
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-xs font-bold">Aktif</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-xs font-bold">Nonaktif</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-500">Urutan</span>
                        <span class="text-sm font-bold text-slate-800">{{ $category->sort_order }}</span>
                    </div>
                    @if($category->description)
                    <div class="pt-3 border-t border-slate-100">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Deskripsi</label>
                        <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $category->description }}</p>
                    </div>
                    @endif
                    <div class="pt-3 border-t border-slate-100 space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400 font-bold">Dibuat</span>
                            <span class="text-slate-600 font-medium">{{ $category->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400 font-bold">Diupdate</span>
                            <span class="text-slate-600 font-medium">{{ $category->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Products List -->
        <div class="lg:col-span-2">
            <div class="glass-panel rounded-[2rem] overflow-hidden card-anim animate-slide-up-fade delay-100">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="ri-box-3-fill text-brand-600"></i>
                        <h3 class="text-lg font-bold text-slate-800">Produk dalam Kategori</h3>
                    </div>
                    <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}" class="px-4 py-2 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold text-xs shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-1.5">
                        <i class="ri-add-line"></i> Tambah Produk
                    </a>
                </div>

                @if($category->products->count() > 0)
                <div class="divide-y divide-slate-50">
                    @foreach($category->products as $product)
                    <a href="{{ route('admin.products.show', $product) }}" class="flex items-center justify-between p-5 hover:bg-blue-50/50 transition-colors group">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            @php
                                $iconUrl = null;
                                if ($product->icon) {
                                    $iconUrl = str_starts_with($product->icon, 'http') ? $product->icon : asset('storage/' . $product->icon);
                                }
                            @endphp
                            @if($iconUrl)
                                <img src="{{ $iconUrl }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-xl object-cover shadow-sm border border-slate-100 flex-shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm flex-shrink-0">
                                    {{ strtoupper(substr($product->name, 0, 2)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800 group-hover:text-brand-600 transition-colors truncate">{{ $product->name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $product->provider }} &bull; {{ $product->provider_code }}</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-4">
                            <p class="font-bold text-emerald-600 text-sm">Rp {{ number_format($product->visitor_price, 0, ',', '.') }}</p>
                            @if($product->status === 'active')
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px] font-bold mt-1 inline-block">Aktif</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-[10px] font-bold mt-1 inline-block">Nonaktif</span>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="p-16 text-center">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-box-3-line text-slate-400 text-4xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-2">Belum ada produk</h3>
                    <p class="text-slate-500 text-sm mb-6">Tambah produk untuk kategori ini</p>
                    <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}" class="px-6 py-3 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all hover:-translate-y-0.5 active:scale-95 inline-flex items-center">
                        <i class="ri-add-line mr-2"></i> Tambah Produk
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
