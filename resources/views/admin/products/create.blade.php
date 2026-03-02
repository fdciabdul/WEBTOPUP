@extends('layouts.admin')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')

@push('styles')
<style>
    .tab-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.625rem;
        transition: all 0.3s;
        border: 1px solid transparent;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        cursor: pointer;
        white-space: nowrap;
    }
    .tab-btn.active {
        background: #0f172a;
        color: white;
        border-color: #0f172a;
    }
    .tab-btn:not(.active) {
        background: white;
        color: #64748b;
        border-color: white;
    }
    .tab-btn:not(.active):hover {
        border-color: #bfdbfe;
        color: #0033AA;
    }

    .input-field {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        background: rgba(255,255,255,0.5);
        border: 1px solid #e2e8f0;
        outline: none;
        transition: all 0.3s;
        font-size: 0.875rem;
        font-weight: 500;
        color: #1e293b;
        font-family: inherit;
    }
    .input-field:focus {
        border-color: #0044CC;
        box-shadow: 0 0 0 2px rgba(0,68,204,0.2);
        background: white;
    }
    .input-field::placeholder {
        color: #94a3b8;
    }

    select.input-field {
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem;
        padding-right: 2.5rem;
    }

    .price-input {
        width: 100%;
        padding: 0.625rem 0.75rem 0.625rem 2.5rem;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: #1e293b;
        outline: none;
        transition: all 0.3s;
        font-family: inherit;
    }
    .price-input:focus {
        border-color: #0044CC;
        box-shadow: 0 0 0 2px rgba(219,234,254,1);
    }

    .variant-card {
        background: white;
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        transition: all 0.3s;
    }
    .variant-card:hover {
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        transform: translateY(-4px);
    }

    .category-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .list-enter-active, .list-leave-active { transition: all 0.4s ease; }
    .list-enter-from, .list-leave-to { opacity: 0; transform: translateX(-20px); }
</style>
@endpush

