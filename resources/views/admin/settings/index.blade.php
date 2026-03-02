@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('page-title', 'Konfigurasi Website')
@section('page-description', 'Atur semua parameter bisnis Marspedia disini.')

@section('content')
<div class="space-y-8">
    <!-- Tabs -->
    <div class="bg-white rounded-2xl p-2 shadow-sm border border-slate-200 flex flex-wrap gap-2">
        <a href="{{ route('admin.settings.index', ['tab' => 'general']) }}"
           class="px-6 py-3 rounded-xl font-bold text-sm transition-all {{ $tab === 'general' ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="ri-settings-3-line mr-2"></i>Umum
        </a>
        <a href="{{ route('admin.settings.index', ['tab' => 'social']) }}"
           class="px-6 py-3 rounded-xl font-bold text-sm transition-all {{ $tab === 'social' ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="ri-share-line mr-2"></i>Social Media
        </a>
        <a href="{{ route('admin.settings.index', ['tab' => 'profit']) }}"
           class="px-6 py-3 rounded-xl font-bold text-sm transition-all {{ $tab === 'profit' ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="ri-percent-line mr-2"></i>Profit & Biaya
        </a>
        <a href="{{ route('admin.settings.index', ['tab' => 'providers']) }}"
           class="px-6 py-3 rounded-xl font-bold text-sm transition-all {{ $tab === 'providers' ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="ri-plug-line mr-2"></i>Provider API
        </a>
        <a href="{{ route('admin.settings.index', ['tab' => 'payment']) }}"
           class="px-6 py-3 rounded-xl font-bold text-sm transition-all {{ $tab === 'payment' ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="ri-bank-card-line mr-2"></i>Payment Gateway
        </a>
    </div>

    <!-- General Settings Tab -->
    @if($tab === 'general')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8">
            <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="bg-gradient-to-r from-brand-600 to-cyan-500 p-5 flex items-center gap-3 text-white">
                    <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center">
                        <i class="ri-layout-masonry-fill text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Identitas Umum</h3>
                        <p class="text-xs text-cyan-100 font-medium">Judul, Deskripsi & Kontak Utama</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="general">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Judul Website</label>
                            <div class="flex items-center px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 focus-within:bg-white transition">
                                <i class="ri-t-box-line text-slate-400 mr-3"></i>
                                <input type="text" name="site_title" value="{{ old('site_title', $generalSettings['site_title']) }}"
                                       class="bg-transparent w-full outline-none font-bold text-slate-700 text-sm" placeholder="Marspedia Store">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">No. WhatsApp Utama</label>
                            <div class="flex items-center px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 focus-within:bg-white transition">
                                <i class="ri-whatsapp-fill text-green-500 mr-3"></i>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp', $generalSettings['whatsapp']) }}"
                                       class="bg-transparent w-full outline-none font-bold text-slate-700 text-sm" placeholder="62812xxx">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Deskripsi SEO</label>
                        <div class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 focus-within:bg-white transition">
                            <textarea name="site_description" rows="3"
                                      class="bg-transparent w-full outline-none font-bold text-slate-700 text-sm resize-none"
                                      placeholder="Deskripsi singkat website...">{{ old('site_description', $generalSettings['site_description']) }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Keywords</label>
                        <div class="flex items-center px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 focus-within:bg-white transition">
                            <i class="ri-hashtag text-slate-400 mr-3"></i>
                            <input type="text" name="site_keywords" value="{{ old('site_keywords', $generalSettings['site_keywords']) }}"
                                   class="bg-transparent w-full outline-none font-bold text-slate-700 text-sm" placeholder="topup, game, voucher...">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Footer Text</label>
                        <div class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 focus-within:bg-white transition">
                            <textarea name="footer_text" rows="3"
                                      class="bg-transparent w-full outline-none font-bold text-slate-700 text-sm resize-none"
                                      placeholder="© 2026 Marspedia&#10;All Rights Reserved">{{ old('footer_text', $generalSettings['footer_text']) }}</textarea>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all">
                            <i class="ri-save-line mr-2"></i>Simpan Pengaturan Umum
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden h-full">
                <div class="bg-gradient-to-r from-violet-600 to-purple-600 p-5 flex items-center gap-3 text-white">
                    <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center">
                        <i class="ri-image-2-fill text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Logo Website</h3>
                        <p class="text-xs text-purple-100 font-medium">Tampilan Visual Brand</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 flex flex-col items-center justify-center">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="general">
                    <input type="hidden" name="site_title" value="{{ $generalSettings['site_title'] }}">

                    <div class="relative w-48 h-48 rounded-[2rem] bg-slate-50 border-4 border-dashed border-slate-200 hover:border-purple-500 hover:bg-purple-50 transition group cursor-pointer flex items-center justify-center overflow-hidden"
                         onclick="document.getElementById('logo-input').click()">
                        @if($generalSettings['logo'])
                            <img src="{{ asset('storage/' . $generalSettings['logo']) }}" alt="Logo" class="w-full h-full object-contain p-4 group-hover:scale-110 transition duration-500">
                        @else
                            <div class="text-center p-4">
                                <i class="ri-upload-cloud-fill text-4xl text-slate-300 group-hover:text-purple-500 transition mb-2 block"></i>
                                <span class="text-xs font-bold text-slate-400 group-hover:text-purple-600">Klik Upload</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition backdrop-blur-sm">
                            <span class="text-white font-bold text-xs bg-white/20 px-4 py-2 rounded-full border border-white/50">Ganti</span>
                        </div>
                    </div>
                    <input type="file" id="logo-input" name="logo" class="hidden" accept="image/*" onchange="this.form.submit()">
                    <p class="text-xs font-bold text-slate-400 mt-4">Max: 2MB (PNG/JPG)</p>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Social Media Tab -->
    @if($tab === 'social')
    <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-pink-600 to-rose-500 p-5 flex items-center gap-3 text-white justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center">
                    <i class="ri-share-forward-fill text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Social Media URL</h3>
                    <p class="text-xs text-pink-100 font-medium">Link Komunitas & Kontak</p>
                </div>
            </div>
            <i class="ri-links-line text-4xl text-white/20"></i>
        </div>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 md:p-8">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="social">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $socialFields = [
                        'whatsapp_url' => ['icon' => 'ri-whatsapp-fill text-green-500', 'label' => 'WhatsApp'],
                        'facebook' => ['icon' => 'ri-facebook-circle-fill text-blue-600', 'label' => 'Facebook'],
                        'instagram' => ['icon' => 'ri-instagram-fill text-pink-500', 'label' => 'Instagram'],
                        'tiktok' => ['icon' => 'ri-tiktok-fill text-slate-800', 'label' => 'TikTok'],
                        'telegram' => ['icon' => 'ri-telegram-fill text-blue-400', 'label' => 'Telegram'],
                        'youtube' => ['icon' => 'ri-youtube-fill text-red-600', 'label' => 'YouTube'],
                        'contact_email' => ['icon' => 'ri-mail-fill text-red-500', 'label' => 'Email'],
                    ];
                @endphp

                @foreach($socialFields as $field => $config)
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1.5 block">{{ $config['label'] }}</label>
                    <div class="flex items-center px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-pink-500 focus-within:bg-white transition">
                        <i class="{{ $config['icon'] }} text-xl mr-2"></i>
                        <input type="text" name="{{ $field }}" value="{{ old($field, $socialSettings[$field] ?? $socialSettings['email'] ?? '') }}"
                               class="bg-transparent w-full outline-none font-bold text-slate-700 text-xs" placeholder="Link {{ $config['label'] }}">
                    </div>
                </div>
                @endforeach
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-pink-600 to-rose-500 text-white rounded-xl font-bold shadow-lg shadow-pink-500/30 hover:shadow-pink-500/50 transition-all">
                    <i class="ri-save-line mr-2"></i>Simpan Social Media
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Profit & Fees Tab -->
    @if($tab === 'profit')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Profit Settings -->
        <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-emerald-500 p-5 flex items-center gap-3 text-white">
                <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center">
                    <i class="ri-percent-fill text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Profit Margin (%)</h3>
                    <p class="text-xs text-teal-100 font-medium">Persentase profit per level</p>
                </div>
            </div>
            <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="section" value="profit">

                @php
                    $roles = [
                        'visitor' => 'Visitor',
                        'reseller' => 'Reseller',
                        'reseller_vip' => 'Reseller VIP',
                        'reseller_vvip' => 'Reseller VVIP',
                    ];
                @endphp

                @foreach($roles as $key => $label)
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase w-32">{{ $label }}</span>
                    <div class="flex-1 flex items-center px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-teal-500 transition">
                        <input type="number" name="profit_{{ $key }}" value="{{ old('profit_' . $key, $profitSettings[$key]) }}"
                               class="bg-transparent w-full outline-none font-bold text-slate-800 text-right text-sm" placeholder="0" step="0.1">
                        <span class="text-slate-400 font-bold ml-2 text-xs">%</span>
                    </div>
                </div>
                @endforeach

                <div class="pt-4">
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-teal-600 to-emerald-500 text-white rounded-xl font-bold shadow-lg shadow-teal-500/30 transition-all">
                        <i class="ri-save-line mr-2"></i>Simpan Profit
                    </button>
                </div>
            </form>
        </div>

        <!-- Fees Settings -->
        <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="bg-gradient-to-r from-slate-700 to-slate-600 p-5 flex items-center justify-between text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center">
                        <i class="ri-coins-line text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Biaya Layanan</h3>
                        <p class="text-xs text-slate-300 font-medium">Admin & Layanan</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6" id="fees-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="section" value="fees">

                <div id="fees-container" class="space-y-3 mb-4">
                    @forelse($fees as $index => $fee)
                    <div class="flex items-center gap-3 fee-row">
                        <input type="text" name="fees[{{ $index }}][name]" value="{{ $fee->name }}"
                               class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 text-sm focus:border-brand-500 focus:bg-white transition outline-none"
                               placeholder="Nama Biaya">
                        <div class="flex items-center px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 transition w-32">
                            <span class="text-slate-400 font-bold text-xs mr-1">Rp</span>
                            <input type="number" name="fees[{{ $index }}][amount]" value="{{ $fee->amount }}"
                                   class="bg-transparent w-full outline-none font-bold text-slate-800 text-right text-sm" placeholder="0">
                        </div>
                        <button type="button" onclick="removeFee(this)" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition flex items-center justify-center">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                    @empty
                    <p class="text-center text-slate-400 text-sm py-4">Belum ada biaya</p>
                    @endforelse
                </div>

                <button type="button" onclick="addFee()" class="w-full py-2 border-2 border-dashed border-slate-200 rounded-xl text-slate-500 font-bold text-sm hover:border-brand-500 hover:text-brand-600 transition">
                    <i class="ri-add-line mr-1"></i>Tambah Biaya
                </button>

                <div class="pt-4">
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-slate-700 to-slate-600 text-white rounded-xl font-bold shadow-lg transition-all">
                        <i class="ri-save-line mr-2"></i>Simpan Biaya
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Providers Tab -->
    @if($tab === 'providers')
    <div>
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white flex items-center justify-center shadow-lg shadow-violet-500/30">
                <i class="ri-gamepad-line text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Provider Top Up</h2>
                <p class="text-sm text-slate-500">Kelola koneksi ke provider top up</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @include('admin.settings.partials.provider-card', [
                'provider' => $topupProviders->firstWhere('provider_name', 'apigames'),
                'name' => 'API Games',
                'icon' => 'ri-vip-crown-fill',
                'gradient' => 'from-amber-400 to-yellow-500',
                'fields' => [
                    ['name' => 'credentials[merchant_id]', 'label' => 'Merchant ID', 'type' => 'text'],
                    ['name' => 'credentials[secret_key]', 'label' => 'Secret Key', 'type' => 'password'],
                    ['name' => 'config[api_url]', 'label' => 'API URL', 'type' => 'text', 'default' => 'https://api.apigames.id'],
                ],
                'showSync' => true
            ])

            @include('admin.settings.partials.provider-card', [
                'provider' => $topupProviders->firstWhere('provider_name', 'digiflazz'),
                'name' => 'DigiFlazz',
                'icon' => 'ri-flashlight-fill',
                'gradient' => 'from-orange-400 to-amber-500',
                'fields' => [
                    ['name' => 'credentials[username]', 'label' => 'Username', 'type' => 'text'],
                    ['name' => 'credentials[api_key]', 'label' => 'API Key', 'type' => 'password'],
                    ['name' => 'credentials[webhook_secret]', 'label' => 'Webhook Secret', 'type' => 'password'],
                ],
                'showSync' => true
            ])

            @include('admin.settings.partials.provider-card', [
                'provider' => $topupProviders->firstWhere('provider_name', 'manual'),
                'name' => 'Manual',
                'icon' => 'ri-hand-coin-line',
                'gradient' => 'from-slate-400 to-slate-600',
                'fields' => [],
                'showInfo' => 'Provider manual memungkinkan Anda memproses pesanan secara manual dari admin panel.',
                'showSync' => false
            ])
        </div>
    </div>
    @endif

    <!-- Payment Tab -->
    @if($tab === 'payment')
    <div>
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30">
                <i class="ri-bank-card-line text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Payment Gateway</h2>
                <p class="text-sm text-slate-500">Kelola koneksi ke payment gateway</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @include('admin.settings.partials.provider-card', [
                'provider' => $paymentProviders->firstWhere('provider_name', 'ipaymu'),
                'name' => 'iPaymu',
                'icon' => 'ri-wallet-3-fill',
                'gradient' => 'from-blue-500 to-indigo-600',
                'fields' => [
                    ['name' => 'credentials[api_key]', 'label' => 'API Key', 'type' => 'password'],
                    ['name' => 'credentials[va_number]', 'label' => 'VA Number', 'type' => 'text'],
                ],
                'showSync' => false
            ])

            @include('admin.settings.partials.provider-card', [
                'provider' => $paymentProviders->firstWhere('provider_name', 'midtrans'),
                'name' => 'Midtrans',
                'icon' => 'ri-secure-payment-line',
                'gradient' => 'from-cyan-500 to-blue-600',
                'fields' => [
                    ['name' => 'credentials[server_key]', 'label' => 'Server Key', 'type' => 'password'],
                    ['name' => 'credentials[client_key]', 'label' => 'Client Key', 'type' => 'text'],
                ],
                'showEnvironment' => true,
                'showSync' => false
            ])
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
let feeIndex = {{ $fees->count() }};