@section('content')
<div id="productApp" class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products.index') }}" class="w-10 h-10 rounded-xl bg-white/50 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-500 transition-all">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Tambah Produk</h2>
                <p class="text-sm text-slate-500">Buat produk baru dengan varian harga</p>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="glass-panel rounded-[2rem] overflow-hidden animate-slide-up-fade">

        <!-- Tabs -->
        <div class="px-8 pt-6 pb-2 bg-white/40 border-b border-white/50">
            <div class="flex gap-4 overflow-x-auto pb-2">
                <button type="button" @click="activeTab = 'info'" :class="['tab-btn', activeTab === 'info' ? 'active' : '']">
                    <i class="ri-information-line text-lg"></i> Info Produk
                </button>
                <button type="button" @click="activeTab = 'harga'" :class="['tab-btn', activeTab === 'harga' ? 'active' : '']">
                    <i class="ri-price-tag-3-line text-lg"></i> Harga & Varian
                </button>
                <button type="button" @click="activeTab = 'format'" :class="['tab-btn', activeTab === 'format' ? 'active' : '']">
                    <i class="ri-list-settings-line text-lg"></i> Data Pembeli
                </button>
            </div>
        </div>

        <!-- Form Content -->
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" @submit="prepareSubmit">
            @csrf

            <!-- Hidden fields for Vue data (values set by prepareSubmit) -->
            <input type="hidden" name="variant_mode" value="">
            <input type="hidden" name="variants_data" value="">
            <input type="hidden" name="input_fields" value="">
            <input type="hidden" name="notes" value="">

            <div class="p-6 md:p-8 bg-slate-50/30">

                <!-- Tab: Info Produk -->
                <div v-show="activeTab === 'info'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-6xl mx-auto">

                    <!-- Left Column - Image & Toggles -->
                    <div class="lg:col-span-4 flex flex-col gap-6">
                        <!-- Image Upload -->
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                            <label class="block text-sm font-bold text-slate-700 mb-3">Icon/Gambar Produk</label>
                            <div class="aspect-square bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center relative overflow-hidden cursor-pointer hover:border-brand-500 transition-colors group">
                                <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover absolute inset-0">
                                <div v-else class="text-center p-6 space-y-2">
                                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center text-brand-600 mx-auto group-hover:scale-110 transition-transform">
                                        <i class="ri-image-add-line text-3xl"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-400">Klik untuk Upload</p>
                                </div>
                                <input type="file" name="icon" accept="image/*" @change="handleFileUpload" class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>
                            <div class="mt-4">
                                <input type="text" name="icon_url" v-model="imageUrl" @input="imagePreview = imageUrl" placeholder="Atau paste URL gambar..." class="input-field text-xs">
                            </div>
                        </div>

                        <!-- Toggles -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center">
                                        <i class="ri-star-fill text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">Best Seller</p>
                                        <p class="text-[10px] text-slate-400">Produk Unggulan</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_best_seller" value="1" v-model="form.isBestSeller" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                </label>
                            </div>

                            <div class="h-px bg-slate-100 w-full"></div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center">
                                        <i class="ri-vip-crown-fill text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">Featured</p>
                                        <p class="text-[10px] text-slate-400">Tampil di Halaman Utama</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_featured" value="1" v-model="form.isFeatured" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                </label>
                            </div>

                            <div class="h-px bg-slate-100 w-full"></div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center">
                                        <i class="ri-toggle-line text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">Status Aktif</p>
                                        <p class="text-[10px] text-slate-400">Tampil di Toko</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" v-model="form.isActive" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Product Info -->
                    <div class="lg:col-span-8">
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 flex flex-col gap-6 h-full relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-50 to-transparent rounded-bl-full -z-0 opacity-50"></div>

                            <div class="space-y-2 relative z-10">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Produk <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="ri-box-3-line absolute left-4 top-1/2 -translate-y-1/2 text-xl text-slate-400"></i>
                                    <input type="text" name="name" v-model="form.name" @input="autoSlug" required placeholder="Cth: Mobile Legends" class="input-field pl-12 text-lg font-bold">
                                </div>
                            </div>

                            <div class="space-y-2 relative z-10">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider flex gap-2 items-center">
                                    Slug URL <span class="bg-blue-50 text-brand-600 px-2 py-0.5 rounded text-[10px]">AUTO</span>
                                </label>
                                <div class="relative">
                                    <i class="ri-link absolute left-4 top-1/2 -translate-y-1/2 text-xl text-slate-400"></i>
                                    <div class="flex items-center input-field pl-12">
                                        <span class="text-slate-400 text-sm font-semibold mr-1">/produk/</span>
                                        <input type="text" name="slug" v-model="form.slug" class="flex-1 bg-transparent border-none outline-none text-sm font-bold text-brand-600 p-0">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <i class="ri-folder-line absolute left-4 top-1/2 -translate-y-1/2 text-lg text-slate-400"></i>
                                        <select name="category_id" required class="input-field pl-11 appearance-none cursor-pointer">
                                            <option value="">Pilih Kategori</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Provider</label>
                                    <div class="relative">
                                        <i class="ri-server-line absolute left-4 top-1/2 -translate-y-1/2 text-lg text-slate-400"></i>
                                        <select name="provider" class="input-field pl-11 appearance-none cursor-pointer">
                                            <option value="apigames">ApiGames</option>
                                            <option value="digiflazz">Digiflazz</option>
                                            <option value="manual">Manual</option>
                                        </select>
                                        <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2 relative z-10 flex-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Deskripsi</label>
                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
                                    <div class="flex items-center gap-1 px-3 py-2 bg-slate-50 border-b border-slate-200">
                                        <button type="button" onclick="insertTag('description-editor','<b>','</b>')" class="w-8 h-8 rounded-lg hover:bg-slate-200 flex items-center justify-center text-sm font-bold text-slate-600 transition" title="Bold"><b>B</b></button>
                                        <button type="button" onclick="insertTag('description-editor','<i>','</i>')" class="w-8 h-8 rounded-lg hover:bg-slate-200 flex items-center justify-center text-sm italic text-slate-600 transition" title="Italic"><i>I</i></button>
                                        <div class="w-px h-5 bg-slate-200 mx-1"></div>
                                        <button type="button" onclick="insertTag('description-editor','<ul>\n<li>','</li>\n</ul>')" class="w-8 h-8 rounded-lg hover:bg-slate-200 flex items-center justify-center text-slate-600 transition" title="Bullet List"><i class="ri-list-unordered"></i></button>
                                        <button type="button" onclick="insertTag('description-editor','<ol>\n<li>','</li>\n</ol>')" class="w-8 h-8 rounded-lg hover:bg-slate-200 flex items-center justify-center text-slate-600 transition" title="Numbered List"><i class="ri-list-ordered"></i></button>
                                    </div>
                                    <textarea id="description-editor" name="description" v-model="form.description" rows="4" placeholder="Tulis deskripsi produk lengkap... (Mendukung HTML: bold, italic, list)" class="w-full px-4 py-3 border-none outline-none text-sm font-medium text-slate-700 resize-none bg-transparent"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Harga & Varian -->
                <div v-show="activeTab === 'harga'" class="max-w-5xl mx-auto space-y-8">

                    <!-- Mode Toggle -->
                    <div class="flex justify-center">
                        <div class="bg-white p-1.5 rounded-full shadow-lg shadow-blue-500/10 border border-slate-100 flex">
                            <button type="button" @click="variantMode = 'simple'" :class="['px-8 py-3 rounded-full text-xs font-bold transition-all flex items-center gap-2', variantMode === 'simple' ? 'bg-brand-600 text-white shadow-md' : 'text-slate-500 hover:text-slate-800']">
                                <i class="ri-price-tag-3-line"></i> Mode Simple
                            </button>
                            <button type="button" @click="variantMode = 'nested'" :class="['px-8 py-3 rounded-full text-xs font-bold transition-all flex items-center gap-2', variantMode === 'nested' ? 'bg-slate-800 text-white shadow-md' : 'text-slate-500 hover:text-slate-800']">
                                <i class="ri-folder-line"></i> Mode Kategori (Sosmed)
                            </button>
                        </div>
                    </div>

                    <!-- Simple Mode -->
                    <div v-if="variantMode === 'simple'" class="space-y-5">
                        <template v-for="(item, index) in simpleVariants" :key="index">
                            <div class="variant-card relative group">
                                <div class="flex flex-col gap-5">
                                    <div class="flex flex-col md:flex-row gap-5">
                                        <div class="flex-1 space-y-1">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase">Nama Varian <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i class="ri-barcode-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                <input type="text" v-model="item.name" placeholder="Cth: 86 Diamonds, 1 Bulan" class="input-field pl-9 text-sm font-bold">
                                            </div>
                                        </div>
                                        <div class="w-full md:w-32 space-y-1">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase">Stok</label>
                                            <input type="number" v-model="item.stock" class="w-full py-2.5 bg-blue-50 border border-blue-100 rounded-xl text-sm font-bold text-brand-600 text-center outline-none">
                                        </div>
                                        <button type="button" @click="removeSimpleVariant(index)" v-if="simpleVariants.length > 1" class="w-10 h-10 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all self-end mb-1">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase flex items-center gap-2">
                                                <i class="ri-cloud-line text-brand-600"></i> Kode Provider
                                            </label>
                                            <input type="text" v-model="item.provider_code" placeholder="Kode dari provider (opsional)" class="input-field text-xs">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase flex items-center gap-2">
                                                <i class="ri-download-cloud-line text-indigo-600"></i> Link Download (Digital)
                                            </label>
                                            <input type="text" v-model="item.download_link" placeholder="Paste link file (opsional)" class="input-field text-xs text-indigo-600 placeholder-indigo-300/70">
                                        </div>
                                    </div>

                                    <!-- Price Tiers -->
                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        <div class="flex items-center gap-2 mb-3">
                                            <i class="ri-coins-line text-yellow-500"></i>
                                            <span class="text-xs font-bold text-slate-600 uppercase">Tier Harga</span>
                                        </div>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            <div class="relative">
                                                <span class="absolute -top-2 left-3 px-1.5 bg-white text-[9px] font-bold text-slate-500 border border-slate-200 rounded-md z-10">Visitor</span>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                    <input type="text" v-model="item.prices.visitor" @input="formatPrice($event, item.prices, 'visitor')" class="price-input" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="relative">
                                                <span class="absolute -top-2 left-3 px-1.5 bg-white text-[9px] font-bold text-blue-500 border border-blue-200 rounded-md z-10">Reseller</span>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                    <input type="text" v-model="item.prices.reseller" @input="formatPrice($event, item.prices, 'reseller')" class="price-input" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="relative">
                                                <span class="absolute -top-2 left-3 px-1.5 bg-white text-[9px] font-bold text-purple-500 border border-purple-200 rounded-md z-10">VIP</span>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                    <input type="text" v-model="item.prices.vip" @input="formatPrice($event, item.prices, 'vip')" class="price-input" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="relative">
                                                <span class="absolute -top-2 left-3 px-1.5 bg-white text-[9px] font-bold text-red-500 border border-red-200 rounded-md z-10">VVIP</span>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                    <input type="text" v-model="item.prices.vvip" @input="formatPrice($event, item.prices, 'vvip')" class="price-input" placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addSimpleVariant" class="w-full py-4 border-2 border-dashed border-slate-300 rounded-2xl text-slate-400 font-bold hover:border-brand-500 hover:text-brand-600 hover:bg-blue-50 transition-all flex items-center justify-center gap-2 group">
                            <i class="ri-add-circle-line text-2xl group-hover:scale-110 transition-transform"></i> Tambah Varian Baru
                        </button>
                    </div>

                    <!-- Nested Mode (Sosmed) -->
                    <div v-else class="space-y-6">
                        <template v-for="(cat, cIdx) in nestedVariants" :key="cIdx">
                            <div class="category-card">
                                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center gap-4">
                                    <div class="w-8 h-8 bg-white rounded-lg shadow-sm flex items-center justify-center text-brand-600 font-bold text-sm">@{{ cIdx + 1 }}</div>
                                    <input type="text" v-model="cat.name" placeholder="Nama Kategori (Cth: Followers IG)" class="flex-1 bg-transparent font-bold text-lg text-slate-700 outline-none placeholder-slate-400">
                                    <button type="button" @click="removeNestedCategory(cIdx)" v-if="nestedVariants.length > 1" class="text-slate-400 hover:text-red-500">
                                        <i class="ri-delete-bin-line text-xl"></i>
                                    </button>
                                </div>
                                <div class="p-6 space-y-6">
                                    <template v-for="(item, iIdx) in cat.items" :key="iIdx">
                                        <div class="pl-4 border-l-2 border-slate-200 hover:border-brand-500 transition-colors">
                                            <div class="space-y-4">
                                                <div class="flex gap-3">
                                                    <div class="relative flex-1">
                                                        <i class="ri-box-3-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                        <input type="text" v-model="item.name" placeholder="Nama Item (Cth: 1000 Followers)" class="input-field pl-9 text-sm font-bold">
                                                    </div>
                                                    <input type="number" v-model="item.stock" placeholder="Stok" class="w-24 bg-blue-50 rounded-xl text-center text-xs font-bold text-brand-600 outline-none">
                                                    <button type="button" @click="removeNestedItem(cIdx, iIdx)" v-if="cat.items.length > 1" class="text-red-300 hover:text-red-500 w-10 flex items-center justify-center">
                                                        <i class="ri-close-circle-line text-xl"></i>
                                                    </button>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    <div class="relative">
                                                        <i class="ri-cloud-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                        <input type="text" v-model="item.provider_code" placeholder="Kode Provider" class="input-field pl-9 text-xs">
                                                    </div>
                                                    <div class="relative">
                                                        <i class="ri-download-cloud-line absolute left-3 top-1/2 -translate-y-1/2 text-indigo-400"></i>
                                                        <input type="text" v-model="item.download_link" placeholder="Link Download (opsional)" class="input-field pl-9 text-xs text-indigo-600">
                                                    </div>
                                                </div>

                                                <!-- Price Tiers -->
                                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                                    <div class="flex items-center gap-2 mb-3">
                                                        <i class="ri-coins-line text-yellow-500"></i>
                                                        <span class="text-xs font-bold text-slate-600 uppercase">Tier Harga</span>
                                                    </div>
                                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                        <div class="relative">
                                                            <span class="absolute -top-2 left-3 px-1.5 bg-white text-[9px] font-bold text-slate-500 border border-slate-200 rounded-md z-10">Visitor</span>
                                                            <div class="relative">
                                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                                <input type="text" v-model="item.prices.visitor" @input="formatPrice($event, item.prices, 'visitor')" class="price-input" placeholder="0">
                                                            </div>
                                                        </div>
                                                        <div class="relative">
                                                            <span class="absolute -top-2 left-3 px-1.5 bg-white text-[9px] font-bold text-blue-500 border border-blue-200 rounded-md z-10">Reseller</span>
                                                            <div class="relative">
                                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                                <input type="text" v-model="item.prices.reseller" @input="formatPrice($event, item.prices, 'reseller')" class="price-input" placeholder="0">
                                                            </div>
                                                        </div>
                                                        <div class="relative">
                                                            <span class="absolute -top-2 left-3 px-1.5 bg-white text-[9px] font-bold text-purple-500 border border-purple-200 rounded-md z-10">VIP</span>
                                                            <div class="relative">
                                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                                <input type="text" v-model="item.prices.vip" @input="formatPrice($event, item.prices, 'vip')" class="price-input" placeholder="0">
                                                            </div>
                                                        </div>
                                                        <div class="relative">
                                                            <span class="absolute -top-2 left-3 px-1.5 bg-white text-[9px] font-bold text-red-500 border border-red-200 rounded-md z-10">VVIP</span>
                                                            <div class="relative">
                                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                                <input type="text" v-model="item.prices.vvip" @input="formatPrice($event, item.prices, 'vvip')" class="price-input" placeholder="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <button type="button" @click="addNestedItem(cIdx)" class="text-xs font-bold text-brand-600 hover:underline flex items-center gap-1 mt-2">
                                        <i class="ri-add-line"></i> Tambah Sub Item
                                    </button>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addNestedCategory" class="w-full py-4 bg-slate-800 text-white rounded-2xl font-bold shadow-lg hover:bg-slate-900 transition-all flex items-center justify-center gap-2">
                            <i class="ri-folder-add-line text-xl"></i> Tambah Kategori
                        </button>
                    </div>
                </div>

                <!-- Tab: Data Pembeli -->
                <div v-show="activeTab === 'format'" class="max-w-4xl mx-auto space-y-8">

                    <!-- Input Fields -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2 text-lg">
                                <i class="ri-list-check-2 text-purple-500 text-2xl"></i> Data Pembelian
                            </h3>
                            <button type="button" @click="addInputField" class="px-4 py-2 bg-purple-50 text-purple-600 rounded-xl text-xs font-bold hover:bg-purple-100 transition-colors">
                                + Tambah Kolom
                            </button>
                        </div>
                        <div class="space-y-4">
                            <template v-for="(input, idx) in formatInputs" :key="idx">
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex flex-col gap-3 group hover:border-purple-200 transition-colors">
                                    <div class="flex gap-4 items-center">
                                        <div class="flex-1 relative">
                                            <i class="ri-text absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="text" v-model="input.label" placeholder="Label Input (Cth: User ID)" class="input-field pl-9 text-sm font-bold">
                                        </div>
                                        <div class="w-1/3 relative">
                                            <select v-model="input.type" class="input-field text-sm font-bold cursor-pointer">
                                                <option value="text">Teks</option>
                                                <option value="number">Angka</option>
                                                <option value="dropdown">Dropdown</option>
                                            </select>
                                        </div>
                                        <button type="button" @click="removeInputField(idx)" v-if="formatInputs.length > 1" class="w-8 h-8 rounded-lg bg-white text-slate-300 hover:text-red-500 flex items-center justify-center shadow-sm">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                    <div v-if="input.type === 'dropdown'">
                                        <input type="text" v-model="input.options" placeholder="Opsi Dropdown: Server A, Server B (Pisahkan koma)" class="w-full px-4 py-2 bg-purple-50 border border-purple-100 text-purple-700 rounded-lg text-xs font-bold outline-none focus:ring-1 focus:ring-purple-400">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2 text-lg">
                                <i class="ri-error-warning-line text-orange-500 text-2xl"></i> Catatan Produk
                            </h3>
                            <button type="button" @click="addNote" class="px-4 py-2 bg-orange-50 text-orange-600 rounded-xl text-xs font-bold hover:bg-orange-100 transition-colors">
                                + Tambah Catatan
                            </button>
                        </div>
                        <div class="space-y-3">
                            <template v-for="(note, idx) in customNotes" :key="idx">
                                <div class="flex gap-4 items-start bg-orange-50/60 p-4 rounded-xl border border-orange-100">
                                    <i class="ri-information-line text-orange-400 mt-1 text-lg"></i>
                                    <textarea v-model="note.text" rows="2" placeholder="Tulis catatan untuk pembeli..." class="flex-1 bg-transparent border-none text-sm font-medium text-slate-700 outline-none resize-none placeholder-orange-300"></textarea>
                                    <button type="button" @click="removeNote(idx)" v-if="customNotes.length > 1" class="text-orange-300 hover:text-red-500">
                                        <i class="ri-close-line text-lg"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-8 py-5 bg-white/80 backdrop-blur-md border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-slate-500 hover:bg-slate-100 font-bold text-sm transition-colors">
                    <i class="ri-arrow-left-line text-lg"></i> Kembali
                </a>
                <div class="flex gap-4">
                    <a href="{{ route('admin.products.index') }}" class="px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition-colors text-sm">Batal</a>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 text-white font-bold shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:scale-105 active:scale-95 transition-all text-sm flex items-center gap-2">
                        <i class="ri-save-line text-lg"></i> Simpan Produk
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Rich text toolbar helper
function insertTag(editorId, openTag, closeTag) {
    const el = document.getElementById(editorId);
    const start = el.selectionStart;
    const end = el.selectionEnd;
    const selected = el.value.substring(start, end);
    const replacement = openTag + (selected || 'teks') + closeTag;
    el.value = el.value.substring(0, start) + replacement + el.value.substring(end);
    el.focus();
    el.selectionStart = start + openTag.length;
    el.selectionEnd = start + openTag.length + (selected || 'teks').length;
    el.dispatchEvent(new Event('input'));
}

const { createApp } = Vue;

createApp({
    data() {
        return {
            activeTab: 'info',
            variantMode: 'simple',
            imagePreview: null,
            imageUrl: '',

            form: {
                name: '',
                slug: '',
                description: '',
                isBestSeller: false,
                isFeatured: false,
                isActive: true,
            },

            simpleVariants: [
                { name: '', stock: 9999999, provider_code: '', download_link: '', prices: { visitor: '', reseller: '', vip: '', vvip: '' } }
            ],

            nestedVariants: [
                {
                    name: '',
                    items: [
                        { name: '', stock: 9999999, provider_code: '', download_link: '', prices: { visitor: '', reseller: '', vip: '', vvip: '' } }
                    ]
                }
            ],

            formatInputs: [
                { label: 'User ID', type: 'text', options: '' }
            ],

            customNotes: [
                { text: '' }
            ]
        }
    },
    methods: {
        autoSlug() {
            this.form.slug = this.form.name
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        },

        handleFileUpload(e) {
            const file = e.target.files[0];
            if (file) {
                this.imagePreview = URL.createObjectURL(file);
            }
        },

        formatPrice(event, priceObject, key) {
            let value = event.target.value;
            let number = value.replace(/[^0-9]/g, '');

            if (number) {
                let formatted = parseInt(number).toLocaleString('id-ID');
                priceObject[key] = formatted;
            } else {
                priceObject[key] = '';
            }
        },

        // Simple Variants
        addSimpleVariant() {
            this.simpleVariants.push({
                name: '',
                stock: 9999999,
                provider_code: '',
                download_link: '',
                prices: { visitor: '', reseller: '', vip: '', vvip: '' }
            });
        },
        removeSimpleVariant(index) {
            this.simpleVariants.splice(index, 1);
        },

        // Nested Variants
        addNestedCategory() {
            this.nestedVariants.push({
                name: '',
                items: [
                    { name: '', stock: 9999999, provider_code: '', download_link: '', prices: { visitor: '', reseller: '', vip: '', vvip: '' } }
                ]
            });
        },
        removeNestedCategory(index) {
            this.nestedVariants.splice(index, 1);
        },
        addNestedItem(catIndex) {
            this.nestedVariants[catIndex].items.push({
                name: '',
                stock: 9999999,
                provider_code: '',
                download_link: '',
                prices: { visitor: '', reseller: '', vip: '', vvip: '' }
            });
        },
        removeNestedItem(catIndex, itemIndex) {
            this.nestedVariants[catIndex].items.splice(itemIndex, 1);
        },

        // Input Fields
        addInputField() {
            this.formatInputs.push({ label: '', type: 'text', options: '' });
        },
        removeInputField(index) {
            this.formatInputs.splice(index, 1);
        },

        // Notes
        addNote() {
            this.customNotes.push({ text: '' });
        },
        removeNote(index) {
            this.customNotes.splice(index, 1);
        },

        prepareSubmit(event) {
            // Validate variant names
            if (this.variantMode === 'simple') {
                const emptyVariants = this.simpleVariants.filter(v => !v.name || v.name.trim() === '');
                if (emptyVariants.length > 0) {
                    event.preventDefault();
                    alert('⚠️ Mohon isi nama varian untuk semua item!');
                    this.activeTab = 'harga';
                    return false;
                }
            } else {
                for (const cat of this.nestedVariants) {
                    if (!cat.name || cat.name.trim() === '') {
                        event.preventDefault();
                        alert('⚠️ Mohon isi nama kategori untuk semua kategori!');
                        this.activeTab = 'harga';
                        return false;
                    }
                    const emptyItems = cat.items.filter(item => !item.name || item.name.trim() === '');
                    if (emptyItems.length > 0) {
                        event.preventDefault();
                        alert('⚠️ Mohon isi nama item untuk semua varian!');
                        this.activeTab = 'harga';
                        return false;
                    }
                }
            }

            // Clean up price formatting before submit
            const cleanPrice = (val) => {
                if (!val) return 0;
                return parseInt(val.toString().replace(/[^0-9]/g, '')) || 0;
            };

            // Deep clone to avoid modifying reactive data
            let variantsToSubmit;
            if (this.variantMode === 'simple') {
                variantsToSubmit = JSON.parse(JSON.stringify(this.simpleVariants));
                variantsToSubmit.forEach(v => {
                    v.prices.visitor = cleanPrice(v.prices.visitor);
                    v.prices.reseller = cleanPrice(v.prices.reseller);
                    v.prices.vip = cleanPrice(v.prices.vip);
                    v.prices.vvip = cleanPrice(v.prices.vvip);
                });
            } else {
                variantsToSubmit = JSON.parse(JSON.stringify(this.nestedVariants));
                variantsToSubmit.forEach(cat => {
                    cat.items.forEach(v => {
                        v.prices.visitor = cleanPrice(v.prices.visitor);
                        v.prices.reseller = cleanPrice(v.prices.reseller);
                        v.prices.vip = cleanPrice(v.prices.vip);
                        v.prices.vvip = cleanPrice(v.prices.vvip);
                    });
                });
            }

            // Manually set hidden input values to ensure they're submitted
            const form = event.target;
            form.querySelector('input[name="variant_mode"]').value = this.variantMode;
            form.querySelector('input[name="variants_data"]').value = JSON.stringify(variantsToSubmit);
            form.querySelector('input[name="input_fields"]').value = JSON.stringify(this.formatInputs);
            form.querySelector('input[name="notes"]').value = JSON.stringify(this.customNotes);
        }
    }
}).mount('#productApp');
</script>
@endpush