function addFee() {
    const container = document.getElementById('fees-container');
    const emptyMsg = container.querySelector('p');
    if (emptyMsg) emptyMsg.remove();

    const row = document.createElement('div');
    row.className = 'flex items-center gap-3 fee-row';
    row.innerHTML = `
        <input type="text" name="fees[${feeIndex}][name]"
               class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 text-sm focus:border-brand-500 focus:bg-white transition outline-none"
               placeholder="Nama Biaya">
        <div class="flex items-center px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 transition w-32">
            <span class="text-slate-400 font-bold text-xs mr-1">Rp</span>
            <input type="number" name="fees[${feeIndex}][amount]"
                   class="bg-transparent w-full outline-none font-bold text-slate-800 text-right text-sm" placeholder="0">
        </div>
        <button type="button" onclick="removeFee(this)" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition flex items-center justify-center">
            <i class="ri-delete-bin-line"></i>
        </button>
    `;
    container.appendChild(row);
    feeIndex++;
}

function removeFee(btn) {
    btn.closest('.fee-row').remove();
}

function testConnection(providerId) {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i>';

    fetch(`/admin/settings/provider/${providerId}/test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? '✅ ' + data.message : '❌ ' + data.message);
    })
    .catch(error => {
        alert('❌ Connection test failed');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function syncProducts(provider) {
    if (!confirm(`Sync products from ${provider}?`)) return;

    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i>';

    fetch('/admin/settings/sync-products', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ provider })
    })
    .then(response => response.json())
    .then(data => {
        alert('✅ Product sync has been queued');
    })
    .catch(error => {
        alert('❌ Sync failed');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>
@endpush
@endsection
